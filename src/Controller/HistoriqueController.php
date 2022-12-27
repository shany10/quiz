<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Historique;
use App\Entity\Categorie;

class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'app_historique')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $arr_categorie = [];
        $arr_score =[];
        $arr_date = [];
        if($this->getUser()) {
            $historique = $doctrine->getRepository(Historique::class)->findBy(['id_user' => $this->getUser()->getId()]);
            foreach($historique as $value) {
                $categorie = $doctrine->getRepository(Categorie::class)->findBy(['id' => $value->getIdCategorie()]);
                array_push($arr_categorie , $categorie[0]->getName());
                array_push($arr_score , $value->getScore());
                array_push($arr_date , $value->getDate());
            }
        }
        return $this->render('historique/historique.html.twig', [
            'categorie' => $arr_categorie,
            'score' => $arr_score,
            'date' => $arr_date
        ]);
    }
}
