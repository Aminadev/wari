<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Repository\ProfilRepository;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CompteController extends AbstractController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        
    }
    //LA ROUTE A RENSEIGNER SUR POSTMAN
    /**
     * @Route("api/new/compte", name="new_compte",  methods={"POST"})
     */
    public function newCompte(Request $request, EntityManagerInterface $manager,UserPasswordEncoderInterface $passwordEncode,ProfilRepository $roleRepository,PartenaireRepository $partenaireRepository)
    {
        $values = json_decode($request->getContent());
       
        $dateJours = new \DateTime();
        $user = new User();
        $depot = new Depot();
        $compte = new Compte();
        $role = $roleRepository->findOneBy(array('libelle' => 'Partenaire'));
        $partenaire_existant = $partenaireRepository->findOneBy(array('ninea' => $values->ninea));
        
        if($values){
            #### Creation de User Partenaire ####
            if($partenaire_existant != null){

                #### Générer le numéro de compte #####

                $compte_id = $this->getLastId() + 1 ;
                $numCompte =str_pad($compte_id, 9 ,"0",STR_PAD_LEFT);
                
                #### Creation de compte Partenaire ####

                $userCreateur = $this->tokenStorage->getToken()->getUser();
             
                $compte->setNumero($numCompte)
                    ->setSolde(0)
                    ->setDatecreate($dateJours)
                    ->setUserc($userCreateur)
                    ->setPartenaire($partenaire_existant);
                $manager->persist($compte);
                
                ##### Initiliasation du compte ####

                $depot->setDatedpt($dateJours)
                    ->setMontantdpt($values->montantdpt)
                    ->setUserd($userCreateur)
                    ->setCompte($compte);
                $manager->persist($depot);

                #### Mise à jour du compte #####

                $NouveauSolde = ($values->montantdpt+$compte->getSolde());
                $compte->setSolde($NouveauSolde);
                $manager->persist($compte);
                $manager->flush();
                $data = [
                    'status' => 201,
                    'message' => 'Ce partenaire existe déja et un nouveau compte a été bien creé pour lui . '
                ];
                return new JsonResponse($data, 201);

            }else{
                $partenaire = new Partenaire();
                #### Creation de  Partenaire ####
        
                $partenaire->setNinea($values->ninea)
                    ->setRc($values->rc);
                  
                $manager->persist($partenaire);
                
                #### Creation de contrat Partenaire ####

                $user->setLogin($values->login)
                    ->setUsername($values->username)
                    ->setPassword($passwordEncode->encodePassword($user, $values->password))
                    ->setProfil($role)
                    ->setPartenaire($partenaire);
                $manager->persist($user);

                #### Générer le numéro de compte #####

                $compte_id = $this->getLastId() + 1 ;
                $numCompte =str_pad($compte_id, 9 ,"0",STR_PAD_LEFT);
                
                #### Creation de compte Partenaire ####

                $userCreateur = $this->tokenStorage->getToken()->getUser();
                $compte->setNumero($numCompte)
                    ->setSolde(0)
                    ->setDatecreate($dateJours)
                    ->setUserc($userCreateur)
                    ->setPartenaire($partenaire);
                $manager->persist($compte);
                
                ##### Initiliasation du compte ####

                $depot->setDatedpt($dateJours)
                    ->setMontantdpt($values->montantdpt)
                    ->setUserd($userCreateur)
                    ->setCompte($compte);
                $manager->persist($depot);

                #### Mise à jour du compte #####

                $NouveauSolde = ($values->montantdpt+$compte->getSolde());
                $compte->setSolde($NouveauSolde);
                $manager->persist($compte);
                $manager->flush();
                $data = [
                    'status' => 201,
                    'message' => 'Le compte a été bien creé . '
                ];
                return new JsonResponse($data, 201);
            
            }
        }else{
            $data = [
                'status' => 500,
                'message' => 'Veuillez saisir les valeurs . '];
    
            return new JsonResponse($data, 500);
        }
                    
    }

    ### Get last Partenaire ###
    public function getLastId() 
    {
        $repository = $this->getDoctrine()->getRepository(Compte::class);
        // look for a single Product by name
        $res = $repository->findBy([], ['id' => 'DESC']) ;
        if($res == null){
            return 0;
        }else{
            return $res[0]->getId();
        }
        
    }  
}