<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $searchTerm = $request->query->get('q');
        if ($searchTerm) {
            $categories = $categoryRepository->search($searchTerm);
        } else {
            $categories = $categoryRepository->findAllOrdered();
        }

        return $this->render('fortune/homepage.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findWithFortuneJoin($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found!');
        }

        return $this->render('fortune/showCategory.html.twig', [
            'category' => $category,
        ]);
    }
}
