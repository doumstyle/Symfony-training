<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    // #[Route('/account', name: 'app_account')]
    public function index(CategoriesRepository $categoriesRepo): Response
    {
        $categories = $categoriesRepo->findAll();
        return $this->render('account/account.html.twig', [
            'controller_name' => 'AccountController',
            'categories' => $categories
        ]);
    }
}
