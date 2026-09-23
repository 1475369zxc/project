<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Vacancy;
use App\Entity\Tag;

class VacancyController extends AbstractController {

    #[Route("/vacancy_show/{id}", name: "vacancy_show")]
    public function show(int $id, EntityManagerInterface $em): Response {
        $vacancy = $em -> getRepository(Vacancy::class) -> find($id);
        $vacancy->getAttributes()->toArray();

        $tag = $em->getRepository(Tag::class)->findAll();

        return $this->render('vacancy/show.html.twig', [
            'vacancy' => $vacancy,
            'tag' => $tag,
        ]);

    }

}
