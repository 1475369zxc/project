<?php

namespace App\Controller;

use App\Form\SalesforceForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\SalesforceService;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SalesforceController extends AbstractController
{
    #[Route('/profile/salesforce', name: 'profile_salesforce')]
    public function salesforce(
        Request $request,
        SalesforceService $salesforce,
        EntityManagerInterface $em
    ): Response
    {
        $form = $this->createForm(SalesforceForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            /** @var User $user */
            $user = $this->getUser();

            try {
                if ($user->getSalesforceAccountId() === null) {
                    $result = $salesforce->createAccountWithContact(
                        pastCompany: $data['pastCompany'],
                        lastName: $user->getName() ?? 'Unknown',
                        email: $user->getEmail(),
                        pastWork: $data['pastWork'] ?? null,
                        phone: $data['phone'] ?? null,
                    );

                    $user->setSalesforceAccountId($result['account_id']);
                    $user->setSalesforceContactId($result['contact_id']);
                    $em->flush();

                    $this->addFlash('success', 'Created in Salesforce!');
                } else {
                    $salesforce->updateAccountWithContact(
                        accountId: $user->getSalesforceAccountId(),
                        contactId: $user->getSalesforceContactId(),
                        pastCompany: $data['pastCompany'],
                        lastName: $user->getName() ?? 'Unknown',
                        email: $user->getEmail(),
                        pastWork: $data['pastWork'] ?? null,
                        phone: $data['phone'] ?? null,
                    );

                    $this->addFlash('success', 'Updated in Salesforce!');
                }
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Salesforce error: ' . $e->getMessage());
            }

            return $this->redirectToRoute('profile');
        }

        return $this->render('salesforce/salesforce.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
