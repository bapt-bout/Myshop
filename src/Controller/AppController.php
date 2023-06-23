<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ArticleRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;




class AppController extends AbstractController
{

    private $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        $this->commandeRepository = $commandeRepository;
    }

    #[Route('/mon-compte/commandes', name: 'mon_compte_commandes')]
    public function commandes(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $commandes = $commandeRepository->findBy(['id_membre' => $user]);
    
        return $this->render('cart/commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }


    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig');
    }

    #[Route('/produit', name: 'app_produit')]
    public function produit(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('produit/index.html.twig', [
            'lesProduits' => $produits
        ]);
    }

    #[Route('/produit/add', name: 'produit_add')]
    public function ajouter(EntityManagerInterface $manager, Request $request, SluggerInterface $slugger): Response
    {
        $produit = new Produit;

        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('photo_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                  
                }

                
                $produit->setPhoto($newFilename);
            } 
                $manager->persist($produit);
                $manager->flush();
                return $this->redirectToRoute('app_produit');
           
       
        }

        return $this->render('produit/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/produit/show/{id}", name: "produit_show")]
    public function show(Produit $produit =null) :Response
    {
        if($produit == null)
        {
            return $this->redirectToRoute('home');
        }
        return $this->render('app/show.html.twig', [
            'produit' => $produit
        ]);
    }

}
