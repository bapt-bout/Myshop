<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('couleur')
            ->add('taille')
            ->add('collection')
            ->add('photo', FileType::class, [
                'label' => 'photo',

                
                'mapped' => false,

              
              
                'required' => false,

           
              
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'photo/jpeg',
                            'photo/png',
                            'photo/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez mettre une image conforme',
                    ])
                ],
            ])
            ->add('prix')
            ->add('stock')
            ->add('date_enregistrement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
