<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\SearchType;
use App\Services\PercenTool;
use App\Model\SearchData;
use App\Repository\CategoryRepository;
use App\Repository\TutorialRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/categorie')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index_category')]
    public function index(
        CategoryRepository $categoryRepository,
        TutorialRepository $tutorialRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $categories = $categoryRepository->findAll();
        $tutorials = $tutorialRepository->findAll();

        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $queryBuilder = $tutorialRepository->findBySearch($searchData);

            $pagination = $paginator->paginate(
                $queryBuilder,
                $searchData->page,
                12 // Number of items per page
            );

            return $this->render('tutorial/searchtutorials.html.twig', [
                'form' => $form->createView(),
                'pagination' => $pagination,
                'tutorials' => $tutorials,
            ]);
        }

        return $this->render('category/index.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug}', name: 'category_show')]
    public function show(
        Category $category,
        PercenTool $percenTool
    ): Response {

        $successByCategory = $percenTool->calculatePercentage($category);

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'successByCategory' => $successByCategory,
        ]);
    }
}
