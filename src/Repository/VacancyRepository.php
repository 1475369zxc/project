<?php

namespace App\Repository;

use App\Entity\Vacancy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Vacancy>
 */
class VacancyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vacancy::class);
    }

    public function getVacancyListQuery(?string $search = null): QueryBuilder {
        $qb = $this->createQueryBuilder('v')
            ->where('v.isPublic = :isPublic')
            ->setParameter('isPublic', true)
            ->orderBy('v.createdAt', 'DESC');

        if ($search) {
            $qb->andWhere('v.title LIKE :search')
            ->setParameter('search', '%' . $search . '%');
        }

        return $qb;
    }

}
