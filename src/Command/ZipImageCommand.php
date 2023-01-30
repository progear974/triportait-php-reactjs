<?php

namespace App\Command;

use App\Repository\ShootingRepository;
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

    public function __construct(ShootingRepository $shootingRepository, ZippingService $zippingService, KernelInterface $appKernel)
    {
        $this->shootingRepository = $shootingRepository;
        $this->zippingService = $zippingService;
        $this->appKernel = $appKernel;
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    private function checkFilesExists(Shooting $shooting): bool
    {
        $pathPrint = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_PRINTS"] . "/" . $shooting->getPrintFilename();
        if (!file_exists($pathPrint))
            return false;
        $basePathSingles = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_SINGLES"] . "/";
        foreach ($shooting->getSingleFilenames() as $single) {
            $path = $basePathSingles . $single;
            if (!file_exists($path))
                return false;
        }
        return true;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $shootings = $this->shootingRepository->findBy(["zip" => false]);

        $dest = $this->appKernel->getProjectDir() . "/" . "public" . "/" . "images";
        $destZip = $this->appKernel->getProjectDir() . "/" . "public" . "/" ."zip";
        $process = Process::fromShellCommandline("mkdir -p {$dest}", timeout: null);
        $process->mustRun(null);
        $process = Process::fromShellCommandline("mkdir -p {$destZip}", timeout: null);
        $process->mustRun(null);

        foreach ($shootings as $shooting) {
            if (!$this->checkFilesExists($shooting)) {
                continue;
            }
            $pathPrintFolderToCopy = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_PRINTS"];
            $pathSinglesFolderToCopy = $_ENV["DATA_ROOT_FOLDER"] . "/" . $shooting->getFolder() . "/" . $_ENV["FOLDER_SINGLES"];

            $print_filename = $shooting->getPrintFilename();
            $process = Process::fromShellCommandline("cp ${pathPrintFolderToCopy}/${print_filename} ${dest}", timeout: null);
            $process->mustRun(null);

            $singles_filenames = $shooting->getSingleFilenames();
            foreach ($singles_filenames as $singles_filename) {
                print_r("${pathSinglesFolderToCopy}/${singles_filename}");
                echo "\n\n";
                $process = Process::fromShellCommandline("cp ${pathSinglesFolderToCopy}/${singles_filename} ${dest}", timeout: null);
                $process->mustRun(null);
            }
            $this->zippingService->zipSession($shooting->getCode());
        }
        return Command::SUCCESS;
    }
}
