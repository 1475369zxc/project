<?php

namespace App\Controller\Admin;

use App\Entity\Attribute;
use App\Enum\AttributeType;
use App\Form\AttributeOptionType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class AttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Attribute::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addJsFile('js/admin/attribute/selector.js');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description'),
            AssociationField::new('category'),
            ChoiceField::new('type')
                ->setFormType(EnumType::class)
                ->setFormTypeOption('class', AttributeType::class)
                ->setFormTypeOption('choice_label', fn(AttributeType $type) => $type->label()),
            BooleanField::new('isMultiple')
                ->setLabel('Multiple choice')
                ->renderAsSwitch(false)
                ->addCssClass('field-is-multiple')
                ->onlyOnForms(),
            CollectionField::new('options')
                ->onlyOnForms()
                ->allowAdd()
                ->allowDelete()
                ->setEntryType(AttributeOptionType::class)
                ->setFormTypeOption('by_reference', false),

        ];
    }

}
