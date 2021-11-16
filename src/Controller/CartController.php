<?php

namespace App\Controller;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartController extends AbstractController
{

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    #[Route('/panier', name: 'cart')]
    public function index(ProduitRepository $produitRepository): Response
    {

        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
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
    public function checkout(ProduitRepository $produitRepository)
    {
        // récupère le panier de la session
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
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
    public function success()
    {
        $panier = $this->session->get('panier', []);

        if(!empty($panier)) {
            foreach($panier as $k => $item) {
                unset($panier[$k]);
            }
        }

        $this->session->set('panier', $panier);
        $this->addFlash('success', "Transaction réussie ! Merci de votre confiance." );

        return $this->redirectToRoute('home_page', []);
    }
    
    #[Route('/cancel', name: 'cancel_url')]
    public function cancel(ProduitRepository $produitRepository)
    {
        $panier = $this->session->get('panier', []);
        $panierWithData = [];

        foreach($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $produitRepository->find($id),
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
