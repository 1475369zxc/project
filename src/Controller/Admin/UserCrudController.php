<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Enum\UserRole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;


class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private ParameterBagInterface $params
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DETAIL, 'ROLE_RECRUITER')

            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN')
            ->setPermission(Action::BATCH_DELETE, 'ROLE_ADMIN');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->overrideTemplate('crud/edit', 'admin/user/edit.html.twig');
    }


    public function configureFields(string $pageName): iterable
    {

        return [
            TextField::new('photo')
                ->setTemplatePath('admin/fields/image_user.html.twig')
                ->hideOnForm(),
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            DateTimeField::new('birthday')
                ->setFormTypeOption('disabled', true)
                ->setFormat('dd.MM.yyyy HH:mm'),
            EmailField::new('email'),
            ChoiceField::new('role')
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', UserRole::class)
                ->setFormTypeOption('choice_label', fn(UserRole $role) => $role->label()),
            BooleanField::new('isVerified')
                ->renderAsSwitch(false),
            BooleanField::new('isBlocked')
                ->renderAsSwitch(false),
            DateTimeField::new('lastLogin')
                ->setLabel('Last login')
                ->setFormTypeOption('disabled', true)
                ->setFormat('dd.MM.yyyy HH:mm'),
            DateTimeField::new('createdAt')
                ->setLabel('Created at')
                ->setFormTypeOption('disabled', true)
                ->setFormat('dd.MM.yyyy HH:mm'),
            DateTimeField::new('updatedAt')
                ->setLabel('Updated at')
                ->setFormTypeOption('disabled', true)
                ->setFormat('dd.MM.yyyy HH:mm'),


        ];
    }

}
