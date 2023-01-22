<?php

namespace App\Controller;

use App\Entity\Shooting;
use App\Repository\ShootingRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\HeaderUtils;

#[Route('/api', name: 'api')]
class DSLRController extends AbstractController
{
    /*
     * Objectif :   récuperer la requete processing_start pour extraire les noms et chemins des photos
     *              afin de les enregistrer en base de données.
     * */
    #[Route('/', name: 'app_d_s_l_r')]
    public function index(Request $request, LoggerInterface $logger, EntityManagerInterface $entityManager): JsonResponse
    {
        $event_type = $request->get("event_type");
        if ($event_type == "processing_start") {
            $paths = [];
            $id = 1;
            while (true) {
                $path = $request->get("param".$id);
                if ($path != null)
                    $paths[] = $path;
                else
                    break;
                $id += 1;
            }
            $last = utf8_decode(array_pop($paths));
            $logger->alert($last);
            $arr = explode("\\", $last);
            $good_key = null;
            foreach ($arr as $key => $value) {
                if ($value == "dslrBooth") {
                    $good_key = $key + 1;
                    break;
                }
            }

            $folder = $arr[$good_key];
            $shooting = new Shooting();
            $shooting->setSingleFilenames($paths);
            $shooting->setFolder($folder);

            $shooting->setDate(new \DateTime());

            $printFilename = array_pop($arr);
            $printFilenameWithoutExtension = substr($printFilename, 0, strrpos($printFilename, "."));
            $arrPrintFilename = explode("_", $printFilenameWithoutExtension);
            $code = $arrPrintFilename[1] . $arrPrintFilename[2];

            $shooting->setPrintFilename($printFilename);
            $shooting->setCode($code);
            $shooting->setZip(false);
            $entityManager->persist($shooting);
            $entityManager->flush();
        }
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DSLRController.php',
        ]);
    }

    #[Route('/download/{filename}', name: 'download_filename')]
    public function download(KernelInterface $appKernel, $filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $zip_folder_path = $appKernel->getProjectDir() . "/" . "public" . "/" . $_ENV["FOLDER_ZIP"];
        $singles_folder_path = $appKernel->getProjectDir() . "/" . "public" . "/" . "images";

        if ($extension == "zip") {
            $response = new BinaryFileResponse($zip_folder_path . "/" . $filename);
        } else {
            $response = new BinaryFileResponse($singles_folder_path . "/" . $filename);
        }

        $response->setContentDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename);
        return $response;
    }

    #[Route('/photos/{code}', name: 'link_photo')]
    public function link_photo(ShootingRepository $shootingRepository, $code)
    {
        $shooting = $shootingRepository->findOneBy([
            "code" => $code
        ]);
        $arr_url = [];
        $arr_url[] = $_ENV["URL_PUBLIC"] . "/" . "images" . "/" .$_ENV["FOLDER_PRINTS"] . "/" . $shooting->getPrintFilename();
        foreach ($shooting->getSingleFilenames() as $filename) {
            $arr_url[] = $_ENV["URL_PUBLIC"] . "/" . "images" . "/" .$_ENV["FOLDER_SINGLES"] . "/" . $filename;
        }
        return $this->json(["code" => $code, "urls" => $arr_url, "date" => $shooting->getDate()]);
    }

    #[Route('/shooting', name: 'shooting')]
    public function shooting(ShootingRepository $shootingRepository): JsonResponse
    {
        $shootings = $shootingRepository->findAll();
        $arr = [];
        foreach ($shootings as $shooting) {
            $arr[] = [
                "id" => $shooting->getId(),
                "folder" => $shooting->getFolder(),
                "code" => $shooting->getPrintFilename(),
                "date" => $shooting->getDate()
            ];
        }
        return $this->json($arr);
    }
}
