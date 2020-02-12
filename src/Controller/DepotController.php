<?php

namespace App\Controller;

use App\Entity\Depot;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DepotController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    /**
     * @Route("api/fairedepot", name="depot",  methods={"POST"})
     */
    public function faireDepot(Request $request, EntityManagerInterface $manager,CompteRepository $compteRepository)
    {
       
        $dateJours = new \DateTime();
        $depot = new Depot();
        $userd = $this->tokenStorage->getToken()->getUser();

        ##### Faire un depot ####

        $values = json_decode($request->getContent());
        if(isset($values->numero, $values->montantdpt)) 
        {
            $compte = $compteRepository->findOneBy(array('numero'=> $values->numero));
        if($compte)
        {
            $depot->setMontantdpt($values->montantdpt)
                ->setUserd($userd)
                ->setDatedpt($dateJours)
                ->setCompte($compte);
            $manager->persist($depot);
          
            #### Mise à jour du compte #####

            $NouveauSolde = ($values->montantdpt+$compte->getSolde());
            $compte->setSolde($NouveauSolde);
            $manager->persist($compte);
            $manager->flush();
            $data = [
            'status' => 201,
            'message' => 'Vous avez déposé '.$values->montantdpt.' dans votre compte => '.$values->numero];

            return new JsonResponse($data, 201);
            
        }else{
            $data = [
                'status' => 500,
                'message' => 'Le numéro de compte n\'exixte pas . '];
    
                return new JsonResponse($data, 500);
            }
        }
        else{
            $data = [
                'status' => 500,
                'message' => 'Veuillez saisir le numero de compte et le montant'];
    
                return new JsonResponse($data, 500);
        }
    }
   
}