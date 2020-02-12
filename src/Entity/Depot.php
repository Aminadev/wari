<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\DepotRepository")
 */
class Depot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $datedpt;

    /**
     * @ORM\Column(type="integer")
     * @Groups("POST")
     */
    private $montantdpt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Compte", inversedBy="depots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Compte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="depots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userd;


    public function __construct(){
     $this-> datedpt = new DateTime();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatedpt(): ?\DateTimeInterface
    {
        return $this->datedpt;
    }

    public function setDatedpt(\DateTimeInterface $datedpt): self
    {
        $this->datedpt = $datedpt;

        return $this;
    }

    public function getMontantdpt(): ?int
    {
        return $this->montantdpt;
    }

    public function setMontantdpt(int $montantdpt): self
    {
        $this->montantdpt = $montantdpt;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->Compte;
    }

    public function setCompte(?Compte $Compte): self
    {
        $this->Compte = $Compte;

        return $this;
    }

    public function getUserd(): ?User
    {
        return $this->User;
    }

    public function setUserd(?User $userd): self
    {
        $this->userd = $userd;

        return $this;
    }
}
