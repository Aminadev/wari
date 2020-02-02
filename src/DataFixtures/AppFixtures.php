<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $role = new Profil();
        $role->setLibelle('Admin_system');
        $manager->persist($role);
        
        $role1 = new Profil();
        $role1->setLibelle('Admin');
        $manager->persist($role1);

        $role2 = new Profil();
        $role2->setLibelle('Caissier');
        $manager->persist($role2);

        $role3 = new Profil();
        $role3->setLibelle('Partenaire');
        $manager->persist($role3);

        $admin = new User();
        $admin->setUsername('Diouf');
        $admin->setLogin('dioufy@gmail.com');
        $admin->setPassword($this->encoder->encodePassword($admin,'passer'));
        $admin->setProfil($role);
        $manager->persist($admin);
        $manager->flush();
        //dd($admin->getRoles());
    }
}
