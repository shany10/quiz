<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ModifEmailType;
use App\Form\ModifPasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Security\EmailVerifier;


class ProfilController extends AbstractController
{

    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/profil', name: 'app_profil')]
    public function index(Request $request , ManagerRegistry $doctrine , UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $user_interface = $this->getUser();
        if(!$user_interface) {
            return $this->redirectToRoute('app_home');
        }
        $emailform = $this->createForm(ModifEmailType::class);
        $passwordform = $this->createForm(ModifPasswordType::class);

        $entityManager = $doctrine->getManager();
        $user_db = $entityManager->getRepository(User::class)
        ->find($user_interface->getId());
        $emailform->handleRequest($request);
        $passwordform->handleRequest($request);

        if ($emailform->isSubmitted() && $emailform->isValid()) {
            $user_db->setEmail($emailform->get('email')->getData());
            $user_db->setisVerified(false);
            $entityManager->flush();
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user_db,
            (new TemplatedEmail())
                ->from(new Address('shanyahsam@gmail.com'))
                ->to($user_db->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')       
            );
            // do anything else you need here, like send an email
            return $this->redirectToRoute('app_confirme');
        }

        if ($passwordform->isSubmitted() && $passwordform->isValid()) {
          
            $user_db->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $passwordform->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();
        }

        return $this->render('profil/profil.html.twig', [
            'user' => $user_interface,
            'emailForm' => $emailform->createView(),
            'passwordForm' => $passwordform->createView(),
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_login');
        }
       
        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }
}
