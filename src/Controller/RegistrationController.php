<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\TOSFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use ReCaptcha\ReCaptcha;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GoogleAuthenticatorInterface $googleAuthenticator, MailerInterface $mailer)
    {
        $user = new User();
       $recaptchaSiteKey = $this->getParameter('google_recaptcha_site_key');
              
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {  
             
            $recaptcha = new ReCaptcha($recaptchaSiteKey);
            $resp = $recaptcha->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());
            
            // uncommenet this block on live
//            if (!$resp->isSuccess()) {
//                $message = "The reCAPTCHA wasn't entered correctly.";
//                $this->addFlash('error', $message);
//                
//                 return $this->redirectToRoute('register');
//            }

            $data = $form->getData();
            
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            
            if(!$data->getGoogleAuthenticatorSecret()) {
                  $user->setGoogleAuthenticatorSecret(null);
            }
            else {
                $secret = $googleAuthenticator->generateSecret();
                $user->setGoogleAuthenticatorSecret($secret);
            }
           
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->sendConfirmationEmailMessage($user, $mailer);
            
            $message = 'Registered successfully, Activation email has been send to your email account!';
            $this->addFlash('success', $message);
                
            return $this->redirectToRoute('register');
        }
        
       return $this->render('registration/register.html.twig', [
           'registrationForm' => $form->createView(),
           'recaptchaSiteKey' => $recaptchaSiteKey,
        ]);
    }
    
    public function sendConfirmationEmailMessage(User $user, MailerInterface $mailer)
    {
        $confirmationToken = $user->getUuid();

        $email = (new TemplatedEmail())
            ->from(new Address('shoaib@gmail.com', 'mail bot'))
            ->to($user->getEmail())
            ->subject('Account activation')
            ->htmlTemplate('registration/activation_email.html.twig')
            ->context([
                'confirmationToken' => $confirmationToken
            ])
        ;

        $mailer->send($email);
    }
    
    /**
    * @Route("user/activate/{token}", name="activation")
    */
    public function confirm(Request $request, $token)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy([
            'uuid' => $token,
        ]);
        
        if (!$user) {
            throw $this->createNotFoundException('We couldn\'t find an account for that confirmation token');
        }

        if ($user->getIsActive()) {
            return $this->redirectToRoute('login');
        }
        
        $form = $this->createForm(TOSFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            
            if (true === $form['is_active']->getData()) {
                $user->setIsActive(true);

                $entityManager->persist($user);
                $entityManager->flush();
                
                return $this->redirectToRoute('welcome');
            }
        }
        
        return $this->render('registration/confirm.html.twig', [
           'TOSForm' => $form->createView(),
        ]);
    }
    
    /**
    * @Route("user/welcome", name="welcome")
    */
    public function welcome(Request $request)
    {
        return $this->render('registration/welcome.html.twig'); 
    }
}
