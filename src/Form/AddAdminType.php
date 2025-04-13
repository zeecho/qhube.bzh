<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = [
            'Admin' => 'ROLE_ADMIN',
            'CDF' => 'ROLE_CDF',
        ];

        $builder
            ->add('wcaId', TextType::class, [
                'label' => 'WCA ID',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Role',
                'choices' => $roles,
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light']
            ])
        ;
    }
}