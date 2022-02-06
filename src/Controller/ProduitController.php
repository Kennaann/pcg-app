<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Fournisseur;
use App\Repository\AvisRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProduitController extends AbstractController
{
    private $em;
    private $repo;
    private $paginator;

    public function __construct(EntityManagerInterface $em, ProduitRepository $repo, PaginatorInterface $paginator)
    {
        $this->em = $em;
        $this->repo = $repo;
        $this->paginator = $paginator;
    }

    #[Route('/produits', name: 'produits')]
    public function index(): Response
    {
        $categories = $this->em->getRepository(Categorie::class)->findAll();

        return $this->render('produit/produits.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/produits/{id}', name: 'par_categories')]
    public function produitsParCategories($id, Request $request)
    {

        $fournisseurs = $this->em->getRepository(Fournisseur::class)->findAll();

        // requete vers la BDD
        $all_produits = $this->em->createQuery(
            'SELECT p
            FROM App\Entity\Produit p
            INNER JOIN p.categorie c 
            WHERE c.categorie =:id_cat'
        )
        ->setParameters(['id_cat'=> $id]);

        // paginator va executer la requête et afficher le résultat 
        $produits = $this->paginator->paginate(
            $all_produits,
            $request->query->getInt('page', 1),
            8
        );

        // si ma requête ne renvoie aucun résultats c'est que l'ID passé en url est invalide
        if(empty($produits->getItems())) {
            throw $this->createNotFoundException();
        }

        return $this->render('produit/par_categories.html.twig', [
            'produits' => $produits,
            'fournisseurs' => $fournisseurs,
            'categorie_id' => $id, 
            'filter_prix' => 50
        ]);
    }

    #[Route('/produits/{id}/filter', name: 'produits_filter')]
    public function produitsParCategoriesWithFilter($id, Request $request)
    {
        // recupère les données de mon formulaire de filtre
        if(array_key_exists('fournisseur', $_GET)) {
            $filter_fournisseurs = $_GET['fournisseur'];
        } else {
            $filter_fournisseurs = null;
        }

        $prix = $_GET['prix'];

        // requête vers la BDD
        if($filter_fournisseurs) {
            $all_produits = $this->em->createQuery(
                'SELECT p
                FROM App\Entity\Produit p
                INNER JOIN p.categorie c
                INNER JOIN p.fournisseur f
                WHERE f.fournisseur IN (:all_f) AND p.prix < :p AND c.categorie =:id_cat'
            )
            ->setParameters([
                'all_f' => $filter_fournisseurs,
                'p' => $prix,
                'id_cat'=> $id
            ]);
        } else {
            $all_produits = $this->em->createQuery(
                'SELECT p
                FROM App\Entity\Produit p
                INNER JOIN p.categorie c
                WHERE p.prix < :p AND c.categorie =:id_cat'
            )
            ->setParameters([
                'p' => $prix,
                'id_cat'=> $id
            ]);
        }

        $fournisseurs = $this->em->getRepository(Fournisseur::class)->findAll();

        $produits = $this->paginator->paginate(
            $all_produits,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('produit/par_categories.html.twig', [
            'produits' => $produits,
            'fournisseurs' => $fournisseurs,
            'categorie_id' => $id, 
            'filter_prix' => $prix
        ]);
    }

    #[Route('/produits/{categorie}/{id}', name: 'product_show')]
    public function productShow(Request $request, $id, $categorie, AvisRepository $avis_repo) 
    {
        $produit = $this->em->getRepository(Produit::class)->findBy(array("nom" => $id));

        $all_avis = $avis_repo->findByProductAndPublished($id);

        $avis = $this->paginator->paginate(
            $all_avis,
            $request->query->getInt('page', 1),
            4
        );

        
        
        // si ma requête ne renvoie aucun résultats c'est que le produit n'existe pas 
        if(!$produit) {
            throw $this->createNotFoundException();
        }
        
        return $this->render('produit/product_show.html.twig', [
            'produit' => $produit,
            'avis' => $avis,
            'categorie' => $categorie
        ]);
    }

    #[Route('/produits/{categorie}/{id}/avis', name: 'avis')]
    public function avis(Request $request, Security $security, $id, $categorie)
    {
        $produit = $this->em->getRepository(Produit::class)->findBy(array("nom" => $id))[0];


        if($request->request->get('commentaire')) {
            $avis = new Avis;
            $avis->setTitre($request->request->get('titre'));
            $avis->setAvis($request->request->get('commentaire'));
            $avis->setNote($request->request->get('note'));
            $avis->setPublished(0);
            $avis->setProduit($produit);
            $avis->setUser($security->getUser());

            $this->em->persist($avis);
            $this->em->flush();
            
            $this->addFlash('success', 'Votre commentaire sera revu par l\'administrateur du site avant d\'être publié.' );
        }

        return $this->redirectToRoute('product_show', [
            'categorie' => $categorie,
            'id' => $id
        ]);
    }

    #[Route('/test', name: 'test')]
    public function test()
    {

    }
}