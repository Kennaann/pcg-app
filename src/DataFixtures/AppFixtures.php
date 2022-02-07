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

        $pcImages = ["pc1.jpg", "pc2.jpg", "pc3.jpg", "pc4.jpg"];
        $ecranImages = ["ecran1.jpg", "ecran2.jpg", "ecran3.jpg", "ecran4.jpg"];
        $clavierImages = ["clavier1.jpg", "clavier2.jpg", "clavier3.jpg", "clavier4.jpg", "clavier5.jpg"];
        $sourisImages = ["souris1.jpg", "souris2.jpg", "souris3.jpg", "souris4.jpg", "souris5.jpg"];
        $casqueImages = ["casque1.jpg", "casque2.jpg", "casque3.jpg", "casque4.jpg"];

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

        // 20 PC
        for($i = 1; $i <= 20; $i++) {
            $k = $i - 1;

            $produit = new Produit();

            $produit->setNom('PC n°'.$i);
            $produit->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium');
            $produit->setQuantitee(rand(0, 10));
            $produit->setImage($pcImages[array_rand($pcImages, 1)]);

            // permet de recuperer une valeur random avec deux décimales
            $produit->setPrix(mt_rand(500.00 * $div, 4999.99 * $div) / $div);

            // Récupère l'index 1 du tableau catégorie 
            $produit->setCategorie($categories[0]);
            $produit->setFournisseur($fournisseurs[array_rand($fournisseurs, 1)]);

            $manager->persist($produit);
            // $manager->persist($categories[array_rand($categories, 1)]);
            // $manager->persist($fournisseurs[array_rand($fournisseurs, 1)]);
        }

        // 20 Ecran
        for($i = 1; $i <= 20; $i++) {
            $k = $i - 1;

            $produit = new Produit();

            $produit->setNom('Ecran n°'.$i);
            $produit->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium');
            $produit->setQuantitee(rand(0, 10));
            $produit->setImage($ecranImages[array_rand($ecranImages, 1)]);

            // permet de recuperer une valeur random avec deux décimales
            $produit->setPrix(mt_rand(50.00 * $div, 499.99 * $div) / $div);

            // Récupère l'index 2 du tableau catégorie 
            $produit->setCategorie($categories[1]);
            $produit->setFournisseur($fournisseurs[array_rand($fournisseurs, 1)]);

            $manager->persist($produit);
            // $manager->persist($categories[array_rand($categories, 1)]);
            // $manager->persist($fournisseurs[array_rand($fournisseurs, 1)]);
        }

        // 20 Claviers
        for($i = 1; $i <= 20; $i++) {
            $k = $i - 1;

            $produit = new Produit();

            $produit->setNom('Clavier n°'.$i);
            $produit->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium');
            $produit->setQuantitee(rand(0, 10));
            $produit->setImage($clavierImages[array_rand($clavierImages, 1)]);

            // permet de recuperer une valeur random avec deux décimales
            $produit->setPrix(mt_rand(50.00 * $div, 499.99 * $div) / $div);

            // Récupère l'index 1 du tableau catégorie 
            $produit->setCategorie($categories[2]);
            $produit->setFournisseur($fournisseurs[array_rand($fournisseurs, 1)]);

            $manager->persist($produit);
            // $manager->persist($categories[array_rand($categories, 1)]);
            // $manager->persist($fournisseurs[array_rand($fournisseurs, 1)]);
        }

        // 20 Souris
        for($i = 1; $i <= 20; $i++) {
            $k = $i - 1;

            $produit = new Produit();

            $produit->setNom('Souris n°'.$i);
            $produit->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium');
            $produit->setQuantitee(rand(0, 10));
            $produit->setImage($sourisImages[array_rand($sourisImages, 1)]);

            // permet de recuperer une valeur random avec deux décimales
            $produit->setPrix(mt_rand(50.00 * $div, 499.99 * $div) / $div);

            // Récupère l'index 2 du tableau catégorie 
            $produit->setCategorie($categories[3]);
            $produit->setFournisseur($fournisseurs[array_rand($fournisseurs, 1)]);

            $manager->persist($produit);
            // $manager->persist($categories[array_rand($categories, 1)]);
            // $manager->persist($fournisseurs[array_rand($fournisseurs, 1)]);
        }

        // 20 Casques
        for($i = 1; $i <= 20; $i++) {
            $k = $i - 1;

            $produit = new Produit();

            $produit->setNom('Casque n°'.$i);
            $produit->setDescription('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium');
            $produit->setQuantitee(rand(0, 10));
            $produit->setImage($casqueImages[array_rand($casqueImages, 1)]);

            // permet de recuperer une valeur random avec deux décimales
            $produit->setPrix(mt_rand(20.00 * $div, 299.99 * $div) / $div);

            // Récupère l'index 2 du tableau catégorie 
            $produit->setCategorie($categories[4]);
            $produit->setFournisseur($fournisseurs[array_rand($fournisseurs, 1)]);

            $manager->persist($produit);
            // $manager->persist($categories[array_rand($categories, 1)]);
            // $manager->persist($fournisseurs[array_rand($fournisseurs, 1)]);
        }

        $manager->flush();
    }
}
