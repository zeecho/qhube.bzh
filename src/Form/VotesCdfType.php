<?php

namespace App\Form;

use App\Entity\VotesCdf;
use App\Service\TranslationDataGenerator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class VotesCdfType extends AbstractType
{
    private TranslatorInterface $translator;
    private CacheInterface $cache;

    public function __construct(TranslatorInterface $translator, CacheInterface $cache)
    {
        $this->translator = $translator;
        $this->cache = $cache;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $registrations = $this->getRegistrationData();

        $registrationsByEvent = [];
        foreach ($registrations as $key => $registrationEvent) {
            unset($registrationEvent['Juliette Sébastien']);
            unset($registrationEvent['Diego Fraile']);
            unset($registrationEvent['Alexandre Carlier']);
            unset($registrationEvent['Abdelhak Kaddour']);
            unset($registrationEvent['Charles Daloz-Baltenberger']);
            unset($registrationEvent['Jules Desjardin']);
            unset($registrationEvent['Anthony Lafourcade']);

            ksort($registrationEvent, SORT_LOCALE_STRING);
            $registrationsByEvent[$key] = $registrationEvent;
        }

        $placeholder = '-';

        $builder
            ->add('three', ChoiceType::class, [
                'choices' => $registrationsByEvent['333'],
                'label' => $this->translator->trans('rankings.events.333'),
                'placeholder' => $placeholder,

            ])
            ->add('two', ChoiceType::class, [
                'choices' => $registrationsByEvent['222'],
                'label' => $this->translator->trans('rankings.events.222'),
                'placeholder' => $placeholder,
            ])
            ->add('four', ChoiceType::class, [
                'choices' => $registrationsByEvent['444'],
                'label' => $this->translator->trans('rankings.events.444'),
                'placeholder' => $placeholder,
            ])
            ->add('five', ChoiceType::class, [
                'choices' => $registrationsByEvent['555'],
                'label' => $this->translator->trans('rankings.events.555'),
                'placeholder' => $placeholder,
            ])
            ->add('bld', ChoiceType::class, [
                'choices' => $registrationsByEvent['333bf'],
                'label' => $this->translator->trans('rankings.events.333bf'),
                'placeholder' => $placeholder,
            ])
            ->add('oh', ChoiceType::class, [
                'choices' => $registrationsByEvent['333oh'],
                'label' => $this->translator->trans('rankings.events.333oh'),
                'placeholder' => $placeholder,
            ])
            ->add('pyra', ChoiceType::class, [
                'choices' => $registrationsByEvent['pyram'],
                'label' => $this->translator->trans('rankings.events.pyram'),
                'placeholder' => $placeholder,
            ])
            ->add('skewb', ChoiceType::class, [
                'choices' => $registrationsByEvent['skewb'],
                'label' => $this->translator->trans('rankings.events.skewb'),
                'placeholder' => $placeholder,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Voter'
            ]);

    }

    private function getRegistrationData()
    {
        $cacheKey = 'registration_data';

        return $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(3600); // Cache for 1 hour

            $finder = new Finder();

            $registrations = [];

            $languageFile = $finder->in('../data/')->files()->name('FrenchChampionship2025-registration.csv');
            foreach ($languageFile as $file) {
                if (($handle = fopen($file, 'r')) !== false) {
                    $bom = "\xef\xbb\xbf";
                    if (fgets($handle, 4) !== $bom) {
                        rewind($handle);
                    }

                    $c = 0;
                    $headers = [];
                    while (!feof($handle)) {
                        $line = fgetcsv($handle, 10240, ",", '"', '\\');
                        if ($c == 0) {
                            $headers = $line;
                        } else {
                            if ($line) {
                                if ($line[0] === 'a') {
                                    if ($line[2] === 'France') {
                                        foreach ([6, 7, 8, 9, 12, 13, 16, 17] as $eventIndex) {
                                            if ($line[$eventIndex] == 1) {
                                                $registrations[$headers[$eventIndex]][$line[1]] = $line[3];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $c++;
                    }
                }
                break;
            }

            return $registrations;
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VotesCdf::class,
        ]);
    }
}
