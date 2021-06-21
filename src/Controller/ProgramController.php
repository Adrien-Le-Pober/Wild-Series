<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Program;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Service\Mailer;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/programs", name="program_")
 */
class ProgramController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        return $this->render('program/index.html.twig', [
            'programs' => $programs
        ]);
    }

    /**
     *
     * @Route("/new", name="new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, Slugify $slugify, Mailer $mailer) : Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $entityManager->persist($program);
            $entityManager->flush();
            $mailer->sendMail($program,'Program/newProgramEmail.html.twig');
            return $this->redirectToRoute('program_index');
        }
        return $this->render('program/new.html.twig', [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Program $program, Slugify $slugify): Response
    {
        $seasons = $this->getDoctrine()->getRepository(Season::class)
            ->findBy(['programs' => $program]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$program->getId().' found in program\'s table.'
            );
        }
        $slug = $slugify->generate($program->getTitle());
        $program->setSlug($slug);
        return $this->render('program/show.html.twig',[
            'program' => $program,
            'seasons' => $seasons
        ]);
    }


    /**
     * @Route("/{programId}/seasons/{seasonId}", name="season_show", methods={"GET"}, requirements={"programId"="\d+", "seasonId"="\d+"})
     *
     */
    public function showSeason(Program $programId, Season $seasonId): Response
    {
        return $this->render('program/season_show.html.twig', [
        "program" => $programId,
        "season" => $seasonId
    ]);
    }


    /**
     * @Route("/{programId}/seasons/{seasonId}/episodes/{episodeId}", name="episode_show", methods={"GET"}, requirements={"programId"="\d+", "seasonId"="\d+", "episodeId"="\d+"})
     *
     */
    public function showEpisode(Program $programId, Season $seasonId, Episode $episodeId, Request $request): Response
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($programId);
            $entityManager->flush();
        }
        return $this->render('program/episode_show.html.twig', [
            "program" => $programId,
            "season" => $seasonId,
            "episode" => $episodeId
        ]);
    }
}