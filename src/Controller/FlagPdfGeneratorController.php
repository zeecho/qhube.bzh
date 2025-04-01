<?php

namespace App\Controller;

use App\Form\FlagsPdfType;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use setasign\Fpdi\Fpdi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FlagPdfGeneratorController extends AbstractController
{
    #[Route('/{_locale}/flags-pdf', name: 'flag_pdf_generator')]
    public function index(Request $request, FormFactoryInterface $formFactory, Pdf $knpSnappyPdf): Response
    {
        $form = $formFactory->create(FlagsPdfType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $path = $request->getUriForPath('/flags/' . $formData['flag'] . '.svg');

            $html = $this->renderView(
                'flag_pdf_generator/pdf.html.twig', [
                    'flagPath' => $path,
                    'flagSize' => $formData['flagHeight']
                ]
            );

            $pdfContent = $knpSnappyPdf->getOutputFromHtml($html, ['page-size' => $formData['format']]);
            $tempFile = tempnam(sys_get_temp_dir(), 'pdf');
            file_put_contents($tempFile, $pdfContent);
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($tempFile);
            $pdf->AddPage();
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx);

            // Save the modified PDF to a temporary file
            $modifiedTempFile = tempnam(sys_get_temp_dir(), 'pdf');
            $pdf->Output($modifiedTempFile, 'F'); // 'F' means save to file

            unlink($tempFile);

            // Return the modified PDF as a downloadable file using PdfResponse
            $response = new PdfResponse(file_get_contents($modifiedTempFile), 'flags.pdf');

            // Clean up the modified temporary file
            unlink($modifiedTempFile);

            return $response;
        }
        return $this->render('flag_pdf_generator/index.html.twig', [
            'form' => $form,
        ]);
    }


}