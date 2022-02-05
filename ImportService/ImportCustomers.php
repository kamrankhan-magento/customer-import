<?php

/**
 * @author Kamran Khan
 * @email kamran.unitedsol@gmail.com
 */
declare(strict_types=1);

namespace WireIt\CustomerImport\ImportService;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Store\Model\StoreManagerInterface;

class ImportCustomers implements ImportInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $store;
    /**
     * @var CustomerInterfaceFactory
     */
    private $customerFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        StoreManagerInterface       $store,
        CustomerInterfaceFactory    $customerFactory,
        CustomerRepositoryInterface $customerRepository)
    {
        $this->store = $store;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param string $source
     * @return mixed|void
     */
    public function csvImport(string $source)
    {
        try {
            $row = 0;
            if (($handle = fopen($source, "r")) !== false) {
                while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                    $row++;
                    if ($row > 0) {
                        $this->saveCustomerData($data);
                    }
                }
                fclose($handle);
            }
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * @param string $source
     * @return mixed|void
     */
    public function jsonImport(string $source)
    {
        $fileData = file_get_contents($source);
        if ($fileData !== false) {
            $customers = json_decode($fileData, true);
            foreach ($customers as $customer) {
                $customer = array_values($customer);
                $this->saveCustomerData($customer);
            }
        }
    }

    /**
     * @param $data
     * @return void
     */
    public function saveCustomerData($data): void
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        try {
            $customer = $this->customerFactory->create();
            $customer->setStoreId($this->store->getStore()->getId());
            $customer->setWebsiteId($this->store->getWebsite()->getId());
            $customer->setFirstname($data[0]);
            $customer->setLastname($data[1]);
            $customer->setEmail($data[2]);
            $this->customerRepository->save($customer);
        } catch (InputException|InputMismatchException|LocalizedException $e) {
            $e->getMessage();
        }
    }
}
