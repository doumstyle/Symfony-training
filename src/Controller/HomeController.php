<?php

namespace App\Controller;

use App\Repository\ArticlesRepository;
use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticlesRepository $articlesRepo, CategoriesRepository $categoriesRepo): Response
    {
        $limit = 3;
        $articles = $articlesRepo->getLastArticles($limit);

        $categories = $categoriesRepo->findAll();

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'title' => 'News',
            'limit' => $limit,
            'categories' => $categories
        ]);
    }

    #[Route('/allArticles', name: 'app_home_all')]
    public function getAllArticles(ArticlesRepository $articlesRepo, CategoriesRepository $categoriesRepo): Response
    {
        $articles = $articlesRepo->findAll();
        $categories = $categoriesRepo->findAll();

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
            'title' => 'All Articles',
            'categories' => $categories
        ]);

    }
    #[Route('/article/{id}', name: 'app_home_article')]
    public function showArticle(ArticlesRepository $articlesRepo, $id, CategoriesRepository $categoriesRepo): Response
    {
        $article = $articlesRepo->findOneBy(['id' => $id]);
        $title = $article->getTitle();
        $categories = $categoriesRepo->findAll();

        return $this->render('home/index.html.twig', [
            'article' => $article,
            'title' => $title,
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_articles')]
    public function showCategory(ArticlesRepository $articlesRepo, CategoriesRepository $categoriesRepo, $id): Response
    {
        $categories = $categoriesRepo->findAll();
        $category = $categoriesRepo->findOneBy(['id' => $id]);

        return $this->render('home/index.html.twig', [
            'articles' => $articlesRepo->findByCategory($id),
            'title' => $category->getName(),
            'categories' => $categories
        ]);
    }
}
