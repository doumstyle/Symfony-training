<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Form\ArticlesType;
use App\Repository\ArticlesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/articles')]
final class ArticlesController extends AbstractController
{
    #[Route(name: 'app_my_articles', methods: ['GET'])]
    public function index(ArticlesRepository $articlesRepository): Response
    {
        $user = $this->getUser();
        return $this->render('articles/index.html.twig', [
            'articles' => $articlesRepository->findByUser($user)
        ]);
    }

    #[Route('/new', name: 'app_articles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $article = new Articles();
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            $article->setUser($user);

            $image = $form->get('image')->getData();
            $ogImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImage = $slugger->slug($ogImage);

            $newImage = $safeImage . '-' . uniqid() . '.' . $image->guessExtension();

            $image->move($this->getParameter('articles_image_directory'), $newImage);

            $article->setImage($newImage);

            $categories = $article->getCategory()->getValues();
            foreach ($categories as $category) {
                $category->addArticle($article);
                $entityManager->persist($category);
            }

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_my_articles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('articles/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_show', methods: ['GET'])]
    public function show(Articles $article): Response
    {
        return $this->render('articles/show.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_articles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Articles $article, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticlesType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $article->setUser($user);

            $image = $form->get('image')->getData();
            $ogImage = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImage = $slugger->slug($ogImage);

            $newImage = $safeImage . '-' . uniqid() . '.' . $image->guessExtension();

            $image->move($this->getParameter('articles_image_directory'), $newImage);

            $article->setImage($newImage);

            $categories = $article->getCategory()->getValues();
            foreach ($categories as $category) {
                $category->addArticle($article);
                $entityManager->persist($category);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_my_articles', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('articles/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_articles_delete', methods: ['POST'])]
    public function delete(Request $request, Articles $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_my_articles', [], Response::HTTP_SEE_OTHER);
    }
}
