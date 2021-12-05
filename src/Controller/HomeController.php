<?php

namespace App\Controller;

use App\Repository\CarouselRepository;
use App\Repository\DishRepository;
// use App\Service\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CarouselRepository $cr, DishRepository $dr): Response
    {
        // for ($query = 1; $query < 7; $query++) {
        //     $qrs->qrcode($query, $baseUrl);
        // }
        $carousels = $cr->findBy(['status' => 1]);
        // \dd($carousels);
        $hotSells = $dr->getHotSells();
        return $this->render('home/index.html.twig', [
            'carousels' => $carousels,
            'hotSells' => $hotSells
        ]);
    }
}
