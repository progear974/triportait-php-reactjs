<?php

namespace App\Command;

use App\Entity\Shooting;
use App\Repository\ShootingRepository;
use App\Services\TriportraitTreeService;
use Doctrine\ORM\EntityManagerInterface;
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
    name: 'app:add-shooting',
    description: 'Add a short description for your command',
)]
class AddShootingCommand extends Command
{

    private $entityManager;
    private $appKernel;

    public function __construct(EntityManagerInterface $entityManager, KernelInterface $appKernel)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
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
        $filename = $input->getArgument('filename') != null ? $input->getArgument('filename') : "add.txt";
        $pathFile = "{$this->appKernel->getProjectDir()}/var/files/$filename";

        if (!file_exists($pathFile)) {
            $io->error("File $filename not found");
            return Command::FAILURE;
        }
        $file = file_get_contents($pathFile);
        $infos = explode("\n", $file);
        $result = [];
        foreach ($infos as $info) {
            try {
                $info = rtrim($info);
                if ($info == null)
                    continue;
                $infos_shooting = explode(' ', $info);
                $shooting = new Shooting();
                $shooting->setCode($infos_shooting[0]);
                $shooting->setDate(new \DateTime($infos_shooting[1]));
                $shooting->setFolder($infos_shooting[2]);
                $shooting->setPrintFilename($infos_shooting[3]);
                $path_singles = [];
                for ($i = 4; $i < sizeof($infos_shooting); $i++)
                    $path_singles[] = $infos_shooting[$i];
                $shooting->setSingleFilenames($path_singles);
                $shooting->setZip(false);
                $this->entityManager->persist($shooting);
                $this->entityManager->flush();
            } catch (Exception $exception) {
                $io->error("ERROR");
            }
        }
        return Command::SUCCESS;
    }
}
