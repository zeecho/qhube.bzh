<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Nation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankingsType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $eventsForForm = $this->entityManager->getRepository(Event::class)->getEventsForForm();

        $builder
            ->add('country', ChoiceType::class, [
                'choices' => $this->entityManager->getRepository(Nation::class)->findAllOrderedByTranslations(),
                'choice_label' => function ($c, $key, $value) {
                    return 'rankings.country_names.' . $c;
                },
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'rankings.country',
                'placeholder' => '-',
                'data' => $options['country'],
            ])
            ->add('event', ChoiceType::class, [
                'choices' => $eventsForForm,
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'rankings.event',
                'choice_label' => function ($eventLabel, $key, $value) {
                    return 'rankings.events.' . $eventLabel;
                },
                'placeholder' => '-',
                'data' => $options['event']
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'rankings.single' => 'single',
                    'rankings.average' => 'average'
                ],
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'rankings.type',
                'placeholder' => '-',
                'data' => $options['type']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'country' => null,
            'event' => null,
            'type' => null,
        ]);
    }
}