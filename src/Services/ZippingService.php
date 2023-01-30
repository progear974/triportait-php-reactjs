<?php

namespace App\Services;

use App\Repository\ShootingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use ZipArchive;

class ZippingService
{
    /** KernelInterface $appKernel */
    private $appKernel;

    private ShootingRepository $shootingRepository;

    public function __construct(KernelInterface $appKernel, ShootingRepository $shootingRepository)
    {
        $this->appKernel = $appKernel;
        $this->shootingRepository = $shootingRepository;
    }

    public function zipSession($code, EntityManagerInterface $entityManager)
    {
        if ($code == null)
            return;
        $shooting = $this->shootingRepository->findOneBy(["code" => $code, "zip" => false]);
        if (!$shooting)
            return;
        $arr_url = [];
        $arr_url[] = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_PRINTS"] . "/" . $shooting->getPrintFilename();
        foreach ($shooting->getSingleFilenames() as $filename) {
            $arr_url[] = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_SINGLES"] . "/" . $filename;
        }

        $zip = new ZipArchive();
        $zip->open($this->appKernel->getProjectDir() . "/" . "public" . "/" . "zip" . "/" . $code . ".zip",  ZipArchive::CREATE);
        foreach ($arr_url as $url) {
            $zip->addFile("{$url}", basename($url));
        }
        $zip->close();
        $shooting->setZip(true);
        $entityManager->persist($shooting);
        $entityManager->flush();
    }

}