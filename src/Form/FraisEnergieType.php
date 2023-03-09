<?php

namespace App\Form;

use App\Entity\FraisEnergie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class FraisEnergieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('taux_Energie', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('date', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('membre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FraisEnergie::class,
        ]);
    }
}
