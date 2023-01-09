<?php

namespace App\Command;

use App\Repository\ShootingRepository;
use App\Services\OCR;
use App\Services\UpdateNotPresentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:code-analyse',
    description: 'Add a short description for your command',
)]
class CodeAnalyseCommand extends Command
{
    private $shootingRepository;
    private $ocr;
    private $entityManager;

    public function __construct(ShootingRepository $shootingRepository, OCR $ocr, EntityManagerInterface $entityManager)
    {
        $this->shootingRepository = $shootingRepository;
        $this->ocr = $ocr;
        $this->entityManager = $entityManager;
        parent::__construct();

    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        $shootings = $this->shootingRepository->findBy(["code" => null]);
        foreach ($shootings as $shooting) {
            $folder = $shooting->getFolder();
            $root_folder = $_ENV['DATA_ROOT_FOLDER'];
            $folder_print = $_ENV['FOLDER_PRINTS'];
            $folder_event = $shooting->getFolder();
            $filename_print = $shooting->getPrintFilename();
            $path_print = $root_folder . '/' . $folder_event . '/' . $folder_print . '/' . $filename_print;
            $this->ocr->cutImage($path_print);
            $code = $this->ocr->readCode(dirname(__DIR__, 2)."/test.jpg");
            $shooting->setCode($code);
            $this->entityManager->persist($shooting);
        }
        $this->entityManager->flush();
        return Command::SUCCESS;
    }
}
