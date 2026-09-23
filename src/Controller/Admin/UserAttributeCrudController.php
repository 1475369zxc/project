<?php

namespace App\Controller\Admin;

use App\Entity\UserAttribute;
use App\Enum\AttributeType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;


class UserAttributeCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return UserAttribute::class;
    }


    public function configureFields(string $pageName): iterable
    {

        return [
            IdField::new('id')
                ->hideOnForm(),
            AssociationField::new('attribute'),
            AssociationField::new('user')
                ->setFormTypeOption('choice_label', 'name'),
            TextField::new('value'),
        ];
    }

}
