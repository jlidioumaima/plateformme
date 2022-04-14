<?php

namespace App\Controller;

use App\Entity\Croisiere;
use App\Form\CroisiereType;
use App\Entity\Images;
use App\Repository\CroisiereRepository;

use App\Repository\CroisiereExcursionRepository;

use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/croisiere")
 */
class CroisiereController extends AbstractController
{
    /**
     * @Route("/", name="app_croisiere_index", methods={"GET"})
     */
    public function index(CroisiereRepository $croisiereRepository, GrilleTarifaireRepository $grilleTarifaireRepository, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        return $this->render('croisiere/index.html.twig', [
            'croisieres' => $croisiereRepository->findAll(),
            'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
            'croisiere_excursions' => $croisiereExcursionRepository->findAll(),
            
            
            //'excursions' => $excursionRepository->findAll(),

        ]);
    }

    /**
     * @Route("/new", name="app_croisiere_new", methods={"GET", "POST"})
     */
    public function new(Request $request, CroisiereRepository $croisiereRepository): Response
    {
        $croisiere = new Croisiere();
        $form = $this->createForm(CroisiereType::class, $croisiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setUrl($fichier);
                $img->setName($fichier);
                $croisiere->addImage($img);
            }
            $croisiereRepository->add($croisiere);
            return $this->redirectToRoute('app_croisiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('croisiere/new.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_croisiere_show", methods={"GET"})
     */
    public function show(Croisiere $croisiere, GrilleTarifaireRepository $grilleTarifaireRepository, CroisiereExcursionRepository $croisiereExcursionRepository): Response
    {
        return $this->render('croisiere/show.html.twig', [
            'croisiere' => $croisiere,
            'grille_tarifaires' => $grilleTarifaireRepository->findByOffre($croisiere),
            //'excursions' => $excursionRepository->findAll($croisiere),
            'croisiere_excursion'=>$croisiereExcursionRepository->findByExcursion($croisiere),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_croisiere_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository): Response
    {
        $form = $this->createForm(CroisiereType::class, $croisiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setUrl($fichier);
                $img->setName($fichier);
                $croisiere->addImage($img);
            }
            $croisiereRepository->add($croisiere);
            return $this->redirectToRoute('app_croisiere_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('croisiere/edit.html.twig', [
            'croisiere' => $croisiere,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_croisiere_delete", methods={"POST"})
     */
    public function delete(Request $request, Croisiere $croisiere, CroisiereRepository $croisiereRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$croisiere->getId(), $request->request->get('_token'))) {
            $croisiereRepository->remove($croisiere);
        }

        return $this->redirectToRoute('app_croisiere_index', [], Response::HTTP_SEE_OTHER);
    }
}