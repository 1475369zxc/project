<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class ProfileService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ImageService $imageService,
    ) {
    }

    public function updateProfile(User $user, FormInterface $form): User
    {
        $user = $form->getData();
        $user = $this->imageService->getPhoto($user, $form->get('photo')->getData());

        $this->em->flush();

        return $user;
    }
}
