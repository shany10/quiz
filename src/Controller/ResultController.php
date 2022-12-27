<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\Historique;
use Doctrine\Persistence\ManagerRegistry;


class ResultController extends AbstractController
{
    #[Route('categorie/{id}/result', name: 'app_result')]
    public function index(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        if ($request->query == null) {
            return $this->redirectToRoute('app_question', ['id' => $id]);
        }
        $questions = $doctrine->getRepository(Question::class)->findBy(['id_categorie' => $id]);
        $historique = new Historique();
        $arr_question_valide = [];
        foreach ($questions as $value) {

            $questions = $doctrine->getRepository(Reponse::class)
                ->findBy([
                    'id_question' => $value->getId(),
                    'reponse_expected' => 1
                ]);
            array_push($arr_question_valide, $questions[0]->getReponse());
        }
        $result = [];
        $score = 0;
        $int = 0;
        foreach ($request->query as $value) {
            if ($value == $arr_question_valide[$int]) {
                array_push($result, 1);
                $score++;
            } else {
                array_push($result, 0);
            };
            $int++;
        }
        if ($this->getUser() !== null) {
            $entityManager = $doctrine->getManager();
            $historique->setIdUser($this->getUser()->getId());
            $historique->setIdCategorie($id);
            $historique->setScore($score);
            $entityManager->persist($historique);
            $entityManager->flush();
        }
        // dd($this->getUser());

        return $this->render('result/result.html.twig', [
            'resulta' => $result,
        ]);
    }
}
