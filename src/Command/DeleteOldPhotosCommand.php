<?php

namespace App\Command;

use App\Repository\ShootingRepository;
use Doctrine\ORM\EntityManager;
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

    public function __construct(ShootingRepository $shootingRepository, KernelInterface $appKernel, EntityManager $entityManager)
    {
        parent::__construct();
        $this->shootingRepository = $shootingRepository;
        $this->appKernel = $appKernel;
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $shootings = $this->shootingRepository->findByOlderThanDay("31");
        $dest_images = $this->appKernel->getProjectDir() . "/" . "public" . "/" . "images";
        $dest_zip = $this->appKernel->getProjectDir() . "/" . "public" . "/" . "zip";
        foreach ($shootings as $shooting) {
            print_r($shooting);
            $pathPrintFolderToCopy = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_PRINTS"];
            $pathSinglesFolderToCopy = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_SINGLES"];

            // delete print file in data and public folder
            $print_filename = $shooting->getPrintFilename();
            $process = Process::fromShellCommandline("rm ${pathPrintFolderToCopy}/${print_filename}", timeout: null);
            $process->mustRun(null);
            $process = Process::fromShellCommandline("rm ${dest_images}/${print_filename}", timeout: null);
            $process->mustRun(null);

            // delete singles files in data and public folder
            $singles_filename = $shooting->getSingleFilenames();
            foreach ($singles_filename as $single_filename) {
                $process = Process::fromShellCommandline("rm ${pathSinglesFolderToCopy}/${single_filename}", timeout: null);
                $process->mustRun(null);
                $process = Process::fromShellCommandline("rm ${dest_images}/${single_filename}", timeout: null);
                $process->mustRun(null);
            }
            $zip_filename = str_replace(".jpg", ".zip", $print_filename);
            $process = Process::fromShellCommandline("rm ${dest_zip}/${zip_filename}", timeout: null);
            $process->mustRun(null);
            $this->entityManager->remove($shooting);
            $this->entityManager->flush();
        }

        return Command::SUCCESS;
    }
}
