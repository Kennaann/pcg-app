<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\Commande;
use Stripe\Checkout\Session;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository)
    {
        $this->session = $session;
        $this->produitRepository = $produitRepository;
    }

    #[Route('/panier', name: 'cart')]
    public function index(): Response
    {   
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->produitRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;
        $panierLength = 0;

        foreach($panierWithData as $item) {
            $totalItem = $item['product']->getPrix() * $item['quantity'];
            $total += $totalItem;

            $panierLength += $item['quantity'];
        }

        return $this->render('cart/index.html.twig', [
            'item' => $panierWithData,
            'total' => $total, 
            'length' => $panierLength
        ]);
    }

    
    #[Route('/panier/add/{id}', name: 'cart_add')]
    public function cartAdd($id, Request $request)
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        } 

        $this->session->set('panier', $panier);

        $this->addFlash('panier', "L'article a bien été ajouté a votre panier !" );

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    
    #[Route('/panier/remove/{id}', name: 'cart_remove')]
    public function cartRemove($id)
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $this->session->set('panier', $panier);

        return $this->redirectToRoute('cart');
    }

    
    #[Route('/paiement', name: 'checkout')]
    public function checkout()
    {
        // récupère le panier de la session
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->produitRepository->find($id),
                'quantity' => $quantity
            ];
        }

        // tableau de données stripe pour redirection vers leur page 
        $line_item_array = [];

        foreach($panierWithData as $k => [
            'product' => $product,
            'quantity' => $quantity
        ]) {
            $line_item_array[] = [
                'price_data' => [
                    'currency' => 'eur', 
                    'product_data' => [
                        'name' => $product->getNom()
                    ],
                    'unit_amount' => $product->getPrix() * 100,
                ],
                'quantity' => $quantity
            ];
        };


        Stripe::setApiKey('sk_test_51Jqzw4FTwbha1cWjtvbt9PUucY5PVl1B2tjJh3hmdyLtJrx2N7m661X3ijOrnQrt3MGeK3lkGWx9y3aQzDjFxae600uh5PgaBK');

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_item_array,
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        
        return $this->redirect($checkout_session->url, 303);
    }
    
    #[Route('/success', name: 'success_url')]
    public function success(EntityManagerInterface $em, Security $security, )
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier)) {

            if($security->getUser() !== null) {
                $commande = New Commande();
                $commande->setUser($security->getUser());
                $commande->setCreatedAt(new DateTimeImmutable());

                // Récupère dans mon panier les produits à ajouter à mon object "commande"
                foreach($panier as $id => $quantity) {
                    $commande->addProduit($this->produitRepository->find($id));
                }

                $em->persist($commande);
                $em->flush();
            }

            // Vide mon panier
            foreach($panier as $id => $quantity) {
                unset($panier[$id]);
            }
        }

        $this->session->set('panier', $panier);
        $this->addFlash('success', "Transaction réussie ! Merci de votre confiance." );

        return $this->redirectToRoute('home_page', []);
    }
    
    #[Route('/cancel', name: 'cancel_url')]
    public function cancel()
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->produitRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;
        $panierLength = 0;

        foreach($panierWithData as $item) {
            $totalItem = $item['product']->getPrix() * $item['quantity'];
            $total += $totalItem;

            $panierLength += $item['quantity'];
        }

        $this->addFlash('fail', "Transaction échouée. Veuillez réessayer." );

        return $this->redirectToRoute('cart', [
            'item' => $panierWithData,
            'total' => $total, 
            'length' => $panierLength
        ]);
    }
}
