<?php

namespace App\Controller;

use App\Entity\Bovinos;
use App\Form\BovinosType;
use App\Repository\BovinosRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Helper\Dumper;
use Symfony\Component\Form\Extensions\Core\Type\IntegerType;

#[Route('/bovinos')]
class BovinosController extends AbstractController
{
    #[Route('/', name: 'app_bovinos_index', methods: ['GET'])]
    public function index(Request $request, BovinosRepository $bovinosRepository, PaginatorInterface $paginator): Response
    {
      $data['tituloPage'] = 'Todos Registros';
      $data['subTitulo'] = 'Registros cadastrados no sistema';
      $query = $bovinosRepository->findTodosOrdenadosPeloAniversario();

      $data['bovinos'] = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        10
      );

      $report['leite'] = $bovinosRepository->somaLeite();
      $report['mediaLeite'] = $bovinosRepository->mediaLeite();

      $report['racao'] = $bovinosRepository->somaRacao();
      $report['mediaRacao'] = $bovinosRepository->mediaRacao();

      $report['quantia'] = $bovinosRepository->countBovinoQuantia();
      $report['quantia_leite'] = $bovinosRepository->avgMediaQuantiaLeite();
      $report['quantia_racao'] = $bovinosRepository->avgMediaQuantiaRacao();

      $report['bovinos_abatidos'] = $bovinosRepository->countBovinosAbatidos();
      $report['leite_abatidos'] = $bovinosRepository->sumLeiteBovinosAbatidos();
      $report['racao_abatidos'] = $bovinosRepository->sumRacaoBovinosAbatidos();
      $report['dataMin_abatidos'] = $bovinosRepository->findByDataMininaAbate();
      $report['dataMax_abatidos'] = $bovinosRepository->findByDataMaximaAbate();

      return $this->render('/bovinos/index.html.twig', ['data' => $data, 'report' => $report]);

    }

    #[Route('/new', name: 'app_bovinos_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $bovino = new Bovinos;

        $form = $this->createForm(BovinosType::class, $bovino);
        $form->handleRequest($request);

        $data['tituloPage'] = 'Novo Registro';
        $data['subTitulo'] = 'Cadastrar novo Bovino no sistema';
        $data['form'] = $form;
        $data['form_visible'] = true;

            if($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($bovino);
                $entityManager->flush();

                $this->addFlash('sucess', 'Bovino adicionado com sucesso !');

                return $this->redirectToRoute('app_bovinos_index');
            }
            return $this->renderForm('bovinos/new.html.twig', $data);
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


    /*#[Route('/abate', name: 'app_bovinos_abate')]
    public function reportAbate(Request $request, BovinosRepository $bovinosRepository, PaginatorInterface $paginator) : Response
    {

        $paraAbate = $bovinosRepository->enviarAbate();
        $resultParaAbate = array_column($paraAbate, 'conditions');

        $data['tituloPage'] = 'Área de Abate';
        $data['subTitulo'] = 'Enviar Bovinos aptos ao Abate';
        $data['paraAbate'] = $resultParaAbate;
        $query = $bovinosRepository->findTodosOrdenadosPeloAniversario();

        $data['bovinos'] = $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            7
        );

        return $this->render('bovinos/abate.html.twig', ['data'=> $data]);
    }

    #[Route('/bovinos/abate/send/{id}', name: 'app_bovinos_abatido')]
    public function sendSlaughter($id, BovinosRepository $bovinosRepository, EntityManagerInterface $entityManager): Response
    {
        $bovino = $bovinosRepository->find($id);
        $bovino->setDataAbatimento(new DateTime());

        $entityManager->flush();

        $this->addFlash('success', 'Animal enviado para abate  com sucesso!!');

        return $this->redirectToRoute('app_bovinos_abate');
    }
    */


    #[Route('/abate', name: 'app_bovinos_abate')]
    public function abate(BovinosRepository $bovinosRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $data['titulo'] = 'bovinos prontos para abate';

        $query = $bovinosRepository->findPossibiliaDeAbate();

        $data['bovinos'] = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            7
        );

        return $this->render('bovinos/abate.html.twig', $data);
    }

    #[Route('/abater/{id}', name: 'app_bovinos_abater')]
    public function abater($id, EntityManagerInterface $em, BovinosRepository $bovinosRepository): Response
    {
        $bovino = $bovinosRepository->find($id);
        $bovino->setDataabatimento(new \DateTime('now'));

        $em->persist($bovino);
        $em->flush();
        $this->addFlash('success', 'Gado mandado pro abate com sucesso!');

        return $this->redirectToRoute('app_bovinos_abate');
    }

    #[Route('/abatidos', name: 'app_bovinos_abatidos')]
    public function abatidos(BovinosRepository $bovinosRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $data['titulo'] = 'Bovinos abatidos';

        $query = $bovinosRepository->findByDataMaximaAbate();

        $data['gados'] = $paginator->paginate(
            $query,
            $request->query->get('page', 1),
            7
        );

        return $this->render('bovinos/abatidos.html.twig', $data);
    }
}