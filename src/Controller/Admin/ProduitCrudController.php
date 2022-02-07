<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
       return [
            Field::new('id'),
            Field::new('nom'),
            TextEditorField::new('description'),
            Field::new('prix'),
            IntegerField::new('quantitee'),
            AssociationField::new('categorie'),
            AssociationField::new('fournisseur'),
            ImageField::new('image')
                ->setUploadDir('public/images/img-articles')
                ->setBasePath('images/img-articles'),
            Field::new('phare')
        ];
    }
    
}
