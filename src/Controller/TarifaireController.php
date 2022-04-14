<?php

namespace App\Controller;

use App\Entity\GrilleTarifaire;
use App\Form\GrilleTarifaireType;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tarifaire")
 */
class TarifaireController extends AbstractController
{
    /**
     * @Route("/", name="app_tarifaire_index", methods={"GET"})
     */
    public function index(GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('tarifaire/index.html.twig', [
            'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
            
            
        ]);
    }

    /**
     * @Route("/new", name="app_tarifaire_new", methods={"GET", "POST"})
     */
    public function new(Request $request, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $grilleTarifaire = new GrilleTarifaire();
        $form = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $grilleTarifaireRepository->add($grilleTarifaire);
            return $this->redirectToRoute('app_tarifaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifaire/new.html.twig', [
            'grille_tarifaire' => $grilleTarifaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarifaire_show", methods={"GET"})
     */
    public function show(GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('tarifaire/show.html.twig', [
            'grille_tarifaire' => $grilleTarifaire,
         
            
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_tarifaire_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        $form = $this->createForm(GrilleTarifaireType::class, $grilleTarifaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $grilleTarifaireRepository->add($grilleTarifaire);
            return $this->redirectToRoute('app_tarifaire_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tarifaire/edit.html.twig', [
            'grille_tarifaire' => $grilleTarifaire,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_tarifaire_delete", methods={"POST"})
     */
    public function delete(Request $request, GrilleTarifaire $grilleTarifaire, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$grilleTarifaire->getId(), $request->request->get('_token'))) {
            $grilleTarifaireRepository->remove($grilleTarifaire);
        }

        return $this->redirectToRoute('app_tarifaire_index', [], Response::HTTP_SEE_OTHER);
    }
}
