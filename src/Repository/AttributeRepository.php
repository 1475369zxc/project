<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\User;
use App\Entity\UserAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<AttributeCategory>
 */
class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    public function createAvailableForUserQueryBuilder(?User $user, string $alias = 'a'): QueryBuilder
    {
        $qb = $this->createQueryBuilder($alias);

        if ($user) {
            $qb->leftJoin(
                    UserAttribute::class,
                    'ua',
                    'WITH',
                    "ua.attribute = $alias AND ua.user = :user"
                )
                ->andWhere('ua.id IS NULL')
                ->setParameter('user', $user);
        }

        return $qb->orderBy("$alias.name", 'ASC');
    }

    public function findAvailableForUser(?User $user): array
    {
        return $this->createAvailableForUserQueryBuilder($user)
            ->getQuery()
            ->getResult();
    }
}
