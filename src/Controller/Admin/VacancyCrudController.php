<?php

namespace App\Controller\Admin;

use App\Entity\Vacancy;
use App\Controller\Admin\AttributeCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use App\Service\Admin\VacancyCloner;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;


class VacancyCrudController extends AbstractCrudController
{
    public function __construct(
        private VacancyCloner $vacancyCloner
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Vacancy::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/edit', 'admin/vacancy/edit.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        $duplicate = Action::new('duplicate', 'Duplicate', 'fa fa-copy')
            ->linkToCrudAction('duplicate');

        $batchDuplicate = Action::new('batchDuplicate', 'Duplicate', 'fa fa-copy')
            ->linkToCrudAction('batchDuplicate');

        return $actions
            ->add(Crud::PAGE_INDEX, $duplicate)
            ->add(Crud::PAGE_EDIT, $duplicate)
            ->add(Crud::PAGE_EDIT, Action::DELETE)
            ->addBatchAction($batchDuplicate);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('photo')
                ->setTemplatePath('admin/fields/image_vacancy.html.twig'),
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('description')
                ->onlyOnForms(),
            BooleanField::new('isPublic')
                ->setLabel('Is public')
                ->renderAsSwitch(false),
            IntegerField::new('maxProjects')
                ->setLabel('Max project'),
            DateTimeField::new('createdAt')
                ->setLabel('Created At')
                ->hideOnForm()
                ->setFormat('dd.MM.yyyy HH:mm'),
            DateTimeField::new('updatedAt')
                ->setLabel('Update At')
                ->hideOnForm()
                ->setFormat('dd.MM.yyyy HH:mm'),
            AssociationField::new('attributes')
                ->setLabel('Attributes')
                ->setCrudController(AttributeCrudController::class)
                ->autocomplete(),
        ];
    }

    #[AdminRoute]
    public function duplicate(
        AdminContext $context,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        $vacancy = $context->getEntity()->getInstance();

        $newVacancy = $this->vacancyCloner->clone($vacancy);

        $em->persist($newVacancy);
        $em->flush();

        $this->addFlash('success', 'The vacancy has been duplicated!');

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::EDIT)
            ->setEntityId($newVacancy->getId())
            ->generateUrl();

        return $this->redirect($url);
    }

    #[AdminRoute]
    public function batchDuplicate(
        BatchActionDto $batchActionDto,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        $repository = $em->getRepository(Vacancy::class);

        $vacancies = [];
        foreach ($batchActionDto->getEntityIds() as $id) {
            $vacancy = $repository->find($id);
            if ($vacancy) {
                $vacancies[] = $vacancy;
            }
        }

        $clones = $this->vacancyCloner->cloneMany($vacancies);

        foreach ($clones as $clone) {
            $em->persist($clone);
        }
        $em->flush();

        $this->addFlash('success', sprintf('Clone %d vacancy!', count($clones)));

        $url = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

}
