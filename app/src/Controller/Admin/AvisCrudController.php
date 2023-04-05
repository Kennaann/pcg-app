<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AvisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Avis::class;
    }

   
    public function configureFields(string $pageName): iterable
    {
        return [
            Field::new('note'),
            Field::new('titre'),
            TextEditorField::new('avis'),
            Field::new('published')
        ];
    }
}
