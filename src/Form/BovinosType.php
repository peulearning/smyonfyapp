<?php

namespace App\Form;

use App\Entity\Bovinos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BovinosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo')
            ->add('leite')
            ->add('racao')
            ->add('peso')
            ->add('data_nascimento')
            ->add('data_abatimento')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bovinos::class,
        ]);
    }
}
