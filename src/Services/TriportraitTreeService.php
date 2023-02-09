<?php

namespace App\Services;

use App\Entity\Shooting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class TriportraitTreeService
{
    /** KernelInterface $appKernel */
    private $appKernel;

    private $triportraitTreeService;

    private $entityManager;

    public function __construct(KernelInterface $appKernel, TriportraitTreeService $triportraitTreeService, EntityManagerInterface $entityManager)
    {
        $this->appKernel = $appKernel;
        $this->triportraitTreeService = $triportraitTreeService;
        $this->entityManager = $entityManager;
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

    public function deletePhotos(Shooting $shooting) {
        $delete_paths = [];
        // delete print file in data and public folder
        $delete_paths[] = $this->triportraitTreeService->getPrintPathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename());
        $delete_paths[] = $this->triportraitTreeService->getImagePathInPublicFolder($shooting->getPrintFilename());
        // delete print file in data and public folder

        // delete singles files in data and public folder
        $delete_paths = array_merge($delete_paths, $this->triportraitTreeService->getSinglesPathInDataFolder($shooting->getFolder(), $shooting->getSingleFilenames()));
        $delete_paths = array_merge($delete_paths, $this->triportraitTreeService->getImagesPathInPublicFolder($shooting->getSingleFilenames()));
        // delete singles files in data and public folder

        // delete zip file in zip folder
        $delete_paths[] = $this->triportraitTreeService->getZipPathInPublicFolder($shooting->getCode() . ".zip");
        // delete zip file in zip folder


        $file_to_delete = implode(" ", $delete_paths);
        $process = Process::fromShellCommandline("rm -f {$file_to_delete}", timeout: null);
        $process->mustRun(null);
        $this->entityManager->remove($shooting);
        $this->entityManager->flush();
    }

}