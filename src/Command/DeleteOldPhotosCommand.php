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
            $this->triportraitTreeService->deletePhotos($shooting);
        }
        return Command::SUCCESS;
    }
}
