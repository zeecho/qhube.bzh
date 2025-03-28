<?php

namespace App\Controller;

use App\Form\TerlaterieSearchType;
use App\Form\TerlaterieWcaKeysType;
use App\Service\ProjectService;
use App\Service\RecursiveArraySearch;
use App\Service\TranslationDataGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;


class TranslationController extends AbstractController
{

    #[Route(
        path: '/{_locale}/terlaterie/encerche/wca-keys',
        name: 'terlaterie_encerche_wca_keys',
    )]
    public function terlaterieEncercheWcaKeys(Request $request, FormFactoryInterface $formFactory): Response
    {
        $form = $formFactory->create(TerlaterieWcaKeysType::class);
        $translations = [];
        $finder = new Finder();
        $languageFiles = $finder->in('../data/translation_sources/wca')->files()->name("*.yml");
        $search = '';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $stringArray = explode(' navigate_next ', $form->getData()['string']);
            if (count($stringArray) < 2) {
                $stringArray = explode(' > ', $form->getData()['string']);
                if (count($stringArray) < 2) {
                    $stringArray = explode(' ', $form->getData()['string']);
                }
            }
            $search = implode(' > ', $stringArray);


            foreach ($languageFiles as $file) {
                $language = $file->getFilenameWithoutExtension();
                $stringArray[0] = $language;
                $data = Yaml::parse($file->getContents());

                foreach ($stringArray as $ymlKey) {
                    if (!array_key_exists($ymlKey, $data)) {
                        $data = '';
                        break;
                    }
                    $data = $data[$ymlKey];
                }

                // lanquages of the wca are like fr-CA but symfony uses the format fr_CA
                $language = str_replace('-', '_', $language);
                $originalLanguageName = Languages::getName($language, $language);
                $languageName = Languages::getName($language);
                $translations[$language]['originalName'] = $originalLanguageName;
                $translations[$language]['translatedName'] = $languageName;
                $translations[$language]['data'] = is_string($data) ? $data : '';
            }
            ksort($translations);
        }
        return $this->render('terlaterie_wca_keys.html.twig', [
            'form' => $form,
            'translations' => $translations,
            'search_string' => $search
        ]);
    }

    #[Route(
        path: '/{_locale}/terlaterie/encerche/projits',
        name: 'terlaterie_encerche_projits',
    )]
    public function terlaterieEncerche(Request $request, FormFactoryInterface $formFactory, ProjectService $projectsService, RecursiveArraySearch $recursiveArraySearch, TranslationDataGenerator $dataGenerator): Response
    {
        $form = $formFactory->create(TerlaterieSearchType::class);
        $translations = [];
        $searchString = '';
        $languagesHandled = [];
        $projects = $projectsService->getProjects();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $searchString = htmlspecialchars($formData['searchString'], ENT_NOQUOTES, 'UTF-8');
            $searchStringWithSlashEscaped = str_replace(array("/"),array("\/"),$searchString);

            $searchLanguage = $formData['searchLanguage'];

            foreach ($projects as $projectKey => $projectInfo) {
                $data = $dataGenerator->generateData($projectKey, $projectInfo, $searchLanguage);

                $results = $recursiveArraySearch->rarray_search($searchStringWithSlashEscaped, $data);

                if (!$results) {
                    continue;
                }

                foreach ($results as $key => $result) {
                    $pattern = '/^' . $searchLanguage . '\./';
                    $keyModified = preg_replace($pattern, '', $key);
                    $translations[$projectKey][$keyModified][$searchLanguage] = preg_replace('/(' . $searchStringWithSlashEscaped . ')/ui', '<span class="search-match">$1</span>', $result);
                }

                $targetLanguages = $formData['targetLanguages'];

                $languagesHandled[$projectKey] = [$searchLanguage];
                foreach ($targetLanguages as $targetLanguage) {
                    if ($targetLanguage === $searchLanguage) {
                        continue;
                    }

                    $data = $dataGenerator->generateData($projectKey, $projectInfo, $targetLanguage);

                    if (!array_key_exists($targetLanguage, $data)) {
                        continue;
                    }

                    $data = $data[$targetLanguage];

                    foreach ($translations[$projectKey] as $key => $translation) {
                        $dataToHandle = $data;
                        $keysArray = explode('.', $key);
                        foreach ($keysArray as $keyInArray) {
                            if (array_key_exists($keyInArray, $dataToHandle)) {
                                $dataToHandle = $dataToHandle[$keyInArray];
                            }
                        }
                        $translations[$projectKey][$key][$targetLanguage] = is_array($dataToHandle) ? '' : $dataToHandle;
                    }
                    $languagesHandled[$projectKey][] = $targetLanguage;
                }
            }
        }

        return $this->render('terlaterie_search.html.twig', [
            'form' => $form,
            'translations' => $translations,
            'search_string' => $searchString,
            'languages' => $languagesHandled,
            'projects' => $projects
        ]);
    }
}