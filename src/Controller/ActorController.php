<?php

namespace App\Controller;

use App\Entity\Actor;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actor", name="actor_")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('actor/index.html.twig', [
            'controller_name' => 'ActorController',
        ]);
    }

    /**
     * @Route("/{slug}", name="show")
     */
    public function show(Actor $actor, Slugify $slugify): Response
    {
        $slug = $slugify->generate($actor->getName());
        $actor->setSlug($slug);
        return $this->render('actor/show.html.twig', [
            'actor' => $actor
        ]);
    }
}
