<?php

namespace App\Entity;

use App\Entity\Image;
use App\Repository\GameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $nbrJoueur = null;

    #[ORM\Column(length: 255)]
    private ?string $editeur = null;

    #[ORM\Column(type:"string",length:255)]
    private $image;

    /*#[ORM\OneToOne(targetEntity: Image::class, cascade: ['persist', 'remove'])]
    private ?self $Image= null;*/
    

    #[ORM\OneToMany(mappedBy: 'game', targetEntity: Joueur::class)]
    private Collection $joueurs;
    

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
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

    public function getNbrJoueur(): ?int
    {
        return $this->nbrJoueur;
    }

    public function setNbrJoueur(int $nbrJoueur): self
    {
        $this->nbrJoueur = $nbrJoueur;

        return $this;
    }

    public function getEditeur(): ?string
    {
        return $this->editeur;
    }

    public function setEditeur(string $editeur): self
    {
        $this->editeur = $editeur;

        return $this;
    }

    /*public function getImage(): ?Image
    {
        return $this->Image;
    }

    public function setImage(Image $image): self
    {
        $this->Image = $image;

        return $this;
    }*/

    /**
* @return mixed
*/
public function getImage()
{
return $this->image;
}
/**
* @param mixed $image
*/
public function setImage($image): void
{
$this->image = $image;
}

    /**
     * @return Collection<int, Joueur>
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs->add($joueur);
            $joueur->setGame($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->joueurs->removeElement($joueur)) {
            // set the owning side to null (unless already changed)
            if ($joueur->getGame() === $this) {
                $joueur->setGame(null);
            }
        }

        return $this;
    }


    
}
