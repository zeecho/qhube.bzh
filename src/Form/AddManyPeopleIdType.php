<?php

namespace App\Form;

use App\Entity\Nation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

class AddManyPeopleIdType extends AbstractType
{
    private EntityManagerInterface $entityManager;
    private TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wcaIds', TextareaType::class, [
                'label' => $this->translator->trans('admin.addpeople.list_of_wca_id'),
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'col-form-label input-group-text'
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('country', ChoiceType::class, [
                'choices' => $this->entityManager->getRepository(Nation::class)->findAllOrderedByTranslations(),
                'choice_label' => function ($c) {
                    return 'rankings.country_names.' . $c;
                },
                'attr' => ['class' => 'form-select mt-2'],
                'label_attr' => ['class' => 'form-label'],
                'label' => false,
                'placeholder' => 'rankings.country',
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light'],
                'label' => $this->translator->trans('crud.submit')
            ])
        ;
    }
}