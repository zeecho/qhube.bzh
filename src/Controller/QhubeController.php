<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class QhubeController extends AbstractController
{

    #[Route('/')]
    public function indexNoLocale(TranslatorInterface $translator): Response
    {
        return $this->redirectToRoute('home', ['_locale' => $translator->getLocale()]);
    }

    #[Route(
        path: '/{_locale}',
        name: 'home',
    )]
    public function home(Request $request, Security $security): Response
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
}
