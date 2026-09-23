<?php

namespace App\Email;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class EmailSend
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $emailFrom,
        private string $emailReplyTo
    )
    {

    }

    private function sendEmail(string $to, string $subject, string $htmlContent): void
    {
        $email = (new Email())
            ->from($this->emailFrom)
            ->to($to)
            ->replyTo($this->emailReplyTo)
            ->subject($subject)
            ->html($htmlContent);

        $this->mailer->send($email);
    }

    public function sendConfirmationEmail(User $user): void
    {
        $htmlContent = $this->twig->render('email/register.html.twig', [
            'name' => $user->getName(),
            'confirmation' => $user->getConfirmationCode(),
        ]);

        $this->sendEmail(
            to: $user->getEmail(),
            subject: 'Welcome!',
            htmlContent: $htmlContent
        );
    }

    public function sendPasswordResetEmail(User $user): void
    {
        $htmlContent = $this->twig->render('email/reset_password.html.twig', [
            'name' => $user->getName(),
            'resetToken' => $user->getConfirmationCode(),
        ]);

        $this->sendEmail(
            to: $user->getEmail(),
            subject: 'Reset your password',
            htmlContent: $htmlContent
        );
    }
}
