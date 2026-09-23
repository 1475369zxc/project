<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

#[AsEventListener(event: InteractiveLoginEvent::class)]
class LoginListener
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $user->setLastLogin(new \DateTime());
        $this->entityManager->flush();
    }
}
