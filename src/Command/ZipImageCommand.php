<?php

namespace App\Command;

use App\Repository\ShootingRepository;
use App\Services\TriportraitTreeService;
use App\Services\ZippingService;
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
        $pathPrint = $this->triportraitTreeService->getPrintPathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename());
        if (!file_exists($pathPrint))
            return false;
        foreach ($shooting->getSingleFilenames() as $single) {
            if (!file_exists($this->triportraitTreeService->getSinglePathInDataFolder($shooting->getFolder(), $single)))
                return false;
        }
        return true;
    }

    private function deleteFilesShooting(Shooting $shooting)
    {
        $delete_paths = [];
        // delete print file in public folder
        $delete_paths[] = $this->triportraitTreeService->getImagePathInPublicFolder($shooting->getPrintFilename());
        // delete print file in public folder

        // delete singles files in public folder
        $delete_paths = array_merge($delete_paths, $this->triportraitTreeService->getImagesPathInPublicFolder($shooting->getSingleFilenames()));
        // delete singles files in public folder

        // delete zip file in zip folder
        $delete_paths[] = $this->triportraitTreeService->getZipPathInPublicFolder(str_replace(".jpg", ".zip", $shooting->getPrintFilename()));
        // delete zip file in zip folder

        $file_to_delete = implode(" ", $delete_paths);
        $process = Process::fromShellCommandline("rm -f {$file_to_delete}", timeout: null);
        $process->mustRun(null);
    }

    private function resizeSingle($pathImage, $new_width=1748, $new_height=2402)
    {
        $img = imagecreatefromjpeg($pathImage);
        if (!$img)
            return;
        $width_img = imagesx($img);
        $height_img = imagesy($img);
        $cropped_img = imagecrop($img, ['x' => ($width_img / 2) - ($new_width / 2), 'y' => ($height_img / 2) - ($new_height / 2), 'width' => $new_width, 'height' => $new_height]);
        if ($cropped_img)
            imagejpeg($cropped_img, $pathImage);
        imagedestroy($img);
        imagedestroy($cropped_img);
    }

    public function changeCameraNamed(Shooting $shooting)
    {
        $pathPrint = $this->triportraitTreeService->getPrintPathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename());
        if (!file_exists($pathPrint)) {
            $newPathName = substr($pathPrint, 0, strlen($pathPrint) - strlen(strrchr($pathPrint, '.'))) . "_3600." . strrchr($pathPrint, '.');
            if (file_exists($newPathName)) {
                $shooting->setPrintFilename(basename($newPathName));
                $this->shootingRepository->save($shooting);
            }
        }
        $arr = [];
        foreach ($shooting->getSingleFilenames() as $single) {
            $pathSingle = $this->triportraitTreeService->getSinglePathInDataFolder($shooting->getFolder(), $single);
            if (!file_exists($pathSingle)) {
                $newPathName = substr($pathSingle, 0, strlen($pathSingle) - strlen(strrchr($pathSingle, '.'))) . "_3600." . strrchr($pathSingle, '.');
                if (file_exists($newPathName)) {
                    print_r("New pathname : " . $newPathName . "\n");
                    $arr[] = basename($newPathName);
                    $this->shootingRepository->save($shooting);
                } else
                    $arr[] = $single;
            } else
                $arr[] = $single;
        }
        $shooting->setSingleFilenames($arr);
        $this->shootingRepository->save($shooting, true);
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
            try {
                $this->changeCameraNamed($shooting);
                if (!$this->checkFilesExists($shooting)) {
                    print_r("Un des fichiers de {$shooting->getCode()} n'est pas upload !");
                    continue;
                }
                $process = Process::fromShellCommandline("cp \"{$this->triportraitTreeService->getPrintPathInDataFolder($shooting->getFolder(), $shooting->getPrintFilename())}\" \"{$this->triportraitTreeService->getPublicImagesFolderPath()}\"", timeout: null);
                $process->mustRun(null);
                $singles_filenames = $this->triportraitTreeService->getSinglesPathInDataFolder($shooting->getFolder(), $shooting->getSingleFilenames());
                $singles_to_copy = implode(" ", array_map(fn($filename) : string => "\"" . $filename . "\"" ,$singles_filenames));
                $process = Process::fromShellCommandline("cp {$singles_to_copy} {$this->triportraitTreeService->getPublicImagesFolderPath()}", timeout: null);
                $process->mustRun(null);
                $files_to_crop = array_map(fn($filename): string => $this->triportraitTreeService->getPublicImagesFolderPath()."/".basename($filename), $singles_filenames);
                array_map(array($this, 'resizeSingle'), $files_to_crop, array_fill(0, sizeof($singles_filenames), 1748), array_fill(0, sizeof($singles_filenames), 2402));
                $this->zippingService->zipSession($shooting->getCode());
            } catch (Exception $exception) {
                $this->deleteFilesShooting($shooting);
                $shooting->setZip(false);
                $this->shootingRepository->save($shooting, true);
            }
        }
        return Command::SUCCESS;
    }
}
