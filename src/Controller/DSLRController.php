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
use Symfony\Component\Routing\Annotation\Route;

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
            $arr = explode("\\", $last);
            $good_key = null;
            foreach ($arr as $key => $value) {
                if ($value == "dslrBooth") {
                    $good_key = $key + 1;
                    break;
                }
            }
            foreach ($paths as $key => $value)
            {
                $paths[$key] = basename($value);
            }
            $folder = $arr[$good_key];
            $shooting = new Shooting();
            $shooting->setSingleFilenames($paths);
            $shooting->setFolder($folder);
            $shooting->setCode(null);
            $shooting->setDate(new \DateTime());
            $shooting->setPrintFilename(array_pop($arr));
            $entityManager->persist($shooting);
            $entityManager->flush();
            $logger->error($last);
            $logger->error($folder);
        }
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DSLRController.php',
        ]);
    }
}
