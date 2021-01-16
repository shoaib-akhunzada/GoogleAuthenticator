<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SettingFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(UserInterface $user)
    {
        if (!$user->getIsActive()) {
            return $this->redirectToRoute('activation', array('token' => $user->getUuid()));
        }
        
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
    
     /**
     * @Route("/dashboard/setting", name="setting")
     */
    public function setting(Request $request, GoogleAuthenticatorInterface $googleAuthenticator, UserInterface $user)
    {   
        $form = $this->createForm(SettingFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {  
            if($form['googleAuthenticatorSecret']->getData()) {
                $secret = $googleAuthenticator->generateSecret();
                $user->setGoogleAuthenticatorSecret($secret);
            }
            else {
                $user->setGoogleAuthenticatorSecret(null);
            }
           
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            $message = "settings updated";
            $this->addFlash('success', $message);
        }
        
       
        return $this->render('dashboard/setting.html.twig', [
            'settingForm' => $form->createView(),
        ]);
    }
}
