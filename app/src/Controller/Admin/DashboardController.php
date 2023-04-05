<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\FournisseurRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private $produitRepository;
    private $userRepository;
    private $commandeRepository;

    public function __construct(ProduitRepository $produitRepository, UserRepository $userRepository, CommandeRepository $commandeRepository) {
        $this->produitRepository = $produitRepository;
        $this->userRepository = $userRepository;
        $this->commandeRepository = $commandeRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        // nombre de commandes par dates
        $commandesByDate = $this->commandeRepository->countByDate();
        // $commandesByDate = null;

        if($commandesByDate) {
            $dates = [];
            $commandesCount = [];

            // on sépare les données tel qu'attendu par chart.js
            foreach($commandesByDate as $commande) {
                $date = date('d/m/Y', strtotime($commande['dateCommande']));
                $dates[] = $date;
                $commandesCount[] = $commande['count'];
            }
        }
    
        // On récupère les produits déja commandés 
        $produitsRaw = $this->produitRepository->getBuyedProduct();
        // $produitsRaw = null;

        if($produitsRaw) {
            $produits = [];
            $limit = 5;
            
            // Déconstruit mon tableau
            foreach($produitsRaw as $produit) {
                $produits[] = $produit->getNom();
            }

            // Tableau avec le Produit en clé et la quantitée en valeur, dans l'ordre decroissant par rapport à la valeur
            $allProduitAndCount = array_count_values($produits);
            arsort($allProduitAndCount);

            // envoie toutes les données de mon tableau jusqu'a une certaine limite
            if($allProduitAndCount < $limit) {
                $produitAndCount = $allProduitAndCount;
            } else {
                // je récupère les 5 premieres valeures de mon tableau
                $produitAndCount = array_slice($allProduitAndCount, 0, $limit);
            }

            $produitsNoms = [];
            $quantites = [];

            foreach($produitAndCount as $produit =>$quantitee) {
                $produitsNoms[] = $produit;
                $quantites[] = $quantitee;
            }
        }


        return $this->render('bundles/EasyAdminBundle/welcome.html.twig', [
            'nb_commandes' => $this->commandeRepository->countCommandes(),
            'nb_produit' => $this->produitRepository->countProduits(),
            'nb_user' => $this->userRepository->countUsers(), 

            
            // "if" condition sur une ligne : si il y a des commandes ou produits en vente j'envoie mes données vers la vue

            'limite' => $produitsRaw ? $limit : null,
            // Envoi mon tableau au format json
            'dates' => $commandesByDate ? json_encode($dates) : null,
            'commandesCount' => $commandesByDate ? json_encode($commandesCount) : null,
            'produitsNoms' => $produitsRaw ? json_encode($produitsNoms) : null,
            'quantites' => $produitsRaw ? json_encode($quantites) : null
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pcg App');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard'),
            MenuItem::linkToUrl('Site', 'fa fa-home', '/'),

            MenuItem::section('CRUD'),
                MenuItem::linkToCrud('Avis', 'fa fa-comment', Avis::class), 
                MenuItem::linkToCrud('Produits', 'fa fa-tags', Produit::class), 
                MenuItem::linkToCrud('Categorie', 'fa fa-box', Categorie::class),
                MenuItem::linkToCrud('Fournisseur', 'fa fa-truck', Fournisseur::class),
                MenuItem::linkToCrud('User', 'fa fa-user', User::class)
        ];

    }
}
