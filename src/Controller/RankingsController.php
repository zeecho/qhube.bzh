<?php

namespace App\Controller;

use App\Entity\Nation;
use App\Entity\PeopleId;
use App\Repository\EventRepository;
use App\Repository\NationRepository;
use App\Repository\PeopleIdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}/rankings')]
class RankingsController extends AbstractController
{
    #[Route(
        path: '/results/{country}/{event}/{type}',
        name: 'rankings',
//        defaults: ['country' => 'bzh', 'event' => '333', 'type' => 'average']
    )]
    public function rankings(Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository, NationRepository $nationRepository, string $country = null, string $event = null, string $type = null): Response
    {
        $events =  $eventRepository->findAll();
        $form = $this->createRankingsForm($entityManager, $events, $country, $event, $type);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            return $this->redirectToRoute('rankings', ['country' => $formData['country'], 'event' => $formData['event'], 'type' => $formData['type']]);
        }
        if ($country == null) {
            return $this->render('rankings_explanations.html.twig', [
                'countriesList' => $entityManager->getRepository(Nation::class)->findAll(),
                'form' => $form
            ]);
        }

        if ($event == null) {
            $event = '333';
            if ($type == null) {
                $type = 'single';
            }
            return $this->redirectToRoute('rankings', ['country' => $country, 'event' => $event, 'type' => $type]);
        }

        $nation = $entityManager->getRepository(Nation::class)->findOneBy(['short' => $country]);

        $rankings = $nationRepository->getResults($country, $event, $type);

        return $this->render('rankings.html.twig', [
            'country' => $nation,
            'rankingsEvent' => $eventRepository->find($event),
            'type' => $type,
            'rankings' => $rankings,
            'events' => $events,
            'countriesList' => $entityManager->getRepository(Nation::class)->findAll(),
            'form' => $form
        ]);
    }

    private function createRankingsForm(EntityManagerInterface $entityManager, $events, $country, $event, $type)
    {
        $eventsForForm = [];
        foreach ($events as $e) {
            $eventsForForm[$e->getName()] = $e->getId();
        }

        $form = $this->createFormBuilder()
            ->add('country', ChoiceType::class, [
                'choices' => $entityManager->getRepository(Nation::class)->findAllOrderedByTranslations(),
                'choice_label' => function ($c, $key, $value) {
                    return 'rankings.country_names.' . $c;
                },
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'rankings.country',
                'placeholder' => 'rankings.country',
                'data' => $country,
            ])
            ->add('event', ChoiceType::class, [
                'choices' => $eventsForForm,
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'rankings.event',
                'choice_label' => function ($eventLabel, $key, $value) {
                    return 'rankings.events.' . $eventLabel;
                },
                'placeholder' => 'rankings.event',
                'data' => $event
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'rankings.single' => 'single',
                    'rankings.average' => 'average'
                ],
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => 'rankings.type',
                'placeholder' => 'rankings.type',
                'data' => $type
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light'],
            ])
            ->getForm();

        return $form;
    }

    public function createJoinRankingsForm($entityManager)
    {
        $form = $this->createFormBuilder()
            ->add('country', ChoiceType::class, [
                'choices' => $entityManager->getRepository(Nation::class)->findAllOrderedByTranslations(),
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
            ->getForm();

        return $form;
    }

    #[Route(
        path: '/join',
        name: 'join_rankings'
    )]
    #[IsGranted('ROLE_USER')]
    public function askToJoinRankings(Request $request, EntityManagerInterface $entityManager, Security $security, PeopleIdRepository $peopleIdRepository, TranslatorInterface $translator)
    {
        $user = $security->getUser();

        $peopleIdCountries = $entityManager->getRepository(PeopleId::class)->findBy(['wcaId' => $user->getWcaId()]);
        $countries = [];
        foreach ($peopleIdCountries as $peopleIdCountry) {
            $countries[$translator->trans('rankings.country_names.' . $peopleIdCountry->getCountryShort())] = $peopleIdCountry->getCountryShort();
        }
        ksort($countries);

        $form = $this->createJoinRankingsForm($entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $maxCountries = 10;
            $personCountriesNb = count($peopleIdCountries);
            if ($personCountriesNb >= $maxCountries) {
                $message = $translator->trans('join.too_many_countries', ['%person_nb_of_countries%' => $personCountriesNb, '%max_nb_of_countries%' => $maxCountries]);
                $this->addFlash('danger', $message);
            } else {
                $formData = $form->getData();
                $countryToAdd = $formData['country'];
                $countryName = $translator->trans('rankings.country_names.' . $countryToAdd);

                $isCountryAlreadyThere = false;
                foreach ($peopleIdCountries as $peopleIdCountry) {
                    if ($peopleIdCountry->getCountryShort() === $countryToAdd) {
                        $isCountryAlreadyThere = true;
                        $message = $translator->trans('join.already_here', ['%country%' => $countryName]);
                        $this->addFlash('danger', $message);
                        break;
                    }
                }

                if (!$isCountryAlreadyThere) {
                    $peopleId = new PeopleId();
                    $peopleId->setWcaId($user->getWCAId());
                    $peopleId->setCountryCode($countryToAdd);
                    $peopleIdRepository->insertNewPeopleId($peopleId, true);
                    $message = $translator->trans('join.added', ['%country%' => $countryName]);
                    $this->addFlash('success', $message);
                }
            }

            return $this->redirectToRoute('join_rankings');
        }

        return $this->render('joinrankings.html.twig', [
            'form' => $form,
            'peopleIdCountries' => $countries
        ]);
    }

    #[Route('/unjoin/{countryShort}', name: 'unjoin_rankings', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteFromRankings(Request $request, $countryShort, EntityManagerInterface $entityManager, Security $security, PeopleIdRepository $peopleIdRepository): Response
    {
        $user = $security->getUser();

        if ($this->isCsrfTokenValid('delete'.$countryShort, $request->request->get('_token'))) {
            $peopleInRanking = $peopleIdRepository->findOneBy(['wcaId' => $user->getWcaId(), 'countryShort' => $countryShort]);

            if ($peopleInRanking) {
                $peopleIdRepository->remove($peopleInRanking, true);
            }
        }

        return $this->redirectToRoute('join_rankings', [], Response::HTTP_SEE_OTHER);
    }
}
