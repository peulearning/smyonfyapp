<?php

namespace App\Controller;

use App\Entity\Bovinos;
use App\Form\BovinosType;
use App\Repository\BovinosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bovinos')]
class BovinosController extends AbstractController
{
    #[Route('/', name: 'app_bovinos_index', methods: ['GET'])]
    public function index(BovinosRepository $bovinosRepository): Response
    {
        return $this->render('bovinos/index.html.twig', [
            'bovinos' => $bovinosRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_bovinos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BovinosRepository $bovinosRepository): Response
    {
        $bovino = new Bovinos();
        $form = $this->createForm(BovinosType::class, $bovino);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bovinosRepository->save($bovino, true);

            return $this->redirectToRoute('app_bovinos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bovinos/new.html.twig', [
            'bovino' => $bovino,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bovinos_show', methods: ['GET'])]
    public function show(Bovinos $bovino): Response
    {
        return $this->render('bovinos/show.html.twig', [
            'bovino' => $bovino,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_bovinos_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Bovinos $bovino, BovinosRepository $bovinosRepository): Response
    {
        $form = $this->createForm(BovinosType::class, $bovino);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bovinosRepository->save($bovino, true);

            return $this->redirectToRoute('app_bovinos_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bovinos/edit.html.twig', [
            'bovino' => $bovino,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_bovinos_delete', methods: ['POST'])]
    public function delete(Request $request, Bovinos $bovino, BovinosRepository $bovinosRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bovino->getId(), $request->request->get('_token'))) {
            $bovinosRepository->remove($bovino, true);
        }

        return $this->redirectToRoute('app_bovinos_index', [], Response::HTTP_SEE_OTHER);
    }
}
