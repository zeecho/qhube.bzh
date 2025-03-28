<?php

namespace App\Form;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Languages;
use Symfony\Contracts\Translation\TranslatorInterface;

class TerlaterieSearchType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $finder = new Finder();
        $languageFile = $finder->in('../data/translation_sources/')->files()->name('languages.txt');
        $languagesFromFile = [];
        foreach ($languageFile as $file) {
            $contents = $file->getContents();
            $languagesFromFile = explode("\n", $contents);
            break;
        }

        $customLanguageList = [
            'anj' => 'anjevin',
            'es' => 'castellano / español',
            'oc-gas' => 'Occitan gascon',
            'oc-pro' => 'Occitan provençau',
            'pt_BR' => 'Português do Brasil',
            'tnt' => 'Trentin',
        ];
        $languages = [];
        foreach ($languagesFromFile as $language) {
            $language = str_replace('-', '_', $language);

            if ($language) {
                if (array_key_exists($language, $customLanguageList)) {
                    $languageName = $customLanguageList[$language];
                } else {
                    try {
                        $languageName = Languages::getName($language, $language);
                    } catch (MissingResourceException $e) {
                        $languageName = $language;
                    }
                }
                $languages[$languageName] = $language;
            }
        }

        ksort($languages, SORT_FLAG_CASE + SORT_STRING);

        $builder
            ->add('searchString', TextType::class, [
                'attr' => [
                    'placeholder' => $this->translator->trans('translation.search.form.search_string'),
                ],
                'label' => $this->translator->trans('translation.search.form.search_string'),
                'label_attr' => ['class' => 'form-title' ]
            ])
            ->add('searchLanguage', ChoiceType::class, [
                'choices' => $languages,
                'label' => $this->translator->trans('translation.search.form.search_language'),
                'label_attr' => ['class' => 'form-title' ],
//                'empty_data' => 'en',
                'preferred_choices' => ['en'],
            ])
            ->add('targetLanguages', ChoiceType::class, [
                'choices' => $languages,
                'label' => $this->translator->trans('translation.search.form.target_language'),
                'expanded' => true,
                'multiple' => true,
                'label_attr' => ['class' => 'no-padding-top']
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light'],
                'label' => $this->translator->trans('translation.search_button'),
            ])
        ;
    }
}