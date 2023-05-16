<?php

namespace App\Controller;

use App\Entity\Nation;
use App\Entity\PeopleId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QhubeController extends AbstractController
{
    #[Route(
        path: '/{_locale}',
        name: 'home',
//        requirements: [
//            '_locale' => 'en|fr|galo|br',
//        ],
    )]
    public function home(Request $request): Response
    {
//        $request->setLocale('fr');
//        $locale = $request->getLocale();

        return $this->render('home.html.twig', []);
    }

    #[Route(
        path: '/{_locale}/gifs',
        name: 'gifs',
//        requirements: [
//            '_locale' => 'en|fr|galo',
//        ],
    )]
//    public function gifs(): Response
//    {
//        $number = random_int(0, 100);
//
//        return new Response(
//            '<html><body>Lucky number: '.$number.'</body></html>'
//        );
//    }
//
//    #[Route(
//        path: '/{_locale}/translations',
//        name: 'translations',
////        requirements: [
////            '_locale' => 'en|fr|galo',
////        ],
//    )]
//    public function terlaterie(): Response
//    {
//        $number = random_int(0, 100);
//
//        return new Response(
//            '<html><body>Lucky number: '.$number.'</body></html>'
//        );
//    }

    #[Route(
        path: '/{_locale}/rankings/{country}/{event}/{type}',
        name: 'rankings',
//        requirements: [
//            '_locale' => 'en|fr|galo',
//        ],
        defaults: ['country' => 'bzh', 'event' => '333', 'type' => 'average']
    )]
    public function rankings(Request $request, EntityManagerInterface $entityManager, string $country, string $event, string $type): Response
    {
        $form = $this->createRankingsForm($entityManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            return $this->redirectToRoute('rankings', ['country' => $formData['country']->getShort(), 'event' => $formData['event'], 'type' => $formData['type']]);
        }

        $nation = $entityManager->getRepository(Nation::class)->findOneBy(['short' => $country]);

        $rankings = $this->getResults($entityManager, $country, $event, $type);

        return $this->render('rankings.html.twig', [
            'country' => $nation,
            'rankingsEvent' => $this->getEvent($entityManager, $event),
            'type' => $type,
            'rankings' => $rankings,
            'events' => $this->getEvents($entityManager),
            'countriesList' => $entityManager->getRepository(Nation::class)->findAll(),
            'form' => $form
        ]);
    }

    private function createRankingsForm(EntityManagerInterface $entityManager)
    {
        $events = $this->getEvents($entityManager);
        $eventsForForm = [];
        foreach ($events as $event) {
            $eventsForForm[$event['name']] = $event['id'];
        }

        $form = $this->createFormBuilder()
            ->add('country', ChoiceType::class, [
                'choices' => $entityManager->getRepository(Nation::class)->findAll(),
                'choice_value' => 'short',
                'choice_label' => function ($country, $key, $value) {
                    return 'rankings.country_names.' . $country->getShort();
                },
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => false,
                'placeholder' => 'rankings.country',
            ])
            ->add('event', ChoiceType::class, [
                'choices' => $eventsForForm,
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => false,
                'placeholder' => 'rankings.event',
                'choice_label' => function ($event, $key, $value) {
                    return 'rankings.events.' . $event;
                },
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'rankings.single' => 'single',
                    'rankings.average' => 'average'
                ],
                'attr' => ['class' => 'form-select'],
                'label_attr' => ['class' => 'form-label'],
                'label' => false,
                'placeholder' => 'rankings.type'
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light']
            ])
            ->getForm();

        return $form;
    }

    public function getEvents(EntityManagerInterface $entityManager): array
    {
        $conn = $entityManager->getConnection();

        $sql = '
            SELECT * FROM Events ORDER BY rank;
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    public function getEvent(EntityManagerInterface $entityManager, $event)
    {
        $conn = $entityManager->getConnection();

        $sql = '
            SELECT * FROM Events WHERE id = "' . $event . '";
            ';
        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative()[0];
    }

    public function getResults(EntityManagerInterface $entityManager, $country, $event, $type)
    {
        if ($type == 'single') {
            $t = 'best';
        } else {
            $t = 'average';
        }

        $conn = $entityManager->getConnection();

        $sql = 'SELECT a.personId, 
                        wca_statistics_time_format(' . $t . ', "' . $event . '", "' . $type . '") as res,  
                        competitionId, 
                        personName, 
                        wca_statistics_time_format(value1, "' . $event . '", "single") AS value1, 
                        wca_statistics_time_format(value2, "' . $event . '", "single") AS value2, 
                        wca_statistics_time_format(value3, "' . $event . '", "single") AS value3, 
                        wca_statistics_time_format(value4, "' . $event . '", "single") AS value4, 
                        wca_statistics_time_format(value5, "' . $event . '", "single") AS value5
                    FROM (
                        SELECT personId, MIN(' . $t . ') AS lowest 
                        FROM people_id p
                        INNER JOIN specific_results r ON r.personId = p.wca_id  
                        WHERE p.country_short = "' . $country . '" 
                          AND eventId = "' . $event . '" 
                          AND ' . $t . ' > 0 
                        GROUP BY personId) a 
                        JOIN specific_results b  ON a.personId = b.personId 
                        AND a.lowest = b.' . $t . '
                        WHERE eventId = "' . $event . '"
                        GROUP BY a.personId
                        ORDER BY ' . $t . ';';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }
}
