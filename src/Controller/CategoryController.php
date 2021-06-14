<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Entity\Program;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/categories", name="category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     *
     * @Route("/new", name="new")
     */
    public function new(Request $request, Slugify $slugify) : Response
    {
        $category = new Category();
        $slug = $slugify->generate($category->getName());
        $category->setSlug($slug);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/{categoryName}", name="show")
     */
    public function show(string $categoryName)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)
            ->findOneBy(['name' => $categoryName]);

        if($category) {
            $series = $this->getDoctrine()->getRepository(Program::class)
                ->findBy(
                    ['category' => $category->getId()], ['id' => 'DESC'], 3);
        }
        if (!$category) {
            throw $this->createNotFoundException(
                'No category with name : '.$categoryName.' found in category\'s table.'
            );
        }
        return $this->render('category/show.html.twig',[
            'category' => $category,
            'series' => $series
        ]);
    }

    public function getCategories()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)
            ->findAll();
        $actors = $this->getDoctrine()->getRepository(Actor::class)
            ->findAll();

        return $this->render('_navbar.html.twig', [
            'categories' => $categories,
            'actors' => $actors
        ]);
    }
}
