<?php

namespace App\Controller;

use App\Entity\Omra;
use App\Entity\Images;

use App\Form\OmraType;
use App\Repository\OmraRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/omra")
 */
class OmraController extends AbstractController
{
    /**
     * @Route("/", name="app_omra_index", methods={"GET"})
     */
    public function index(OmraRepository $omraRepository, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('omra/index.html.twig', [
            'omras' => $omraRepository->findAll(),
            //'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_omra_new", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function new(Request $request, OmraRepository $omraRepository): Response
    {
           $omra = new Omra();
            $form = $this->createForm(OmraType::class, $omra);
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
                    $omra->addImage($img);
                }
                $omraRepository->add($omra);
                return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
            }
    
            return $this->renderForm('omra/new.html.twig', [
                'omra' => $omra,
                'form' => $form,
            ]);
        }
    
    

    /**
     * @Route("/{id}", name="app_omra_show", methods={"GET"})
     */
    public function show(Omra $omra, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('omra/show.html.twig', [
            'omra' => $omra,
            'grille_tarifaires' => $grilleTarifaireRepository->findByOffre($omra),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_omra_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        $form = $this->createForm(OmraType::class, $omra);
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
                $omra->addImage($img);
            }

            $omraRepository->add($omra);
            return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('omra/edit.html.twig', [
            'omra' => $omra,
            'form' => $form,
        ]);
    }

    

    /**
     * @Route("/{id}", name="app_omra_delete", methods={"POST"})
     */
    public function delete(Request $request, Omra $omra, OmraRepository $omraRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$omra->getId(), $request->request->get('_token'))) {
            $omraRepository->remove($omra);
        }

        return $this->redirectToRoute('app_omra_index', [], Response::HTTP_SEE_OTHER);
    }
}