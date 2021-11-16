<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\CategorieRepository;
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
    private $avisRepository;
    private $categorieRepository;
    private $produitRepository;
    private $fournisseurRepository;
    private $userRepository;

    public function __construct(AvisRepository $avisRepository, CategorieRepository $categorieRepository, ProduitRepository $produitRepository, FournisseurRepository $fournisseurRepository, UserRepository $userRepository) {
        $this->avisRepository = $avisRepository;
        $this->categorieRepository = $categorieRepository;
        $this->produitRepository = $produitRepository;
        $this->fournisseurRepository = $fournisseurRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundle/welcome.html.twig', [
            'nb_avis' => count($this->avisRepository->findAll()),
            'nb_categorie' => count($this->categorieRepository->findAll()),
            'nb_produit' => count($this->produitRepository->findAll()),
            'nb_fournisseur' => count($this->fournisseurRepository->findAll()),
            'nb_user' => count($this->userRepository->findAll())
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Pcg App');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
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
