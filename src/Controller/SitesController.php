<?php

namespace App\Controller;
use App\Entity\Images;
use App\Entity\Sites;
use App\Form\SitesType;
use App\Repository\SitesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
/**
 * @Route("/sites")
 */
class SitesController extends AbstractController
{
    /**
     * @Route("/", name="app_sites_index", methods={"GET"})
     */
    public function index(SitesRepository $sitesRepository): Response
    {
        return $this->render('sites/index.html.twig', [
            'sites' => $sitesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_sites_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SitesRepository $sitesRepository): Response
    {
        $site = new Sites();
        $form = $this->createForm(SitesType::class, $site);
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
                $site->addImage($img);
            }
            $sitesRepository->add($site);
            return $this->redirectToRoute('app_sites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sites/new.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_sites_show", methods={"GET"})
     */
    public function show(Sites $site): Response
    {
        return $this->render('sites/show.html.twig', [
            'site' => $site,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_sites_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Sites $site, SitesRepository $sitesRepository): Response
    {
        $form = $this->createForm(SitesType::class, $site);
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
                $site->addImage($img);
            }
            $sitesRepository->add($site);
            return $this->redirectToRoute('app_sites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sites/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_sites_delete", methods={"POST"})
     */
    public function delete(Request $request, Sites $site, SitesRepository $sitesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$site->getId(), $request->request->get('_token'))) {
            $sitesRepository->remove($site);
        }

        return $this->redirectToRoute('app_sites_index', [], Response::HTTP_SEE_OTHER);
    }
     /**
 * @Route("/supprime/image/{id}", name="sites_delete_image", methods={"DELETE"})
 */
public function deleteImage(Images $image, ManagerRegistry $doctrine,Request $request){
   
    $data = json_decode($request->getContent(), true);

    // On vérifie si le token est valide
    if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){
        // On récupère le nom de l'image
        $nom = $image->getName();
        // On supprime le fichier
        unlink($this->getParameter('images_directory').'/'.$nom);

        // On supprime l'entrée de la base
        $em = $this->$doctrine->getManager();
        $em->remove($image);
        $em->flush();

        // On répond en json
        return new JsonResponse(['success' => 1]);
    }else{
        return new JsonResponse(['error' => 'Token Invalide'], 400);
    }
}
}
