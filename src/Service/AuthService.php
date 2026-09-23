<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\ImageService;

class AuthService {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private UserPasswordHasherInterface $passwordHasher,
        private ImageService $imageService
    )
    {

    }

    public function verifyUserByCode(string $code): ?User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
                'confirmationCode' => $code,
        ]);
        if (false === \is_null($user)) {
            $user->setConfirmationCode(null);
            $user->setIsVerified(true);

            $this->entityManager->flush();

            return $user;
        }

        return null;
    }

    public function getLoginErrorMessage(?string $lastEmail): ?string
    {
        if (!$lastEmail) {
            return 'Please enter your email address';
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $lastEmail
        ]);

        if (!$user) {
            return 'User with this email not found';
        }

        if ($user->isVerified() === false) {
            return 'Your account is not verified. Please check your email';
        }

        if ($user->isBlocked()) {
            return 'Your account is blocked. Please contact administrator';
        }

        return 'Invalid password. Please try again';
    }

    public function registerUser(
        User $user,
        string $plainPassword,
        $photoFile
    ): User {

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $value = microtime(true) . $user->getEmail();
        $user->setConfirmationCode(md5($value));

        $user = $this->imageService->getPhoto($user, $photoFile);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;

    }




}
