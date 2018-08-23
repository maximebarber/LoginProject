<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // * 1. Build the form

        $user = new User();
        $form = $this->createForm(Usertype::class, $user);

        // * 2. Handle the submit (will only happen on POST)

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            // * 3. Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            //By default isActive is on
            $user->setIsActive(true);
            //$user->addRole("ROLE_ADMIN");

            // * 4. Save the user
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //Other possible actions (send email, set a flash success message...)
            // * 5. Generate info message
            $this->addFlash('success', 'Votre compte a bien été enregistré.');
            
            // * 6. Redirect to the "login" route
            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'mainNavRegistration' => true,
            'title' => 'Inscription'
        ]);
    }
}
