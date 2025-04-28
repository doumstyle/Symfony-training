<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Form\ProfilesType;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
final class ProfilesController extends AbstractController
{
    #[Route(name: 'app_profiles_index', methods: ['GET'])]
    public function index(ProfilesRepository $profilesRepository): Response
    {
        $user = $this->getUser();

        return $this->render('account/account.html.twig', [
            'profile' => $profilesRepository->findByUser($user),

        ]);
    }

    #[Route('/new', name: 'app_profiles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $profile = new Profiles();
        $form = $this->createForm(ProfilesType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $profile->setUser($user);
            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('app_profiles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profiles/new.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profiles_show', methods: ['GET'])]
    public function show(Profiles $profile): Response
    {
        return $this->render('profiles/show.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profiles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profiles $profile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfilesType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profiles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profiles/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_profiles_delete', methods: ['POST'])]
    public function delete(Request $request, Profiles $profile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $profile->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($profile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_profiles_index', [], Response::HTTP_SEE_OTHER);
    }
}
