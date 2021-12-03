<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/dish', name: 'dish.')]
class DishController extends AbstractController
{
    #[Route('/', name: 'all')]
    public function index(DishRepository $dr): Response
    {
        $dishes = $dr->findAll();
        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, SluggerInterface $slugger, FileUploader $fileUploader): Response
    {
        $dish = new Dish();

        $form = $this->createForm(DishType::class, $dish);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {



            // WITH OUT FILE UPLOAD SERVICE

            // $image = $form->get('image')->getData();
            // $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // // this is needed to safely include the file name as part of the URL
            // $safeFilename = $slugger->slug($originalFilename);
            // $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();



            // $image->move(
            //     $this->getParameter('images_directory'),
            //     $newFilename
            // );

            // $dish->setImage($newFilename);



            // WITH OUT FILE UPLOAD SERVICE

            $image = $form->get('image')->getData();

            $imageName = $fileUploader->upload($image);
            $dish->setImage($imageName);



            $em = $this->getDoctrine()->getManager();
            $em->persist($dish);
            $em->flush();

            $this->addFlash('success', 'Dish Created Successfully');

            return $this->redirectToRoute('dish.all');
        }

        return $this->render('dish/create.html.twig', [
            'createForm' => $form->createView()
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(DishRepository $dr, $id)
    {
        $dish = $dr->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($dish);
        $em->flush();

        $this->addFlash('success', 'Dish Deleted Successfully');

        return $this->redirectToRoute('dish.all');
    }

    // #[Route('/edit-show/{id}', name: 'edit.show')]
    // public function edit(Request $request, CategoryRepository $cr, Dish $dish, FileUploader $fileUploader)
    // {

    //     $categories = $cr->findAll();



    //     $form = $this->createForm(DishType::class, $dish);
    //     $form->handleRequest($request);

    //     $image = $form->get('image')->getData();
    //     if ($form->isSubmitted() && $form->isValid()) {





    //         if ($image) {
    //             $imageFileName = $dish->getImage();

    //             $pathToFile = $this->getParameter('images_directory') . '/' . $imageFileName;
    //             \unlink($pathToFile);



    //             $imageName = $fileUploader->upload($image);
    //             $dish->setImage($imageName);

    //             $this->getDoctrine()->getManager()->flush();

    //             return $this->redirectToRoute('dish.all');
    //         }

    //         $oldImage = $dish->getImage();
    //         $dish->setImage($oldImage);


    //         $this->getDoctrine()->getManager()->flush();
    //         return $this->redirectToRoute('dish.all');
    //     }



    //     return $this->render('dish/edit.html.twig', [
    //         'dish' => $dish,
    //         'categories' => $categories,
    //         'form' => $form->createView(),


    //     ]);
    // }
    #[Route('/edit-show/{id}', name: 'edit.show')]
    public function edit(Request $request, $id, CategoryRepository $cr, EntityManagerInterface $entityManager, DishRepository $dr, SluggerInterface $slugger, dish $dish, LoggerInterface $logger): Response
    {

        $categories = $cr->findAll();

        $dish->setImage(new File(sprintf('%s/%s', $this->getParameter('images_directory'), $dish->getImage())));
        $form = $this->createForm(DishType::class, $dish);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dish      = $form->getData();
            $image = $form->get('image')->getData();
            if ($image) {


                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Image cannot be saved.');
                }
                $dish->setImage($newFilename);
            }

            $entityManager->persist($dish);
            $entityManager->flush();
            $this->addFlash('success', 'Dish was edited!');
        }
        return $this->render('dish/edit.html.twig', [
            'dish' => $dish,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }
}
