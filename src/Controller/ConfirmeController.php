<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmeController extends AbstractController
{
    #[Route('/confirme', name: 'app_confirme')]
    public function index(): Response
    {
        return $this->render('security/confirmation.html.twig');
    }
}
