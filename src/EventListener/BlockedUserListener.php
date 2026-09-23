<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsEventListener(event: RequestEvent::class)]
class BlockedUserListener
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RouterInterface $router
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return;
        }

        if ($user->isBlocked()) {
            $this->tokenStorage->setToken(null);

            if ($event->getRequest()->hasSession()) {
                $event->getRequest()->getSession()->invalidate();
            }

            $event->setResponse(new RedirectResponse(
                $this->router->generate('login', ['blocked' => 1])
            ));
        }
    }
}
