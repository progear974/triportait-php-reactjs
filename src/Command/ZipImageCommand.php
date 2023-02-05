<?php

namespace App\Command;

use App\Repository\ShootingRepository;
use App\Services\TriportraitTreeService;
use App\Services\ZippingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use App\Entity\Shooting;


#[AsCommand(
    name: 'app:zip-image',
    description: 'Add a short description for your command',
)]
class ZipImageCommand extends Command
{
    private $shootingRepository;
    private $zippingService;
    private $appKernel;
    private $triportraitTreeService;

    public function __construct(ShootingRepository $shootingRepository, ZippingService $zippingService, KernelInterface $appKernel, TriportraitTreeService $triportraitTreeService)
    {
        $this->shootingRepository = $shootingRepository;
        $this->zippingService = $zippingService;
        $this->appKernel = $appKernel;
        $this->triportraitTreeService = $triportraitTreeService;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    private function checkFilesExists(Shooting $shooting): bool
    {
        $pathPrint = $this->triportraitTreeService->getSinglePathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename());
        if (!file_exists($pathPrint))
            return false;
        foreach ($shooting->getSingleFilenames() as $single) {
            if (!file_exists($this->triportraitTreeService->getSinglePathInDataFolder($shooting->getFolder(), $single)))
                return false;
        }
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $shootings = $this->shootingRepository->findBy(["zip" => false]);

        $process = Process::fromShellCommandline("mkdir -p {$this->triportraitTreeService->getPublicImagesFolderPath()}", timeout: null);
        $process->mustRun(null);
        $process = Process::fromShellCommandline("mkdir -p {$this->triportraitTreeService->getPublicZipFolderPath()}", timeout: null);
        $process->mustRun(null);

        foreach ($shootings as $shooting) {
            if (!$this->checkFilesExists($shooting)) {
                continue;
            }
            $process = Process::fromShellCommandline("cp {$this->triportraitTreeService->getPrintPathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename())} {$this->triportraitTreeService->getPublicImagesFolderPath()}", timeout: null);
            $process->mustRun(null);

            $singles_filenames = $this->triportraitTreeService->getSinglesPathInDataFolder($shooting->getFolder(), $shooting->getSingleFilenames());
            foreach ($singles_filenames as $single_path) {
                $process = Process::fromShellCommandline("cp {$single_path} {$this->triportraitTreeService->getPublicImagesFolderPath()}", timeout: null);
                $process->mustRun(null);
            }
            $this->zippingService->zipSession($shooting->getCode());
        }
        return Command::SUCCESS;
    }
}
