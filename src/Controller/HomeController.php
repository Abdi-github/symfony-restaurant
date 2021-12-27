<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Repository\CarouselRepository;
use App\Repository\DishRepository;
use App\Service\QrCodeService;
// use App\Service\QrCodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request, CarouselRepository $cr, DishRepository $dr, QrCodeService $qrs): Response
    {
        // for ($table = 1; $table < 7; $table++) {
        //     $qrs->qrcode($table);
        // }


        $session = $this->requestStack->getSession();
        // \dd($request->attributes->get('table'));


        if ($request->query) {
            $table_no = $request->query->get('table');
            $session->set('table', $table_no);
            // $session->invalidate();
        }

        $table_no = $session->get('table');














        $carousels = $cr->findBy(['status' => 1]);
        // \dd($carousels);
        $hotSells = $dr->getHotSells();

        // if ($session->get('table')) {
        //     return $this->redirectToRoute('/?table=1');
        // }

        return $this->render('home/index.html.twig', [
            'carousels' => $carousels,
            'hotSells' => $hotSells
        ]);
    }


    #[Route('/detail/{dish}', name: 'dish.detail')]
    public function dishDetail(Dish $dish): Response
    {

        $session = $this->requestStack->getSession();

        $table_no = $session->get('table');






        return $this->render('detail/index.html.twig', [
            'dish' => $dish
        ]);
    }
}
