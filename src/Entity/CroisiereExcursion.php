<?php

namespace App\Entity;

use App\Repository\CroisiereExcursionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CroisiereExcursionRepository::class)
 */
class CroisiereExcursion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Croisiere::class, inversedBy="croisiereExcursions")
     */
    private $croisiere;

    /**
     * @ORM\ManyToOne(targetEntity=Excursion::class, inversedBy="croisiereExcursions")
     */
    private $excursion;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="croisiereExcursion", orphanRemoval=true, cascade={"persist"})
     */
    private $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCroisiere(): ?Croisiere
    {
        return $this->croisiere;
    }

    public function setCroisiere(?Croisiere $croisiere): self
    {
        $this->croisiere = $croisiere;

        return $this;
    }

    public function getExcursion(): ?Excursion
    {
        return $this->excursion;
    }

    public function setExcursion(?Excursion $excursion): self
    {
        $this->excursion = $excursion;

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setCroisiereExcursion($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getCroisiereExcursion() === $this) {
                $image->setCroisiereExcursion(null);
            }
        }

        return $this;
    }
}
