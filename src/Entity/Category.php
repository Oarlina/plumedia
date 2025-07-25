<?php
namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, Story>
     */
    #[ORM\ManyToMany(targetEntity: Story::class, mappedBy: 'categories', orphanRemoval: true)]
    private Collection $Stories;

    public function __construct(){
        $this->Stories = new ArrayCollection();
    }

    public function getId(): ?int{
        return $this->id;
    }

    public function getName(): ?string{
        return $this->name;
    }
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection<int, Story>
     */
    public function getStories(): Collection{
        return $this->Stories;
    }
    public function addStory(Story $story): static{
        if (!$this->Stories->contains($story)) {
            $this->Stories->add($story);
        }
        return $this;
    }
    public function removeStory(Story $story): static{
        $this->Stories->removeElement($story);
        return $this;
    }
}
