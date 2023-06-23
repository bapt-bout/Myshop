<?php 


namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Dumper\QtFileDumper;

class CartService
{

    private $entityManager;
    private $repo;
    private $rs;

    public function __construct(ProduitRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this->repo = $repo;
    }

    public function add($id)
    {
        $session = $this->rs->getSession();

        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);
        

        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        } else {
            $cart[$id] = 1;
            $qt++;
        }
        

        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);
        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }
        if($qt < 0)
        {
            $qt = 0;
        }
        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function cartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart', []);
        // $qt = $session->get('qt');
        
        // unset($cart['22']);
        // unset($qt);
       
        $cartWithdata = [];

        foreach($cart as $id => $quantity)
        {
            $produit = $this->repo->find($id);
            $cartWithdata[] = [
                'produit' =>  $produit,
                'quantity' => $quantity
            ];
        }

        return $cartWithdata;
    }

    public function montant()
    {
        $cartWithdata = $this->cartWithData();
        $montant = 0;   

        foreach($cartWithdata as $item)
        {
            $montantItem = $item['produit']->getPrix() * $item['quantity'];
            $montant += $montantItem;
        }

        return $montant;
    }

    public function clearCart()
    {
        $session = $this->rs->getSession();
        $session->remove('cart');
        $session->remove('qt');
    }


    
}
