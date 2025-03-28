<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QhubeController extends AbstractController
{
    #[Route('/')]
    public function indexNoLocale(Request $request): Response
    {
        // Autodetect language. If you add a language, put it here as well (the order matters if there's no match)
        $locale = $request->getPreferredLanguage(['eo', 'en', 'fr', 'br'] );

        return $this->redirectToRoute('home', ['_locale' => $locale]);
    }

    #[Route(
        path: '/{_locale}',
        name: 'home',
    )]
    public function home(): Response
    {
        return $this->render('home.html.twig', []);
    }

//    #[Route(
//        path: '/{_locale}/gifs',
//        name: 'gifs',
////        requirements: [
////            '_locale' => 'en|fr|galo',
////        ],
//    )]
//    public function gifs(): Response
//    {
//        $number = random_int(0, 100);
//
//        return new Response(
//            '<html><body>Lucky number: '.$number.'</body></html>'
//        );
//    }
}
