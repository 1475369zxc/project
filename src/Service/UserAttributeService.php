<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserAttribute;
use Doctrine\ORM\EntityManagerInterface;

class UserAttributeService
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function addAttribute(User $user, UserAttribute $attribute): bool
    {
        $existing = $this->em->getRepository(UserAttribute::class)->findOneBy([
            'user' => $user,
            'attribute' => $attribute->getAttribute(),
        ]);

        if ($existing) {
            return false;
        }

        $this->em->persist($attribute);
        $this->em->flush();

        return true;
    }

    public function deleteAttribute(UserAttribute $attribute): void
    {
        $this->em->remove($attribute);
        $this->em->flush();
    }
}
