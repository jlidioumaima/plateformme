<?php

namespace App\Form;
use App\Entity\Pays;
use App\Entity\Croisiere;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class CroisiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
           
            ->add('inclus')
            ->add('Non_Inclus')

            ->add('agence')
            ->add('hotel')
            ->add('pays',EntityType::class,array('class' => Pays::class, 'choice_label' => 'nom',
            'multiple'  => true,))
       
            ->add('images', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Croisiere::class,
        ]);
    }
}
