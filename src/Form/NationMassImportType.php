<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NationMassImportType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $formatTitleString = $this->translator->trans('country.mass_import.format.title');
        $formatExplanationString = $this->translator->trans('country.mass_import.format.explanations');
        $egString = $this->translator->trans('country.mass_import.format.eg');
        $exampleString = "Jèrri	jey	jey.svg	Jersey	Jersey	Ĵerzejo	Jerzenez\nGuernési	ggy	ggy.svg	Guernesey	Guernsey	Gernezejo	Gwernenez";
        $builder
            ->add('tsv', TextareaType::class, [
                'label' => "TSV",
                'attr' => [
                    'placeholder' => $formatTitleString . "\n" . $formatExplanationString . "\n\n" . $egString . "\n" . $exampleString,
                    'sanitize_html' => true
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light'],
            ]);
    }
}
