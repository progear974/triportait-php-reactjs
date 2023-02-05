<?php

namespace App\Services;

use Symfony\Component\HttpKernel\KernelInterface;

class TriportraitTreeService
{
    /** KernelInterface $appKernel */
    private $appKernel;

    public function __construct(KernelInterface $appKernel)
    {
        $this->appKernel = $appKernel;
    }

    public function getPublicImagesFolderPath()
    {
        return "{$this->appKernel->getProjectDir()}/public/images";
    }

    public function getPublicZipFolderPath()
    {
        return "{$this->appKernel->getProjectDir()}/public/zip";
    }

    public function getPrintPathInPublicFolder($printFilename) : string
    {
        return "{$this->getPublicImagesFolderPath()}/Prints/{$printFilename}";
    }

    public function getSinglePathInPublicFolder($singleFilename) : string
    {
        return "{$this->getPublicImagesFolderPath()}/Singles/{$singleFilename}";
    }

    public function getSinglesPathInPublicFolder($singlesFilename) : array
    {
        $arr = [];
        foreach ($singlesFilename as $singleFilename) {
            $arr[] = $this->getSinglePathInPublicFolder($singleFilename);
        }
        return $arr;
    }

    public function getPrintPathInDataFolder($folder, $print_filename) : string
    {
        return "{$_ENV["DATA_ROOT_FOLDER"]}/{$folder}/{$_ENV["FOLDER_PRINTS"]}/{$print_filename}";
    }

    public function getSinglePathInDataFolder($folder, $single_filename) : string
    {
        return "{$_ENV["DATA_ROOT_FOLDER"]}/{$folder}/{$_ENV["FOLDER_SINGLES"]}/{$single_filename}";
    }

    public function getSinglesPathInDataFolder($folder, $singlesFilename) : array
    {
        $arr = [];
        foreach ($singlesFilename as $singleFilename) {
            $arr[] = $this->getSinglePathInDataFolder($folder, $singleFilename);
        }
        return $arr;
    }

}