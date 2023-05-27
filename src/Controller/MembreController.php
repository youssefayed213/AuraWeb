<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\MembreType;
use App\Form\LoginType;
use App\Form\LoginFType;
use App\Repository\MembreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[Route('/membre')]
class MembreController extends AbstractController
{
    #[Route('/signupjson', name: 'signupMembreAction',methods:['GET','POST'])]
    public function signupAction(Request $request)
    {
        $CIN = $request->query->get("CIN");
        $nom = $request->query->get("UserName");
        $prenom = $request->query->get("UserPrenom");
        $Email = $request->query->get("Email");
        $Adresse = $request->query->get("Adresse");
        $Password = $request->query->get("Password");
      

        if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
            return new Response("email invalid.");
        }
        $user = new Membre();
        
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($Email);
        $user->setAdresse($Adresse);
        $user->setPassword($Password);
        //$user->setIsVerified(true);
        

        try {
            $em = $this->getDoctrine()->getManager();
            $em ->persist($user);
            $em -> flush();

            return new JsonResponse("Account is cretaed", 200);
        }catch(\Exception $ex) {
            return new Response("exception".$ex->getMessage());
        }
    }



    #[Route('/signinjson', name: 'signinMembreAction',methods:['GET','POST'])]
    public function siginAction(Request $request,MembreRepository $membreRepository) 
    {
        $Email = $request->query->get("Email");
        $Password = $request->query->get("Password");

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(Membre::class)->findOneBy(['email'=>$Email]);
        $membre = $membreRepository->verif($Email,$Password);
        if($membre!=null){
            /* $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($membre);
            return new JsonResponse($formatted); */
            return $this->json($membre, 200, [], ['groups' => 'posts']);
        }
        else{
            return new Response("failed");
        }
        /* if($user) {
            if($Password == $user->getPassword()) {
                $serializer = new Serializer([new ObjectNormalizer()]);
                $formatted = $serializer->normalize($user);
                return new JsonResponse($formatted);
            }
            else {
                return new Response("failed");
            }
        }
        else 
        {
            return new Response("failed");
        } */
    }

    #[Route('/', name: 'app_membre_index', methods: ['GET'])]
    public function index(Request $request,MembreRepository $membreRepository): Response
    {   $session= $request->getSession();
        return $this->render('membre/index.html.twig', [
            'membres' => $membreRepository->findAll(),
            'membr' => $session->get('user', [])
        ]);
    }
    #[Route('/login', name: 'app_membre_login')]
    public function login(Request $request, MembreRepository $membreRepository): Response
    {   
        $form = $this->createForm(LoginType::class);

        $form->handleRequest($request);
        $session= $request->getSession();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            $password = $form->get('password')->getData();
            $membre=$membreRepository->verif($email,$password);
           
                if ($membre!=null) {
                    $session->set('user', $membre);
                    return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
                } 
                else {
                    $this->addFlash('error', 'Invalid credentials');
                }
        }
        return $this->render('membre/login.html.twig', [
            'forma' => $form->createView()
        ]); 
    }
    #[Route('/loginf', name: 'app_membre_loginf')]
    public function loginf(Request $request, MembreRepository $membreRepository): Response
    {   
        $form = $this->createForm(LoginFType::class);

        $form->handleRequest($request);
        $session= $request->getSession();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            $password = $form->get('password')->getData();
            $membre=$membreRepository->verif($email,$password);
           
                if ($membre!=null) {
                    $session->set('user', $membre);
                    return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                } 
                else {
                    $this->addFlash('error', 'Invalid credentials');
                }
        }
        return $this->render('membre/loginF.html.twig', [
            'forma' => $form->createView()
        ]); 
    }
    #[Route('/signup', name: 'app_membre_signup')]
    public function signup(Request $request, MembreRepository $membreRepository): Response
    {   $membre = new Membre();
        $form = $this->createForm(MembreType::class,$membre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            
           
                if ($membreRepository->findOneBy(['email' => $email])==null) {
                    $membre->setRole("membre");
                    $membreRepository->save($membre, true);
                    return $this->redirectToRoute('app_membre_loginf', [], Response::HTTP_SEE_OTHER);
                } 
                else {
                    $this->addFlash('error', 'Invalid credentials');
                }
        }
        return $this->render('membre/signup.html.twig', [
            'form' => $form->createView()
        ]); 
    }
    #[Route('/signuppar', name: 'app_membre_signuppar')]
    public function signuppar(Request $request, MembreRepository $membreRepository): Response
    {   $membre = new Membre();
        $form = $this->createForm(MembreType::class,$membre);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            
           
                if ($membreRepository->findOneBy(['email' => $email])==null) {
                    $membre->setRole("partenaire");
                    $membreRepository->save($membre, true);
                    return $this->redirectToRoute('app_membre_loginf', [], Response::HTTP_SEE_OTHER);
                } 
                else {
                    $this->addFlash('error', 'Invalid credentials');
                }
        }
        return $this->render('membre/signup.html.twig', [
            'form' => $form->createView()
        ]); 
    }
    #[Route('/new', name: 'app_membre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MembreRepository $membreRepository): Response
    {
        $membre = new Membre();
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $membre->setRole("Admin");
            $membreRepository->save($membre, true);

            return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('membre/new.html.twig', [
            'membre' => $membre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_membre_show', methods: ['GET'])]
    public function show(Membre $membre): Response
    {
        return $this->render('membre/show.html.twig', [
            'membre' => $membre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_membre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Membre $membre, MembreRepository $membreRepository): Response
    {
        $form = $this->createForm(MembreType::class, $membre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $membreRepository->save($membre, true);

            return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('membre/edit.html.twig', [
            'membre' => $membre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_membre_delete', methods: ['POST'])]
    public function delete(Request $request, Membre $membre, MembreRepository $membreRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$membre->getId(), $request->request->get('_token'))) {
            $membreRepository->remove($membre, true);
        }

        return $this->redirectToRoute('app_membre_index', [], Response::HTTP_SEE_OTHER);
    }
   
    
}
