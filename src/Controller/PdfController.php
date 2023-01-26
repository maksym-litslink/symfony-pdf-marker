<?php

namespace App\Controller;

use App\Entity\Pdf;
use App\Entity\Test;
use App\Form\PdfType;
use App\Repository\PdfRepository;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PdfController extends AbstractController
{
    /**
     * @Route("/", name="app_pdf")
     */
    public function index(PdfRepository $pdfRepository): Response
    {
        $pdfs = $pdfRepository->findAll();
        $form = $this->createForm(PdfType::class, new Pdf(), [
            'action' => $this->generateUrl('app_pdf_upload'),
            'method' => 'POST',
        ]);
        return $this->render('pdf/index.html.twig', [
            'form' => $form->createView(),
            'pdfs' => $pdfs,
        ]);
    }

    /**
     * @Route("/upload", name="app_pdf_upload", methods={"POST"})
     */
    public function upload(Request $request, EntityManagerInterface $entityManager)
    {
        $pdf = new Pdf();
        $form = $this->createForm(PdfType::class, $pdf);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    return new Response("Sorry, there was an error uploading your file.", 500);
                }
                $pdf->setName($newFilename);
            }
            $entityManager->persist($pdf);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_pdf');
    }

    /**
     * @Route("/pdf/{id}", name="app_pdf_show")
     */
    public function show(Pdf $pdf)
    {
        $parser = new Parser();
        $parsed = $parser->parseFile($this->getParameter('pdf_directory') . '/' . $pdf->getName());
        $content = $parsed->getText();
        return $this->json([
            'pdf' => $content,
            'pdf_id' => $pdf->getId(),
        ]);
    }

    /**
     * @Route("/test", name="app_pdf_save")
     */
    public function save(Request $request, TestRepository $testRepository, EntityManagerInterface $entityManager, PdfRepository $pdfRepository)
    {
        $pdfId = $request->request->get('pdf_id');
        $pdf = $pdfRepository->find($pdfId);
        if ($pdf) {
            $testRepository->removeByPdf($pdf);
        } else {
            $testRepository->removeWithNull();
        }
        foreach ($request->request->get('elements', []) as $item) {
            $test = new \App\Entity\Test();
            if ($pdf) {
                $test->setPdf($pdf);
            }
            $test->setStartOffset($item['start']);
            $test->setEndOffset($item['end']);
            $entityManager->persist($test);
        }
        $entityManager->flush();
        return new Response('success');
    }

    /**
     * @Route("/test/{id}", name="app_tests")
     */
    public function tests(TestRepository $testRepository, Pdf $pdf = null)
    {
        if (!$pdf) {
            $tests = $testRepository->findBy(['pdf' => null]);
        } else {
            $tests = $pdf->getTests()->toArray();
        }
        return $this->json(array_map(function (Test $test) {
            return [
                'start_offset' => $test->getStartOffset(),
                'end_offset' => $test->getEndOffset(),
            ];
        }, $tests));
    }
}
