<?php

namespace App\Controller;

use App\Form\ProfileForm ;
use App\Form\UserAttributeType;
use App\Entity\UserAttribute;
use App\Entity\Attribute;
use App\Service\UserAttributeService;
use App\Service\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;


class ProfileController extends AbstractController {

    #[Route("/profile", name: "profile")]
    public function profile(
        Request $request,
        EntityManagerInterface $em,
        ProfileService $profileService,
        UserAttributeService $userAttributeService
    ): Response {

        $user = $this->getUser();

        $meForm = $this->createForm(ProfileForm::class, $user, [
            'is_profile' => true,
        ]);
        $meForm->handleRequest($request);

        $newUserAttribute = new UserAttribute();
        $newUserAttribute->setUser($user);

        $attributeForm = $this->createForm(UserAttributeType::class, $newUserAttribute, [
            'em' => $em,
            'user' => $user,
        ]);
        $attributeForm->handleRequest($request);

        if ($meForm->isSubmitted() && $meForm->isValid()) {
            $user = $meForm->getData();

            try {
                $profileService->updateProfile($user, $meForm);

                $this->addFlash('success', 'The profile has been updated!');
                return $this->redirectToRoute('profile');

            } catch(\Exception $e) {
                $this->addFlash('error', 'Failed to update');
            }
        }

        if ($attributeForm->isSubmitted() && $attributeForm->isValid()) {
            $added = $userAttributeService->addAttribute($user, $newUserAttribute);

            if ($added) {
                $this->addFlash('success', 'The attribute has been added!');
            } else {
                $this->addFlash('error', 'This attribute has already been added');
            }

            return $this->redirectToRoute('profile');
        }

        if ($meForm->isSubmitted() && !$meForm->isValid()) {
            $this->addFlash('error', 'Please correct the errors in the form.');
        }

        return $this->render('auth/profile.html.twig', [
            'user' => $user,
            'meForm' => $meForm->createView(),
            'attributeForm' => $attributeForm->createView(),
        ]);
    }

    #[Route('/profile/attribute/value-field', name: 'profile_attribute_value_field', methods: ['POST'])]
    public function valueField(Request $request, EntityManagerInterface $em): Response
    {
        $attributeId = $request->request->get('attribute');

        $attribute = $em->getRepository(Attribute::class)->find($attributeId);

        if (!$attribute) {
            return new Response('', 204);
        }

        $userAttribute = new UserAttribute();
        $userAttribute->setAttribute($attribute);

        $form = $this->createForm(UserAttributeType::class, $userAttribute, [
            'em' => $em,
        ]);

        return $this->render('auth/_value_field.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/attribute/{id}/delete', name: 'profile_attribute_delete', methods: ['POST'])]
    public function deleteAttribute(
        UserAttribute $userAttribute,
        Request $request,
        UserAttributeService $attributeService,
    ): Response {
        if ($userAttribute->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('profile');
        }

        if (!$this->isCsrfTokenValid('delete_attribute_' . $userAttribute->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $attributeService->deleteAttribute($userAttribute);

        $this->addFlash('success', 'The attribute has been removed.');

        return $this->redirectToRoute('profile');
    }
}
