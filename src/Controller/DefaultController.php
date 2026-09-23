<?php

namespace App\Controller;

use App\Entity\Vacancy;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController {

    #[Route("/", name: "homepage")]
    public function homepage(
        Request $request,
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
    ): Response {
        $search = $request->query->get('q');

        $queryBuilder = $em->getRepository(Vacancy::class)->getVacancyListQuery($search);

        $page = max($request->query->getInt('page', 1), 1);
        $vacancy = $paginator->paginate($queryBuilder, $page, 5);

        $tag = $em->getRepository(Tag::class)->findBy([], null, 10);

        return $this->render('default/homepage.html.twig', [
            'vacancy' => $vacancy,
            'tag' => $tag,
            'search' => $search,
        ]);
    }

}
