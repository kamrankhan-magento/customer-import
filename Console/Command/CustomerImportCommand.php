<?php
/**
 * @author Kamran Khan
 * @email kamran.unitedsol@gmail.com
 */
declare(strict_types=1);

namespace WireIt\CustomerImport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Console\Cli;
use WireIt\CustomerImport\ImportService\ImportCustomers;

/**
 * CustomerImportCommand imports customer profiles
 */
class CustomerImportCommand extends Command
{
    private const COMMAND = 'customer:import';
    private const PROFILE_NAME = 'profile-name';
    private const SOURCE = 'source';
    private const DESCRIPTION = 'Imports customer profiles from csv or json file';

    /**
     * @var ImportCustomers
     */
    private $importCustomers;


    /**
     * @param ImportCustomers $importCustomers
     */
    public function __construct(
        ImportCustomers $importCustomers
    )
    {
        $this->importCustomers = $importCustomers;
        parent::__construct();
    }

    /**
     * @inheridoc
     */
    protected function configure()
    {
        $this->setName(self::COMMAND)
            ->setDescription(self::DESCRIPTION)
            ->addArgument(self::PROFILE_NAME, InputArgument::REQUIRED, 'specify sample-csv or sample-json')
            ->addArgument(self::SOURCE, InputArgument::REQUIRED, 'file name');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return null|int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $profileName = $input->getArgument(self::PROFILE_NAME);
        $source = $input->getArgument(self::SOURCE);

        try {
            if ($profileName === 'sample-csv') {
                $this->importCustomers->csvImport($source);
            } elseif ($profileName === 'sample-json') {
                $this->importCustomers->jsonImport($source);
            } else {
                $output->writeln('<comment>Choose sample-csv or sample-json</comment>');
                return Cli::RETURN_FAILURE;
            }
        } catch (\Exception $e) {
            $output->writeln('<error>Customers profile import failed</error>');
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Customers Profile Imported.</info>');
        return Cli::RETURN_SUCCESS;
    }
}
