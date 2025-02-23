<?php

namespace App\Form;

use App\Entity\Nation;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PeopleIdType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\d{4}[A-Z]{4}\d{2}$/',
                        'message' => 'The WCA ID must follow the format YYYYXXXX00 (e.g., 2007MOMO01).'
                    ])
                ],
            ])
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

//    public function createAddPeopleIdForm($entityManager)
//    {
//        $form = $this->createFormBuilder()
//            ->getForm();
//
//        return $form;
//    }
}