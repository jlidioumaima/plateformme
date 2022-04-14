<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\Rondonnee;
use App\Form\RondonneeType;
use App\Form\RandonneeType;
use App\Repository\RondonneeRepository;
use App\Repository\GrilleTarifaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/rondonnee")
 */
class RondonneeController extends AbstractController
{
    /**
     * @Route("/", name="app_rondonnee_index", methods={"GET"})
     */
    public function index(RondonneeRepository $rondonneeRepository, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('rondonnee/index.html.twig', [
            'rondonnees' => $rondonneeRepository->findAll(),
            'grille_tarifaires' => $grilleTarifaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_rondonnee_new", methods={"GET", "POST"})
     */
    public function new(Request $request, RondonneeRepository $rondonneeRepository): Response
    {
        $rondonnee = new Rondonnee();
        $form = $this->createForm(RondonneeType::class, $rondonnee);
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
                $rondonnee->addImage($img);
            }
            $rondonneeRepository->add($rondonnee);
            return $this->redirectToRoute('app_rondonnee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rondonnee/new.html.twig', [
            'rondonnee' => $rondonnee,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_rondonnee_show", methods={"GET"})
     */
    public function show(Rondonnee $rondonnee, GrilleTarifaireRepository $grilleTarifaireRepository): Response
    {
        return $this->render('rondonnee/show.html.twig', [
            'rondonnee' => $rondonnee,
            'grille_tarifaires' => $grilleTarifaireRepository->findByOffre($rondonnee),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_rondonnee_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Rondonnee $rondonnee, RondonneeRepository $rondonneeRepository): Response
    {
        $form = $this->createForm(RondonneeType::class, $rondonnee);
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
                $rondonnee->addImage($img);
            }
            $rondonneeRepository->add($rondonnee);
            return $this->redirectToRoute('app_rondonnee_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rondonnee/edit.html.twig', [
            'rondonnee' => $rondonnee,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_rondonnee_delete", methods={"POST"})
     */
    public function delete(Request $request, Rondonnee $rondonnee, RondonneeRepository $rondonneeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rondonnee->getId(), $request->request->get('_token'))) {
            $rondonneeRepository->remove($rondonnee);
        }

        return $this->redirectToRoute('app_rondonnee_index', [], Response::HTTP_SEE_OTHER);
    }
}