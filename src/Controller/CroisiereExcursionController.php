<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\CroisiereExcursion;
use App\Form\CroisiereExcursionType;
use App\Repository\CroisiereExcursionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/croisiere/excursion")
 */
class CroisiereExcursionController extends AbstractController
{
    /**
     * @Route("/", name="app_croisiere_excursion_index", methods={"GET"})
     */
    public function index(CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        return $this->render('croisiere_excursion/index.html.twig', [
            'croisiere_excursions' => $croisiereExcursionRepository->findAll(),
            
        ]);
    }

    /**
     * @Route("/new", name="app_croisiere_excursion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        $croisiereExcursion = new CroisiereExcursion();
        $form = $this->createForm(CroisiereExcursionType::class, $croisiereExcursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
    
            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $img->setUrl($fichier);
                $croisiereExcursion->addImage($img);
            }
            $croisiereExcursionRepository->add($croisiereExcursion);
            return $this->redirectToRoute('app_croisiere_excursion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('croisiere_excursion/new.html.twig', [
            'croisiere_excursion' => $croisiereExcursion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_croisiere_excursion_show", methods={"GET"})
     */
    public function show(CroisiereExcursion $croisiereExcursion): Response
    {
        return $this->render('croisiere_excursion/show.html.twig', [
            'croisiere_excursion' => $croisiereExcursion,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_croisiere_excursion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CroisiereExcursion $croisiereExcursion, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        $form = $this->createForm(CroisiereExcursionType::class, $croisiereExcursion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
    
            // On boucle sur les images
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();
                
                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );
                
                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $img->setUrl($fichier);
                $croisiereExcursion->addImage($img);
            }
            $croisiereExcursionRepository->add($croisiereExcursion);
            return $this->redirectToRoute('app_croisiere_excursion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('croisiere_excursion/edit.html.twig', [
            'croisiere_excursion' => $croisiereExcursion,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_croisiere_excursion_delete", methods={"POST"})
     */
    public function delete(Request $request, CroisiereExcursion $croisiereExcursion, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$croisiereExcursion->getId(), $request->request->get('_token'))) {
            $croisiereExcursionRepository->remove($croisiereExcursion);
        }

        return $this->redirectToRoute('app_croisiere_excursion_index', [], Response::HTTP_SEE_OTHER);
    }
}
