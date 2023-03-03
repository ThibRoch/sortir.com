<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Form\TripType;
use App\Data\Filters;
use App\Entity\User;
use App\Form\FiltersType;
use App\Form\TripEditType;
use App\Repository\TripRepository;
use App\Repository\StatusRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class TripController extends AbstractController
{
    /**
     * @Route("/trip/create", name="trip_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager, StatusRepository $statusRepository): Response
    {
        $trip = new Trip();
        $trip -> setStartDateTime(new \DateTime());
        $trip -> setDeadline(new \DateTime());
        $tripForm = $this -> createForm(TripType::class, $trip);
        
        // Permet de récupérer et d'insérer les données récupérées
        $tripForm ->handleRequest($request);
        
        if($tripForm -> isSubmitted() && $tripForm -> isValid())
        {

            $status = $statusRepository->find(1);
            $trip -> setStatus($status);

            /** @var User $user */
            $user = $this->getUser();
            $trip->setCreator($user);
            $trip->setCampus($user->getCampus());
            
            $entityManager -> persist($trip);
            $entityManager -> flush();
           

            $this -> addFlash('success', 'Bien jouer !');
            return $this -> redirectToRoute('main_home');
        }


        return $this->render('trip/create.html.twig', [
            'tripForm' => $tripForm -> createView(),
        ]);
    }


    /**
     * @Route("/trip", name="trip_view")
     */
    public function view (TripRepository $tripRepository, Request $request ): Response
    {
        $data = new Filters();
        $data -> campus = $this -> getUser()-> getCampus();
        $filterform = $this -> createform(FiltersType::class, $data);

        $filterform -> handleRequest($request);
        $search = $tripRepository -> findTrip($data, $this-> getUser());
        
    
        return $this->render('trip/view.html.twig', [
            'filterform' => $filterform -> createView(),
            'trips' => $search           
        ]);
          
    }

    /**
     * @Route("/trip/editTrip/{id}", name="trip_editTrip")
     */
    public function editTrip(int $id, Trip $trip, Request $request, TripRepository $tripRepository,EntityManagerInterface $entityManager)
    {
        if($this -> isGranted('POST_EDIT',$trip))
        {
            $trip = $tripRepository -> find($trip->getId());
            $form = $this -> createform(TripType::class,$trip);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form ->isValid())
            {
            $entityManager -> persist($trip);
            $entityManager -> flush();
           

            $this -> addFlash('success', 'Trip updates !');
            return $this -> redirectToRoute('trip_view');

            }

            return $this -> render('trip/edit.html.twig', [
                'trip' => $trip,
                'editForm' => $form ->createView()
            ]);
        }

        $this -> addFlash('Denied', 'Accès refusé !');
        return $this->render('trip/view.html.twig');
    }

     /**
     * @Route("/trip/deletTrip/{id}", name="trip_deleteTrip")
     */
    public function deleteTrip(int $id,ManagerRegistry $managerRegistry,Trip $trip)
    {
        if($this -> isGranted('POST_DELETE',$trip))
        {
            $em = $this-> $managerRegistry-> getRepository(Trip::class);
            $em -> remove($trip);
            $em->flush();
            return $this -> redirectToRoute('trip_view');
        }
        else{
        $this -> addFlash('Denied', 'Accès refusé !');
        return $this->render('trip/view.html.twig');}
    }

    /**
     * @Route("/trip/showTrip/{id}", name="trip_showTrip", methods={"POST"})
     */
    public function showTrip(int $id,TripRepository $tripRepository) :Response
    {
        $showTrip = $tripRepository-> find($id);
        return $this-> render('trip/showTrip.html.twig',[
            "trip" => $showTrip
        ]);
    }

     /**
     * @Route("/trip/{id}/registerTrip", name="trip_register")
     */
    public function registerTrip(int $id, ManagerRegistry $managerRegistry, Trip $trip, EntityManagerInterface $entityManager) :Response
    {
        if($this -> isGranted('POST_REG',$trip))
        {
            $user = $this-> getUser();
            $trip -> addUser($user);
            //$user -> addTrip($trip);
           // $user ->
             $entityManager -> persist($user);
            //$this ->
             $entityManager -> flush();
            $this->addFlash('Ajout effectué', 'Participant ajouté au groupe');

            return $this -> redirectToRoute('trip_view');
        }
        else{
        $this -> addFlash('Denied', 'Accès refusé !');
        return $this->redirectToRoute('trip_view');}
    }

     /**
     * @Route("/trip/{id}/renounceTrip", name="trip_renounceTrip")
     */
    public function renounceTrip(int $id,ManagerRegistry $managerRegistry,Trip $trip, EntityManagerInterface $entityManager) :Response
    {
        if($this -> isGranted('POST_REC',$trip))
        {
             $user = $this -> getUser();
             $trip -> removeUser($user);
            //$user -> removeTrip($trip);
             $entityManager -> persist($trip);
             $entityManager-> flush();
             $this->addFlash('Suppression effectué', 'Participant désinscrit au groupe');
       
             return $this -> redirectToRoute('trip_view');
         }
        else{
             $this -> addFlash('Denied', 'Accès refusé !');
            return $this->redirectToRoute('trip_view');}
    }
}
