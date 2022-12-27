<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Repository\ReponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
class QuestionController extends AbstractController
{
    #[Route('/categorie/{id}', name: 'app_question')]
    public function index(int $id , QuestionRepository $doctrine_question , ReponseRepository $doctrine_reponse ): Response
    {
        $question = $doctrine_question->findBy(['id_categorie' => $id]);
        $arr_question = [];
        $arr_proposition = [];
        foreach($question as $value) {
            $proposition = $doctrine_reponse->findBy(['id_question' => $value->getId()]);
            $arr_question[$value->getId()] = $value->getQuestion();
            $arr_proposition[$value->getId()] = $proposition;
        }
        return $this->render('question/question.html.twig', [
             'question' => $arr_question , 
             'proposition' => $arr_proposition,
             'id' => $id
        ]);
    }
}
