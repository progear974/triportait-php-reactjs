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

    private EntityManagerInterface $entityManager;

    private TriportraitTreeService $triportraitTreeService;

    public function __construct(KernelInterface $appKernel, ShootingRepository $shootingRepository,  EntityManagerInterface $entityManager, TriportraitTreeService $triportraitTreeService)
    {
        $this->appKernel = $appKernel;
        $this->shootingRepository = $shootingRepository;
        $this->entityManager = $entityManager;
        $this->triportraitTreeService = $triportraitTreeService;
    }

    public function zipSession($code)
    {
        if ($code == null)
            return;
        $shooting = $this->shootingRepository->findOneBy(["code" => $code, "zip" => false]);
        if (!$shooting)
            return;
        $arr_url = [];
        $arr_url[] = $this->triportraitTreeService->getPrintPathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename());
        foreach ($shooting->getSingleFilenames() as $filename) {
            $arr_url[] = $this->triportraitTreeService->getSinglePathInDataFolder($shooting->getFolder(), $filename);
        }
        $zip = new ZipArchive();
        $zip->open($this->triportraitTreeService->getPublicZipFolderPath() . "/" . $code . ".zip",  ZipArchive::CREATE);
        foreach ($arr_url as $url) {
            $zip->addFile("{$url}", basename($url));
        }
        $zip->close();
        $shooting->setZip(true);
        $this->entityManager->persist($shooting);
        $this->entityManager->flush();
    }

}