<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;/**/ 
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
#[Route('/produitmobile')]
class ProduitmobileController extends AbstractController
{

    #[Route("/deleteproduit/{id}")]
    public function deletePost(Request $req ,$id,NormalizerInterface $normalizer,ProduitRepository $postRepository)
    {
        $post =$postRepository->find($id);
        $postRepository->remove($post,true);
        $jsonContent =$normalizer->normalize($post, 'json',['groups'=>'produit'] );
        return new Response("produit deleted successsfully" . json_encode($jsonContent));
    }
    #[Route('/addp', name: 'app_produit_add')]
    public function add(Request $request, EntityManagerInterface $em): Response
{
    $nomProd = $request->query->get('nom_prod');
    $description = $request->query->get('description');
    $image = $request->query->get('image');
    $prix = $request->query->getInt('prix');
    $nbrProds = $request->query->getInt('nbr_prods');

    $produit = new Produit();
    $produit->setNomProd($nomProd);
    $produit->setDescription($description);
    $produit->setImage($image);
    $produit->setPrix($prix);
    $produit->setNbrProds($nbrProds);

    $em->persist($produit);
    $em->flush();

    return new Response('Produit ajouté avec succès');
}
#[Route("/updateproduit/{id}")]
    public function updatePost(Request $request ,$id,SerializerInterface $seriaizer,ProduitRepository $postRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $produit = $this->getDoctrine()->getManager()
            ->getRepository(Produit::class)
            ->find($request->get("id"));


       // $produit=$postRepository->find($request->get("id"));
            $nomProd = $request->query->get('nom_prod');
    $description = $request->query->get('description');
    $image = $request->query->get('image');
    $prix = $request->query->getInt('prix');
    $nbrProds = $request->query->getInt('nbr_prods');
    
  //  $produit = new Produit();
    $produit->setNomProd($nomProd);
    $produit->setDescription($description);
    $produit->setImage($image);
    $produit->setPrix($prix);
    $produit->setNbrProds($nbrProds);
    
    
    
        $em->persist($produit);
        $em->flush();
       /* $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize("Post a ete modifiee avec success.");*/
         return $this->json($produit,200,[],['groups'=>'produit']);
    }

}
