<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {

        $div = 10**2; 

        $categorie_table = ["PC", "Ecran", "Clavier", "Souris", "Audio"];
        $categories = [];

        $fournisseur_table = ["PCG", "Logitech", "Razer", "MSI", "SteelSeries"];
        $fournisseurs= [];

        
        //fixtures de categorie
        for($i = 0; $i < sizeof($categorie_table); $i++) {
            $categorie = new Categorie();
            $categorie->setCategorie($categorie_table[$i]);
            array_push($categories, $categorie);
            $manager->persist($categorie);
        }
        

         //fixtures de fournisseur
         for($i = 0; $i < sizeof($fournisseur_table); $i++) {
            $fournisseur = new Fournisseur();
            $fournisseur->setFournisseur($fournisseur_table[$i]);
            array_push($fournisseurs, $fournisseur);
            $manager->persist($fournisseur);
        }
        

        //fixtures des produits//
        for($i = 1; $i <= 100; $i++) {
            $k = $i - 1;

            $produit = new Produit();

            $produit->setNom('Produit n°'.$i);
            $produit->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium');
            $produit->setQuantitee(rand(0, 50));
            $produit->setImage('default-product-image.png');

            //permet de recuperer une valeur random avec deux décimales
            $produit->setPrix(mt_rand(10.00 * $div, 99.99 * $div) / $div);

            //recupère au hasard une valeur dans mes tableaux contenant les noms des categorie et des fournisseurs
            $produit->setCategorie($categories[array_rand($categories, 1)]);
            $produit->setFournisseur($fournisseurs[array_rand($fournisseurs, 1)]);

            $manager->persist($produit);
            $manager->persist($categories[array_rand($categories, 1)]);
            $manager->persist($fournisseurs[array_rand($fournisseurs, 1)]);
        }

        $manager->flush();
    }
}
