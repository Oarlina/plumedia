<?php

namespace App\Entity;

use App\Repository\ChapterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChapterRepository::class)]
class Chapter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $publish = null;

    #[ORM\Column(length: 255)]
    private ?string $file = null;

    #[ORM\Column]
    private ?bool $isFree = null;

    #[ORM\Column]
    private ?int $InSeason = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'chapters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Story $story = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'chapter', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'likedChapters', orphanRemoval: true)]
    private Collection $usersLike;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'readChapters', orphanRemoval: true)]
    private Collection $userHaveRead;

    #[ORM\Column]
    private ?bool $isPublic = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->usersLike = new ArrayCollection();
        $this->userHaveRead = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPublish(): ?\DateTimeInterface
    {
        return $this->publish;
    }

    public function setPublish(\DateTimeInterface $publish): static
    {
        $this->publish = $publish;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function isFree(): ?bool
    {
        return $this->isFree;
    }

    public function setIsFree(bool $isFree): static
    {
        $this->isFree = $isFree;

        return $this;
    }

    public function getInSeason(): ?int
    {
        return $this->InSeason;
    }

    public function setInSeason(int $InSeason): static
    {
        $this->InSeason = $InSeason;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }

    public function setStory(?Story $story): static
    {
        $this->story = $story;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setChapter($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getChapter() === $this) {
                $comment->setChapter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsersLike(): Collection
    {
        return $this->usersLike;
    }

    public function addUsersLike(User $usersLike): static
    {
        if (!$this->usersLike->contains($usersLike)) {
            $this->usersLike->add($usersLike);
            $usersLike->addLikedChapter($this);
        }

        return $this;
    }

    public function removeUsersLike(User $usersLike): static
    {
        if ($this->usersLike->removeElement($usersLike)) {
            $usersLike->removeLikedChapter($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUserHaveRead(): Collection
    {
        return $this->userHaveRead;
    }

    public function addUserHaveRead(User $userHaveRead): static
    {
        if (!$this->userHaveRead->contains($userHaveRead)) {
            $this->userHaveRead->add($userHaveRead);
            $userHaveRead->addReadChapter($this);
        }

        return $this;
    }

    public function removeUserHaveRead(User $userHaveRead): static
    {
        if ($this->userHaveRead->removeElement($userHaveRead)) {
            $userHaveRead->removeReadChapter($this);
        }

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }
}
