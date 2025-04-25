<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $hashedPwd, EntityManagerInterface $entityMgmt): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_account');
        }
        $user = new Users;
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $userPwd = $hashedPwd->hashPassword($user, $password);
            $user->setPassword($userPwd);

            $entityMgmt->persist($user);
            $entityMgmt->flush();

            return $this->redirectToRoute('app_home');
        }


        return $this->render('register/register.html.twig', [
            'formInscription' => $form->createView(),
        ]);

    }
}
