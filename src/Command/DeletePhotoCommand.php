<?php

namespace App\Command;

use App\Repository\ShootingRepository;
use App\Services\TriportraitTreeService;
use Symfony\Component\Config\Definition\Exception\Exception;
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
    name: 'app:delete-code',
    description: 'Add a short description for your command',
)]
class DeletePhotoCommand extends Command
{
    private ShootingRepository $shootingRepository;
    private TriportraitTreeService $triportraitTreeService;
    private KernelInterface $appKernel;

    public function __construct(ShootingRepository $shootingRepository, TriportraitTreeService $triportraitTreeService, KernelInterface $appKernel)
    {
        parent::__construct();
        $this->shootingRepository = $shootingRepository;
        $this->triportraitTreeService = $triportraitTreeService;
        $this->appKernel = $appKernel;
    }

    protected function configure(): void
    {
        $this->addArgument('filename', InputArgument::OPTIONAL, 'File that contain codes to delete')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename') != null ? $input->getArgument('filename') : "delete.txt";
        $pathFile = "{$this->appKernel->getProjectDir()}/var/files/$filename";

        if (!file_exists($pathFile)) {
            $io->error("File $filename not found");
            return Command::FAILURE;
        }
        $file = file_get_contents($pathFile);
        $codes = explode("\n", $file);
        $result = [];
        foreach ($codes as $code) {
            try {
                $code = rtrim($code);
                if ($code == null)
                    continue;
                $shooting = $this->shootingRepository->findOneBy(["code" => $code]);
                if ($shooting == null) {
                    $result[] = "[NOT FOUND] Le code $code n'existe pas en base de données.";
                    $io->info("{$code} not found in database");
                    continue;
                }
                $this->triportraitTreeService->deletePhotos($shooting);
                $result[] = "[OK] Les photos liées au code $code ont bien été supprimés.";
                $io->success("{$code} has been correctly delete.");
            } catch (Exception $exception) {
                $result[] = "[ERROR] Les photos liées au code $code n'ont pas pu être supprimés.";
                $io->error("{$code} hasn't been correctly delete.");
            }
        }
        file_put_contents("{$this->appKernel->getProjectDir()}/var/files/result.txt", $result);
        return Command::SUCCESS;
    }
}
