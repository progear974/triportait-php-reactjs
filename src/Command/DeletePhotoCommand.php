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

#[AsCommand(
    name: 'app:delete-code',
    description: 'Add a short description for your command',
)]
class DeletePhotoCommand extends Command
{
    private ShootingRepository $shootingRepository;
    private TriportraitTreeService $triportraitTreeService;

    public function __construct(ShootingRepository $shootingRepository, TriportraitTreeService $triportraitTreeService)
    {
        parent::__construct();
        $this->shootingRepository = $shootingRepository;
        $this->triportraitTreeService = $triportraitTreeService;
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

        if (!file_exists($filename)) {
            $io->error("File $filename not found");
            return Command::FAILURE;
        }
        $file = file_get_contents($filename);
        $codes = explode("\n", $file);
        print_r($codes);
        foreach ($codes as $code) {
            try {
                $code = rtrim($code);
                if ($code == null)
                    continue;
                $io->info("CODE : ($code)");
                $shooting = $this->shootingRepository->findOneBy(["code" => $code]);
                if ($shooting == null) {
                    $io->info("{$code} not found in database");
                    continue;
                }
                $this->triportraitTreeService->deletePhotos($shooting);
                $io->success("{$code} has been correctly delete.");
            } catch (Exception $exception) {
                $io->error("{$code} hasn't been correctly delete.");
            }
        }
        return Command::SUCCESS;
    }
}
