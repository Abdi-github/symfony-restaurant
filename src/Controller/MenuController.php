<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/menu', name: 'menu')]
    public function index(CategoryRepository $cr): Response
    {
        $session = $this->requestStack->getSession();

        $table_no = $session->get('table');
        $categories = $cr->getAllCategoriesWithDetail();
        // \dd($categories);
        return $this->render('menu/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
