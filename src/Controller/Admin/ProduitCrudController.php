<?php

namespace App\Controller\Admin;

use DateTime;
use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\photoField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre'),
            ColorField::new('couleur'),
            ChoiceField::new('taille')
            ->onlyOnForms()
            ->setFormType(ChoiceType::class)
            ->setFormTypeOption('choices', [
             'S' => 'S',
             'M' => 'M',
             'L' => 'L',
             'XL' => 'XL',
             'XXL' => 'XXL',
             'XXXL' => 'XXXL',
    ])
    ->setRequired(true),
            ChoiceField::new('collection')
            ->onlyOnForms()
            ->setFormType(ChoiceType::class)
            ->setFormTypeOption('choices', [
              'Homme' => 'Homme',
              'Femme' => 'Femme',
              'Enfant garçon' => 'Enfant garçon',
              'Enfant fille' => 'Enfant fille',
              'Unisexe' => 'Unisexe',
            ])
             ->setRequired(true),
             ArrayField::new('collection')
             ->hideOnForm()
             ->formatValue(function ($value) {
                 if (is_array($value)) {
                     return implode(', ', $value);
                 }
                 return $value;
             }),
            ImageField::new('photo')->setBasePath('uploads/photos')->setUploadDir('public/uploads/photos')->setUploadedFileNamePattern('[slug]-[timestamp].[extension]'),
            MoneyField::new('prix')->setCurrency('EUR'),
            NumberField::new('stock'),
            TextEditorField::new('description')->onlyOnForms(),
            DateTimeField::new('dateEnregistrement')->setFormat('d/M/Y à H:m:s')->onlyWhenCreating()->hideOnForm(),
            DateTimeField::new('dateEnregistrement')->setFormat('d/M/Y à H:m:s')->onlyOnIndex()->hideOnForm(),
           
        ];
    }

    public function createEntity(string $entityFqcn)
    {
        $produit= new $entityFqcn;
        $produit->setDateEnregistrement(new DateTime);
        return $produit;
    }
    
    
}
