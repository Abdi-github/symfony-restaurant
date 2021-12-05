<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'menu')]
    public function index(CategoryRepository $cr): Response
    {
        $categories = $cr->getAllCategoriesWithDetail();
        // \dd($categories);
        return $this->render('menu/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
