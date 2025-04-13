<?php

namespace App\Controller;

use App\Entity\VotesCdf;
use App\Form\VotesCdfType;
use App\Repository\VotesCdfRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/{_locale}')]
class CdfFormController extends AbstractController
{
    #[Route('/cdf', name: 'cdf_form')]
    public function index(Request $request, Security $security, VotesCdfRepository $votesCdfRepository): Response
    {
            $signedIn = $this->isGranted('ROLE_USER');
            $form = null;
            $alreadyVoted = null;

            if ($signedIn) {
                $userId = $security->getUser()->getId();
                $vote = $votesCdfRepository->find($userId);

                $alreadyVoted = true;
                if ($vote === null) {
                    $vote = new VotesCdf();
                    $alreadyVoted = false;
                }

                $form = $this->createForm(VotesCdfType::class, $vote);

                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $vote->setVoter($userId);

                    $votesCdfRepository->save($vote, true);
                    $alreadyVoted = true;
                }
            }

            return $this->render('cdf_form/index.html.twig', [
                'signed_in' => $signedIn,
                'form' => $form,
                'already_voted' => $alreadyVoted,
            ]);
    }

    #[IsGranted('ROLE_CDF')]
    #[Route('/cdf/admin', name: 'cdf_admin')]
    public function admin(Request $request, Security $security, VotesCdfRepository $votesCdfRepository): Response
    {
        $finder = new Finder();
        $languageFile = $finder->in('../data/')->files()->name('FrenchChampionship2025-registration.csv');

        $registrations = [];
        foreach ($languageFile as $file) {
            if (($handle = fopen($file, 'r')) !== false) {
                $bom = "\xef\xbb\xbf";
                if (fgets($handle, 4) !== $bom) {
                    rewind($handle);
                }

                $c = 0;
                while (!feof($handle)) {
                    $line = fgetcsv($handle, 10240, ",", '"', '\\');
                    if ($c != 0) {
                        if ($line) {
                            $registrations[$line[3]] = $line[1];
                        }
                    }
                    $c++;
                }
            }
            break;
        }

        $votes = $votesCdfRepository->getVoteResults();

        return $this->render('cdf_form/admin.html.twig', [
            'registrations' => $registrations,
            'votes' => $votes
        ]);
    }
}
