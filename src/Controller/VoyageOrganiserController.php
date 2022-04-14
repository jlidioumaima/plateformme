<?php

namespace App\Controller;
use App\Entity\Images;
use App\Repository\ExcursionRepository;

use App\Entity\VoyageOrganiser;

use App\Entity\VoyageExcursion;
use App\Form\VoyageOrganiserType;
use App\Repository\VoyageOrganiserRepository;
use App\Repository\GrilleTarifaireRepository;
use App\Repository\VoyageExcursionRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/voyage/organiser")
 */
class VoyageOrganiserController extends AbstractController
{
    /**
     * @Route("/", name="app_voyage_organiser_index", methods={"GET"})
     */
    public function index(VoyageOrganiserRepository $voyageOrganiserRepository, GrilleTarifaireRepository $grilleTarifaireRepository, ExcursionRepository $excursionRepository,VoyageExcursionRepository $voyageExcursionRepository): Response
    {
        return $this->render('voyage_organiser/index.html.twig', [
            'voyage_organisers' => $voyageOrganiserRepository->findAll(),
            'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
            //'grille_tarifaires' => $grilleTarifaireRepository->findByOffre('voyage_organisers'),
            //'excursions' => $excursionRepository->findAll(),    
            'voyage_excursions'=>$voyageExcursionRepository->findAll(),


            
         
          
            
           
        ]);
    }

    /**
     * @Route("/new", name="app_voyage_organiser_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        $voyageOrganiser = new VoyageOrganiser();
        $form = $this->createForm(VoyageOrganiserType::class, $voyageOrganiser);
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
                $voyageOrganiser->addImage($img);
            }
            $voyageOrganiserRepository->add($voyageOrganiser);
            return $this->redirectToRoute('app_voyage_organiser_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage_organiser/new.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'form' => $form,
           
        ]);
    }

    /**
     * @Route("/{id}", name="app_voyage_organiser_show", methods={"GET"})
     */
    public function show(VoyageOrganiser $voyageOrganiser ,GrilleTarifaireRepository $grilleTarifaireRepository,VoyageExcursionRepository $voyageExcursionRepository): Response
    {
        return $this->render('voyage_organiser/show.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'grille_tarifaires' => $grilleTarifaireRepository->findByOffre($voyageOrganiser),
            //'excursions' => $excursionRepository->find($voyageOrganiser)
            //'offres' => $offresRepository->findAll(),
            'voyage_excursions'=>$voyageExcursionRepository->findByExcursion($voyageOrganiser),
            //'excursions' => $voyageExcursionRepository->findByVoyageExcursion($voyageOrganiser),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_voyage_organiser_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, VoyageOrganiser $voyageOrganiser, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        $form = $this->createForm(VoyageOrganiserType::class, $voyageOrganiser);
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
                $voyageOrganiser->addImage($img);
            }

            $voyageOrganiserRepository->add($voyageOrganiser);
            return $this->redirectToRoute('app_voyage_organiser_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('voyage_organiser/edit.html.twig', [
            'voyage_organiser' => $voyageOrganiser,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_voyage_organiser_delete", methods={"POST"})
     */
    public function delete(Request $request, VoyageOrganiser $voyageOrganiser, VoyageOrganiserRepository $voyageOrganiserRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voyageOrganiser->getId(), $request->request->get('_token'))) {
            $voyageOrganiserRepository->remove($voyageOrganiser);
        }

        return $this->redirectToRoute('app_voyage_organiser_index', [], Response::HTTP_SEE_OTHER);
    }
}