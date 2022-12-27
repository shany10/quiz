<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;


class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(CategorieRepository $repository , ManagerRegistry $doctrine): Response
    {
        $user_interface = $this->getUser();
        if($user_interface) {
            $entityManager = $doctrine->getManager();
            $user_db = $entityManager->getRepository(User::class)->find($user_interface->getId());
            if($user_db->isVerified() == false) {
                return $this->redirectToRoute('app_confirme');
            };
        }

        $categorie = $repository->findAll();
        return $this->render('home/home.html.twig' ,[
            'categorie' => $categorie
        ]);
    }
}
