<?php

namespace App\Controller;

use App\Entity\Nation;
use App\Form\NationMassImportType;
use App\Form\NationType;
use App\Repository\NationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}/admin/country')]
#[IsGranted('ROLE_ADMIN')]
class NationController extends AbstractController
{
    #[Route('/', name: 'app_nation_index', methods: ['GET'])]
    public function index(NationRepository $nationRepository): Response
    {
        return $this->render('nation/index.html.twig', [
            'nations' => $nationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_nation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, NationRepository $nationRepository): Response
    {
        $nation = new Nation();
        $form = $this->createForm(NationType::class, $nation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nationRepository->save($nation, true);

            return $this->redirectToRoute('app_nation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nation/new.html.twig', [
            'nation' => $nation,
            'form' => $form,
        ]);
    }

    #[Route('/mass-import', name: 'app_nation_mass_import', methods: ['GET', 'POST'])]
    public function massImport(Request $request, NationRepository $nationRepository, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(NationMassImportType::class);
        $form->handleRequest($request);
        $translations = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData()['tsv'];
            $dataArray = explode("\n", $data);
            foreach ($dataArray as $nationLine) {
                $nationData = explode("\t", $nationLine);
                $nation = new Nation();
                $code = $nationData[1];
                $nation->setName($nationData[0]);
                $nation->setShort($code);
                $nation->setImg($nationData[2]);
                foreach ($nationData as $key => $nationDatum) {
                    if ($key > 2) {
                        $translations[$key][$code] = $nationDatum;
                    }
                }
                $nationRepository->save($nation, true);
            }
            $this->addFlash('success', $translator->trans('country.mass_import.added'));
        }

        return $this->render('nation/mass_import.html.twig', [
            'form' => $form,
            'translations' => $translations
        ]);
    }

    #[Route('/{id}', name: 'app_nation_show', methods: ['GET'])]
    public function show(Nation $nation): Response
    {
        return $this->render('nation/show.html.twig', [
            'nation' => $nation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nation $nation, NationRepository $nationRepository): Response
    {
        $form = $this->createForm(NationType::class, $nation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nationRepository->save($nation, true);

            return $this->redirectToRoute('app_nation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('nation/edit.html.twig', [
            'nation' => $nation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nation_delete', methods: ['POST'])]
    public function delete(Request $request, Nation $nation, NationRepository $nationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nation->getId(), $request->request->get('_token'))) {
            $nationRepository->remove($nation, true);
        }

        return $this->redirectToRoute('app_nation_index', [], Response::HTTP_SEE_OTHER);
    }
}
