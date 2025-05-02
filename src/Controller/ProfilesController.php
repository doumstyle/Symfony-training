<?php

namespace App\Controller;

use App\Entity\Profiles;
use App\Form\ProfilesType;
use App\Repository\CategoriesRepository;
use App\Repository\ProfilesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/account')]
final class ProfilesController extends AbstractController
{
    #[Route(name: 'app_profiles_index', methods: ['GET'])]
    public function index(ProfilesRepository $profilesRepository, CategoriesRepository $categoriesRepo): Response
    {
        $user = $this->getUser();
        $categories = $categoriesRepo->findAll();

        return $this->render('account/account.html.twig', [
            'profile' => $profilesRepository->findByUser($user),
            'categories' => $categories

        ]);
    }

    #[Route('/new', name: 'app_profiles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepo): Response
    {
        $profile = new Profiles();
        $form = $this->createForm(ProfilesType::class, $profile);
        $form->handleRequest($request);
        $categories = $categoriesRepo->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $profile->setUser($user);

            $picture = $form->get('picture')->getData();
            $ogPicture = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            $safePicture = $slugger->slug($ogPicture);

            $newPicture = $safePicture . '-' . uniqid() . '.' . $picture->guessExtension();

            $picture->move($this->getParameter('profiles_image_directory'), $newPicture);

            $profile->setPicture($newPicture);

            $entityManager->persist($profile);
            $entityManager->flush();

            return $this->redirectToRoute('app_profiles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profiles/new.html.twig', [
            'profile' => $profile,
            'form' => $form,
            'categories' => $categories
        ]);
    }

    #[Route('/{id}/edit', name: 'app_profiles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Profiles $profile, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategoriesRepository $categoriesRepo): Response
    {
        $categories = $categoriesRepo->findAll();

        $userProfileId = $this->getUser()->getProfiles()->getId();
        if ($profile->getId() !== $userProfileId) {
            return $this->redirectToRoute('app_profiles_edit', ['id' => $userProfileId], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(ProfilesType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $profile->setUser($user);

            $picture = $form->get('picture')->getData();
            $ogPicture = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
            $safePicture = $slugger->slug($ogPicture);

            $newPicture = $safePicture . '-' . uniqid() . '.' . $picture->guessExtension();

            $picture->move($this->getParameter('profiles_image_directory'), $newPicture);

            $profile->setPicture($newPicture);
            $entityManager->flush();

            return $this->redirectToRoute('app_profiles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profiles/edit.html.twig', [
            'profile' => $profile,
            'form' => $form,
            'categories' => $categories
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
