<?php

namespace App\Controller;

use App\Entity\PeopleId;
use App\Form\AddAdminType;
use App\Form\AddManyPeopleIdType;
use App\Form\AddPeopleIdType;
use App\Repository\PeopleIdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_locale}/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route(
        path: '/',
        name: 'admin_index',
    )]
    public function adminIndex()
    {
        return $this->render('admin/index.html.twig');
    }


    #[Route(
        path: '/add-to-rankings',
        name: 'admin_addpeople',
    )]
    public function addPeopleToRankings(Request $request, PeopleIdRepository $peopleIdRepository, FormFactoryInterface $formFactory)
    {
        $form = $formFactory->create(AddPeopleIdType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $peopleId = new PeopleId();
            $peopleId->setWcaId($formData['wcaId']);
            $peopleId->setCountryCode($formData['country']);
            $peopleIdRepository->insertNewPeopleId($peopleId, true);

            return $this->redirectToRoute('admin_addpeople');
        }

        return $this->render('admin/addpeople.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(
        path: '/add-many-to-rankings',
        name: 'admin_add_many_people',
    )]
    public function addManyPeopleToRankings(Request $request, PeopleIdRepository $peopleIdRepository, FormFactoryInterface $formFactory)
    {
        $form = $formFactory->create(AddManyPeopleIdType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $wcaIds = explode("\n", $formData['wcaIds']);;
            $nbAdded = 0;
            $refusedWcaIds = [];
            foreach ($wcaIds as $wcaId) {
                $wcaId = trim($wcaId);
                if (preg_match('/^\d{4}[A-Z]{4}\d{2}$/', $wcaId)) {
                    $peopleId = new PeopleId();
                    $peopleId->setWcaId($wcaId);
                    $peopleId->setCountryCode($formData['country']);
                    $peopleIdRepository->insertNewPeopleId($peopleId, true);
                    $nbAdded++;
                } else {
                    $refusedWcaIds[] = $wcaId;
                }
            }
            $this->addFlash('info', $nbAdded . ' added ; ' . count($refusedWcaIds) . ' refused: ' . implode(', ', $refusedWcaIds));

            return $this->redirectToRoute('admin_add_many_people');
        }

        return $this->render('admin/addmanypeople.html.twig', [
            'form' => $form
        ]);
    }

    #[Route(
        path: '/add-admin',
        name: 'admin_addadmin',
    )]
    public function addAdmin(Request $request, UserRepository $userRepository, TranslatorInterface $translator, FormFactoryInterface $formFactory)
    {
        $form = $formFactory->create(AddAdminType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user = $userRepository->findOneBy(['wcaId' => $formData['wcaId']]);

            $newRole = $formData['role'];
            if (in_array($newRole, $user->getRoles())) {
                $message = $translator->trans('admin.addadmin.already_admin', ['%user%' => $user->getName()]);
                $this->addFlash('danger', $message);
            } else {
                $user->addRole($newRole);
                $userRepository->save($user, true);
                $message = $translator->trans('admin.addadmin.promoted', ['%user%' => $user->getName(), '%role%' => $newRole]);
                $this->addFlash('success', $message);
            }

            return $this->redirectToRoute('admin_addadmin');
        }

        return $this->render('admin/addadmin.html.twig', [
            'form' => $form
        ]);
    }
}
