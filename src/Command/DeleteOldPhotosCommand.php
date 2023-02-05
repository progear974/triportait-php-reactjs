<?php

namespace App\Command;

use App\Repository\ShootingRepository;
use App\Services\TriportraitTreeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:delete-old-photos',
    description: 'Add a short description for your command',
)]
class DeleteOldPhotosCommand extends Command
{
    private $shootingRepository;
    private $appKernel;
    private $entityManager;
    private $triportraitTreeService;

    public function __construct(ShootingRepository $shootingRepository, KernelInterface $appKernel, EntityManagerInterface $entityManager, TriportraitTreeService $triportraitTreeService)
    {
        parent::__construct();
        $this->shootingRepository = $shootingRepository;
        $this->appKernel = $appKernel;
        $this->entityManager = $entityManager;
        $this->triportraitTreeService = $triportraitTreeService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shootings = $this->shootingRepository->findByOlderThanDay("31");
        foreach ($shootings as $shooting) {
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
            $delete_paths[] = $this->triportraitTreeService->getZipPathInPublicFolder(str_replace(".jpg", ".zip", $shooting->getPrintFilename()));
            // delete zip file in zip folder


            $file_to_delete = implode(" ", $delete_paths);
            print_r($delete_paths);
            $process = Process::fromShellCommandline("rm -f {$file_to_delete}", timeout: null);
            $process->mustRun(null);
            $this->entityManager->remove($shooting);
            $this->entityManager->flush();
        }
        return Command::SUCCESS;
    }
}
