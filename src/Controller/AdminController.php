<?php

namespace App\Controller;

use App\Entity\Nation;
use App\Entity\PeopleId;
use App\Repository\NationRepository;
use App\Repository\PeopleIdRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
        path: '/add-people',
        name: 'admin_addpeople',
    )]
    public function addPeopleIdAsAdmin(Request $request, EntityManagerInterface $entityManager, PeopleIdRepository $peopleIdRepository)
    {
        $form = $this->createAddPeopleIdForm($entityManager);

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


    public function createAddPeopleIdForm($entityManager)
    {
        $form = $this->createFormBuilder()
            ->add('wcaId', TextType::class, [
                'label' => 'WCA ID',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\d{4}[A-Z]{4}\d{2}$/',
                        'message' => 'The WCA ID must follow the format YYYYXXXX00 (e.g., 2007MOMO01).'
                    ])
                ],
            ])
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
        path: '/add-admin',
        name: 'admin_addadmin',
    )]
    public function addAdmin(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository, TranslatorInterface $translator)
    {
        $form = $this->createAddAdminForm($entityManager);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $user = $userRepository->findOneBy(['wcaId' => $formData['wcaId']]);
//            $user->setRoles(['ROLE_USER']);
//            $userRepository->save($user, true);

            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $message = $translator->trans('admin.addadmin.already_admin', ['%user%' => $user->getName()]);
                $this->addFlash('danger', $message);
            } else {
                $user->addRole('ROLE_ADMIN');
                $userRepository->save($user, true);
                $message = $translator->trans('admin.addadmin.promoted', ['%user%' => $user->getName()]);
                $this->addFlash('success', $message);
            }

            return $this->redirectToRoute('admin_addadmin');
        }

        return $this->render('admin/addadmin.html.twig', [
            'form' => $form
        ]);
    }


    public function createAddAdminForm()
    {
        $form = $this->createFormBuilder()
            ->add('wcaId', TextType::class, [
                'label' => 'WCA ID',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'input-group-text'
                ],
                'row_attr' => [
                    'class' => 'input-group',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn btn-light']
            ])
            ->getForm();

        return $form;
    }
}
