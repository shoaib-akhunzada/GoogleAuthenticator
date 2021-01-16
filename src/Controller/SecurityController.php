<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginFormType;
use App\Form\Check2faFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $this->isGranted('IS_AUTHENTICATED_FULLY');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginFormType::class);

        return $this->render('security/login.html.twig', [
            'loginForm' => $form->createView(),
            'error'         => $error,
            'lastUsername'         => $lastUsername,
        ]);
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
    }

    /**
     * @Route("/2fa", name="2fa_login")
     */
    public function check2fa(GoogleAuthenticatorInterface $googleAuthenticator, TokenStorageInterface $storage)
    {
        $form = $this->createForm(Check2faFormType::class, [
            'action' => $this->generateUrl('2fa_login_check')
        ]);

        $code = $googleAuthenticator->getQRContent($storage->getToken()->getUser());
        $qrCode = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . $code;

        return $this->render('security/2fa_login.html.twig', [
            'check2faForm' => $form->createView(),
            'qrCode' => $qrCode,
        ]);
    }
}
