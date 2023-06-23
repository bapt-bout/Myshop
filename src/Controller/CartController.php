<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Service\CartService;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    private $cartService;
    private $entityManager;

    public function __construct(CartService $cartService, EntityManagerInterface $entityManager)
    {
        $this->cartService = $cartService;
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(ProduitRepository $repo, CartService $cs): Response
    {
        $cartWithdata = $cs->cartWithData();
        

        $montant = $cs->montant();
        $colorMap = [
            '#000000' => 'Noir',
            '#808080' => 'Gris',
            '#A9A9A9' => 'Gris foncé',
            '#C0C0C0' => 'Argent',
            '#D3D3D3' => 'Gris clair',
            '#F5F5F5' => 'Gris très clair',
            '#8B4513' => 'Brun',
            '#A52A2A' => 'Brun rouille',
            '#DEB887' => 'Brun clair',
            '#FFF8DC' => 'Blanc antique',
            '#000080' => 'Bleu marine',
            '#0000CD' => 'Bleu moyen',
            '#4169E1' => 'Bleu royal',
            '#6495ED' => 'Bleu ciel',
            '#87CEEB' => 'Bleu clair',
            '#ADD8E6' => 'Bleu pâle',
            '#FFFF00' => 'Jaune',
            '#FFD700' => 'Or',
            '#FFA500' => 'Orange',
            '#FF4500' => 'Orange rougeâtre',
            '#FF0000' => 'Rouge',
            '#800080' => 'Violet',
            '#FFC0CB' => 'Rose',
            '#8B008B' => 'Violet foncé',
            '#FF69B4' => 'Rose vif',
            '#228B22' => 'Vert forestier',
            '#008000' => 'Vert',
            '#32CD32' => 'Vert lime',
            '#00FF00' => 'Vert clair',
            '#90EE90' => 'Vert pâle',
            '#FFFFFF' => 'Blanc',
        ];
    

        foreach ($cartWithdata as &$item) {
            $produit = $item['produit'];
            $couleur = $produit->getCouleur();
    
            // Vérifier si la couleur existe dans le tableau de correspondance
            if (isset($colorMap[$couleur])) {
                $item['produit']->setCouleur($colorMap[$couleur]);
            }
        }
    
        return $this->render('cart/index.html.twig', [
            'items' => $cartWithdata,
            'montant' => $montant
        ]);
    }

    #[Route('/cart/add/{id}', name:'cart_add')]
    public function add($id, CartService $cs)
    {
        $this->cartService->add($id);
        
        // dd($session->get('cart'));
        return $this->redirectToRoute('app_produit');

       
    }

    #[Route('/cart/remove/{id}', name:'cart_remove')]
    public function remove($id, CartService $cs) : Response
    {
        $cs->remove($id);
        return $this->redirectToRoute('app_cart');
    }


    #[Route('/cart/validation', name: 'validation_commande')]
    public function cartCommande(EntityManagerInterface $manager, Request $request, Commande $commande = null, CartService $cs, ProduitRepository $repo): Response {
        $cartWithData = $cs->cartWithData();
        $total = $cs->montant();
        $errors = [];
    
        foreach ($cartWithData as $item) {
            $produit = $repo->find($item['produit']->getId());
            $quantiteCommandee = $item['quantity'];
    
    
            $montant = $produit->getPrix() * $quantiteCommandee;
    
            $commande = new Commande();
            $commande
                ->setIdMembre($this->getUser())
                ->setIdProduit($produit)
                ->setQuantity($quantiteCommandee)
                ->setMontant($montant)
                ->setEtat('En cours de traitement')
                ->setDateEnregistrement(new \DateTime());
    
            $manager->persist($produit);
            $manager->persist($commande);
            }
    
            $manager->flush();
    
            $cs->clearCart();
    
            return $this->redirectToRoute('mon_compte_commandes');
        }

}