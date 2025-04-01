<?php

namespace App\Form;

use App\Entity\Nation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlagsPdfType extends AbstractType
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
        $formats = [
            'A4' => 'A4',
            'US letter' => 'Letter'
        ];

        $flagSizes = [
                        'A bit of everything between 12mm and 28mm' => 'all',
        ];
        for ($i = 12; $i <= 30; $i = $i+2) {
            $flagSizes[$i . 'mm'] = $i . 'mm';
        }
        $flagSizes['35mm'] = '35mm';
        $flagSizes['40mm'] = '40mm';

        $flagsCustom = [
            'Catalunya (Estelada blava)' => 'cat-estelada_blava',
            'Catalunya (Estelada vermella)' => 'cat-estelada_vermella',
            'Occitània (sens l\'estela)' => 'occ-sens_l_estela',
        ];

        $flags = $this->entityManager->getRepository(Nation::class)->findAllOrderedByOriginalNames();
        $flags = array_merge($flags, $flagsCustom);
        ksort($flags);

        $builder
            ->add('flag', ChoiceType::class, [
                'choices' => $flags,
                'label' => $this->translator->trans('flag_generator.flag'),
            ])
            ->add('flagWidth', ChoiceType::class, [
                'choices' => $flagSizes,
                'label' => $this->translator->trans('flag_generator.flag_width'),
            ])
            ->add('format', ChoiceType::class, [
                'choices' => $formats,
                'label' => $this->translator->trans('flag_generator.format'),
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light'],
                'label' => $this->translator->trans('flag_generator.submit_button'),
            ])
        ;
    }
}