<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginController extends AbstractController
{

    #[Route(
        path: '/{_locale}/signin',
        name: 'signin'
    )]
    public function signin(Request $request)
    {
        $previousUrl = $request->headers->get('referer');
        $locale = $request->getLocale();
        $redirectURI = str_replace('{locale}', $locale, $_ENV['REDIRECT_URI']);
        $wcaURL = "https://www.worldcubeassociation.org/oauth/authorize?client_id=" . $_ENV['CLIENT_ID']
            . "&redirect_uri=" . $redirectURI
            . "&state=" . urlencode($previousUrl)
            . "&response_type=code&scope=public";

        return $this->redirect($wcaURL);
    }

    #[Route(
        path: '/{_locale}/wca',
        name: 'wca'
    )]
    public function wca(Request $request, EntityManagerInterface $entityManager, Security $security, TranslatorInterface $translator): Response
    {
        $locale = $request->getLocale();
        $redirectURI = str_replace('{locale}', $locale, $_ENV['REDIRECT_URI']);

        $code = $request->get('code');
        $state = $request->query->get('state');

        $httpClient = new Client();
        // create request to WCA API
        $response = $httpClient->post('https://www.worldcubeassociation.org/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => $_ENV['CLIENT_ID'],
                'client_secret' => $_ENV['CLIENT_SECRET'],
                'code' => $code,
                'redirect_uri' => $redirectURI
            ]
        ]);

        $json = json_decode($response->getBody()->getContents(), true);

        $client = new Client([
            'base_uri' => 'https://www.worldcubeassociation.org/api/v0/',
            'headers' => ['Authorization' => 'Bearer ' . $json['access_token']]
        ]);

        $response = $client->get('me');

        // return access token
        $personalData = json_decode($response->getBody()->getContents(), true);
        $personalData = $personalData['me'];

        $wcaWebsiteId = $personalData['id'];
        $user = $entityManager->getRepository(User::class)->findOneBy(['wcaWebsiteId' => $wcaWebsiteId]);

        $wcaId = $personalData['wca_id'];

        if (is_null($user) && !is_null($wcaId)) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['wcaId' => $wcaId]);
        }

        if (is_null($user)) {
            $user = new User();
        }

        if ($wcaId) {
            $user->setWcaId($wcaId);
            // orga team
//            if (in_array($wcaId, ["2010KADD01", "2017MEUN01", "2016DEMU01", "2013FRAI01", "2014BEGU01", "2016POUS01", "2016ANSE02", "2017CRET01", "2017MORE17", "2013FERT01", "2023BERN08", "2011MINN01", "2022GIRA05", "2014BAUD02", "2016BOUR01", "2017THIO01", "2015CONT02", "2015LUCI02", "2022MORE12", "2021BOUC01", "2019DELA02", "2018NEVE02", "2014LAFO01", "2014LAFO02", "2008MORE02", "2010DESJ01", "2019LEFE02", "2015LUCI02", "2019NAED01", "2011HOFF02"])) {
            // french cup team
            if (in_array($wcaId, ["2016POUS01", "2017THIO01", "2008MORE02"])) {
                $user->addRole("ROLE_CDF");
            }
        }

        $user->setName($personalData['name']);
        $user->setCountryIso2($personalData['country_iso2']);
        if (array_key_exists('region', $personalData)) {
            $user->setRegion($personalData['region']);
        }
        $user->setDelegateStatus($personalData['delegate_status']);
        $user->setWcaWebsiteId($wcaWebsiteId);

        $entityManager->persist($user);
        $entityManager->flush();

        $security->login($user);

        if ($state) {
            return $this->redirect($state);
        }

        return $this->redirectToRoute('home', ['_locale' => $translator->getLocale()]);
    }

    #[Route(
        path: '/{_locale}/signout',
        name: 'signout'
    )]
    public function signout(Security $security)
    {
        $security->logout();
    }

}