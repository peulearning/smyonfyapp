<?php

namespace App\Form;

use App\Entity\Bovinos;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BovinosType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('codigo', IntegerType::class, [
				'label' => 'Código do gado: ',
				'attr' => ['placeholder' => 'Codigo']
			])
			->add('leite', NumberType::class, [
				'label' => 'Leite Semanal: ',
				'attr' => ['placeholder' => 'Litros'],
				'invalid_message' => 'Insira um número válido'
			])
			->add('racao', NumberType::class, [
				'label' => 'Ração Semanal: ',
				'attr' => ['placeholder' => 'Quilos'],
				'invalid_message' => 'Insira um número válido'
			])
			->add('peso', NumberType::class, [
				'label' => 'Peso em KG: ',
				'attr' => ['placeholder' => 'Quilos'],
				'invalid_message' => 'Insira um número válido'
			])
			->add('data_nascimento', DateType::class, [
				'label' => 'Data de Nascimento',
				'html5' => false,
				'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker', 'placeholder' => 'dia/mes/ano'],
                'constraints' => [
                	new Assert\LessThanOrEqual([
                		'value' => 'today',
                		'message' => 'Data Inválida'
                	])
                ]
            ])
            ->add('data_abatimento', DateType::class, [
                'label' => 'Data de Abatimento',
                'html5'=> false,
                'format' => 'dd/MM/yyyy',
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datapicker', 'placeholder' => 'dia/mes/ano'],
                'constraints' => [
                    new Assert\LessThanOrEqual ([
                        'value' => 'today',
                        'message' => 'Data Inválida'
                    ])
                ]


			])
			->add('Salvar', SubmitType::class);
	}

}