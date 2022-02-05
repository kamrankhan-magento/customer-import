<?php

/**
 * @author Kamran Khan
 * @email kamran.unitedsol@gmail.com
 */


namespace WireIt\CustomerImport\ImportService;

interface ImportInterface
{
    /**
     * @param string $source
     * @return mixed
     */
    public function csvImport(string $source);

    /**
     * @param string $source
     * @return mixed
     */
    public function jsonImport(string $source);
}
