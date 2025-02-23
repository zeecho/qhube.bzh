<?php

namespace App\Form;

use App\Entity\Nation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class JoinRankingsType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('country', ChoiceType::class, [
                'choices' => $this->entityManager->getRepository(Nation::class)->findAllOrderedByTranslations(),
//                'choice_value' => 'short',
                'choice_label' => function ($c) {
                    return 'rankings.country_names.' . $c;
                },
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => false,
                'placeholder' => 'rankings.country',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light']
            ])
        ;
    }
}