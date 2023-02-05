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

    public function getURLImagePathInPublicFolder($filename)
    {
        return "{$_ENV["URL_PUBLIC"]}/images/{$filename}";
    }

    public function getPublicImagesFolderPath()
    {
        return "{$this->appKernel->getProjectDir()}/public/images";
    }

    public function getPublicZipFolderPath()
    {
        return "{$this->appKernel->getProjectDir()}/public/zip";
    }

    public function getZipPathInPublicFolder($filename)
    {
        return "{$this->getPublicZipFolderPath()}/{$filename}";
    }

    public function getImagePathInPublicFolder($filename) : string
    {
        return "{$this->getPublicImagesFolderPath()}/{$filename}";
    }

    public function getImagesPathInPublicFolder($filenames) : array
    {
        $arr = [];
        foreach ($filenames as $filename) {
            $arr[] = $this->getImagePathInPublicFolder($filename);
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