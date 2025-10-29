<?php
// src/Controller/Admin/ActivityCrudController.php

namespace App\Controller\Admin;

use App\Entity\Activity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class ActivityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Activity::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield TextField::new('location', 'Lieu')->hideOnIndex();
        yield TextareaField::new('description')->renderAsHtml(false);
        yield ArrayField::new('benefits', 'Bénéfices')->hideOnIndex();
        yield TextField::new('image', 'Image (fichier)');
        yield TextField::new('buttonText', 'Texte du bouton')->hideOnIndex();
        yield TextField::new('slug');
        yield IntegerField::new('position')->setHelp('Ordre d\'affichage (croissant)');
        yield BooleanField::new('isActive', 'Actif');
    }
}