<?php

namespace App\Controller;

use App\Form\ProfileForm ;
use App\Email\EmailSend;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AuthController extends AbstractController {

    #[Route("/login", name: "login")]
    public function login(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        AuthService $authService
    ): Response {

        if ($request->query->get('blocked')) {
            $this->addFlash('error', 'Your account is blocked');
        }

        $code = $request->query->get('code');
        if (false === empty($code)) {
            $user = $authService->verifyUserByCode($code);
            if ($user) {
                $this->addFlash('success', 'Your account has been verified.');
            }
            else {
                $this->addFlash('error', 'Invalid or outdated confirmation code.');
            }

            return $this->redirectToRoute('login');
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $lastEmail = $authenticationUtils->getLastUsername();
            $this->addFlash('error', $authService->getLoginErrorMessage($lastEmail));
        }

        return $this->render('auth/login.html.twig');
    }

    #[Route("/register", name: "register")]
    public function register(
        Request $request,
        EmailSend $emailSend,
        MailerInterface $mailer,
        AuthService $authService
    ): Response {

        $form = $this->createForm(ProfileForm ::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            try {
                $authService->registerUser(
                    $user,
                    $form->get('password')->getData(),
                    $form->get('photo')->getData()
                );

                $emailSend->sendConfirmationEmail($user, $mailer);

                $this->addFlash('success', 'Registration is successful! Check your email for confirmation.');

                return $this->redirectToRoute('login');

            } catch(\Exception $e) {
                $this->addFlash('error', 'Failed to register');
            }
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please correct the errors in the form.');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/logout", name: "logout")]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
