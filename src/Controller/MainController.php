<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    private $requestStack;
    private $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    #[Route('/', name: 'home_page')]
    public function index(): Response
    {

        $categories = $this->em->getRepository(Categorie::class)->findAll();
        $session = $this->requestStack->getSession();
        $session->set('categories', $categories);

        // Il ne peut y avoir qu'un seul produit "phare". 
        // Si il y n'y en a aucun, le premier article de la table produit sera affiché. 
        // Si il y a plusieurs produits "phare", le premier produit par ordre d'ID sera affiché.
        // Si aucun produit n'est mis en vente dans votre site, votre page d'accueil sera vide.  

        $produitPhare = $this->em->getRepository(Produit::class)->findBy(array("phare" => true));
        $defaultProduit = $this->em->getRepository(Produit::class)->findAll()[0];

        if($produitPhare) {
            $produitPhare = $produitPhare[0];
        } else {
            $produitPhare = $defaultProduit;
        }

        return $this->render('main/index.html.twig', [
            'produit_phare' => $produitPhare
        ]);
    }
    
    #[Route('/about', name: 'about')]
    public function about()
    {
        return $this->render('main/about.html.twig', []);
    }
    
    #[Route('/commandes', name: 'commandes')]
    public function commandes(Security $security)
    {
        //Récupère l'utilisateur connecté
        $user = $security->getUser();

        if($user) {
            // Récupère la collection de commandes
            $commandes = $user->getCommandes();
        } else {
            $commandes = null;
        }
        

        return $this->render('user/commandes.html.twig', [
            'commandes' => $commandes
        ]);
    }
}
