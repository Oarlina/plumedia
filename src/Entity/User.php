<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['pseudo'], message: 'Il y a déjà un compte avec ce pseudo')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $pseudo = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'isFollow')]
    private Collection $follow;

    /**
     * @var Collection<int, self>
     */
    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'follow')]
    private Collection $isFollow;

    /**
     * @var Collection<int, Story>
     */
    #[ORM\OneToMany(mappedBy: 'person', targetEntity: Story::class)]
    private Collection $stories;

    /**
     * @var Collection<int, Story>
     */
    #[ORM\ManyToMany(targetEntity: Story::class, inversedBy: 'usersLike')]
    #[ORM\JoinTable(name: 'likestory')] // cette ligne permet de renommer le nom de la table 
    private Collection $likedStories;

    /**
     * @var Collection<int, Story>
     */
    #[ORM\ManyToMany(targetEntity: Story::class, inversedBy: 'usersFollow')]
    #[ORM\JoinTable(name: 'followstory')] // cette ligne permet de renommer le nom de la table 
    private Collection $followedStories;

    /**
     * @var Collection<int, Chapter>
     */
    #[ORM\ManyToMany(targetEntity: Chapter::class, inversedBy: 'usersLike')]
    #[ORM\JoinTable(name: 'likechapter')] // cette ligne permet de renommer le nom de la table 
    private Collection $likedChapters;

    /**
     * @var Collection<int, Chapter>
     */
    #[ORM\ManyToMany(targetEntity: Chapter::class, inversedBy: 'userHaveRead')]
    #[ORM\JoinTable(name: 'haveread')] // cette ligne permet de renommer le nom de la table 
    private Collection $readChapters;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createAccount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deleteAccount = null;

    public function __construct()
    {
        $this->follow = new ArrayCollection();
        $this->isFollow = new ArrayCollection();
        $this->likedStories = new ArrayCollection();
        $this->followedStories = new ArrayCollection();
        $this->likedChapters = new ArrayCollection();
        $this->readChapters = new ArrayCollection();
        $this->createAccount = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    /**
     * @return Collection<int, Story>
     */
    public function getStories(): Collection
    {
        return $this->stories;
    }

    public function addStory(Story $story): static {
        if (!$this->stories->contains($story)) {
            $this->stories->add($story);
            $story->setPerson($this);
        }
        return $this;
    }

    public function removeStory(Story $story):static {
        if ($this->stories->removeElement($story)) {
            if ($story->getPerson() === $this) {
                $story->setPerson(null);
            }
        }
        return $this;
    }

    public function getFollow(): Collection {
        return $this->follow;
    }

    public function addFollow(self $follow): static
    {
        if (!$this->follow->contains($follow)) {
            $this->follow->add($follow);
        }
        return $this;
    }

    public function removeFollow(self $follow): static
    {
        $this->follow->removeElement($follow);
        return $this;
    }

    public function getIsFollow(): Collection
    {
        return $this->isFollow;
    }

    public function addIsFollow(self $isFollow): static
    {
        if (!$this->isFollow->contains($isFollow)) {
            $this->isFollow->add($isFollow);
            $isFollow->addFollow($this);
        }
        return $this;
    }

    public function removeIsFollow(self $isFollow): static
    {
        if ($this->isFollow->removeElement($isFollow)) {
            $isFollow->removeFollow($this);
        }
        return $this;
    }

    public function getLikedStories(): Collection
    {
        return $this->likedStories;
    }

    public function addLikedStory(Story $likedStory): static {
        if (!$this->likedStories->contains($likedStory)) {
            $this->likedStories->add($likedStory);
        }
        return $this;
    }

    public function removeLikedStory(Story $likedStory): static
    {
        $this->likedStories->removeElement($likedStory);
        return $this;
    }

    public function getFollowedStories(): Collection
    {
        return $this->followedStories;
    }

    public function addFollowedStory(Story $followedStory): static
    {
        if (!$this->followedStories->contains($followedStory)) {
            $this->followedStories->add($followedStory);
        }
        return $this;
    }

    public function removeFollowedStory(Story $followedStory): static
    {
        $this->followedStories->removeElement($followedStory);
        return $this;
    }

    public function getLikedChapters(): Collection
    {
        return $this->likedChapters;
    }

    public function addLikedChapter(Chapter $likedChapter): static
    {
        if (!$this->likedChapters->contains($likedChapter)) {
            $this->likedChapters->add($likedChapter);
        }
        return $this;
    }

    public function removeLikedChapter(Chapter $likedChapter): static
    {
        $this->likedChapters->removeElement($likedChapter);
        return $this;
    }

    public function getReadChapters(): Collection
    {
        return $this->readChapters;
    }

    public function addReadChapter(Chapter $readChapter): static
    {
        if (!$this->readChapters->contains($readChapter)) {
            $this->readChapters->add($readChapter);
        }
        return $this;
    }

    public function removeReadChapter(Chapter $readChapter): static
    {
        $this->readChapters->removeElement($readChapter);
        return $this;
    }

    public function getCreateAccount(): ?\DateTimeInterface
    {
        return $this->createAccount;
    }

    public function setCreateAccount(\DateTimeInterface $createAccount): static
    {
        $this->createAccount = $createAccount;
        return $this;
    }

    public function getDeleteAccount(): ?\DateTimeInterface
    {
        return $this->deleteAccount;
    }

    public function setDeleteAccount(?\DateTimeInterface $deleteAccount): static
    {
        $this->deleteAccount = $deleteAccount;
        return $this;
    }

    public function setFollowedStories($followedStories): static
    {
        $this->followedStories = $followedStories;
        return $this;
    }
}
