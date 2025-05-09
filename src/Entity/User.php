<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['pseudo'], message: 'Il y a déjà un compte avec ce pseudo')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

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
    #[ORM\JoinTable(name: 'likechapter')]// cette ligne permet de renommer le nom de la table 
    private Collection $likedChapters;

    /**
     * @var Collection<int, Chapter>
     */
    #[ORM\ManyToMany(targetEntity: Chapter::class, inversedBy: 'userHaveRead')]
    #[ORM\JoinTable(name: 'haveread')]// cette ligne permet de renommer le nom de la table 
    private Collection $readChapters;

    public function __construct()
    {
        $this->follow = new ArrayCollection();
        $this->isFollow = new ArrayCollection();
        $this->likedStories = new ArrayCollection();
        $this->followedStories = new ArrayCollection();
        $this->likedChapters = new ArrayCollection();
        $this->readChapters = new ArrayCollection();
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
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }
    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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
    public function addStory(Story $story): static
    {
        if (!$this->stories->contains($story)) {
            $this->stories->add($story);
            $story->setPerson($this);
        }

        return $this;
    }
    public function removeStory(Story $story): static
    {
        if ($this->stories->removeElement($story)) {
            // set the owning side to null (unless already changed)
            if ($story->getPerson() === $this) {
                $story->setPerson(null);
            }
        }

        return $this;
    }
    public function getUsers(): ?self
    {
        return $this->users;
    }
    public function setUsers(?self $users): static
    {
        $this->users = $users;

        return $this;
    }
    public function addUser(self $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setUsers($this);
        }

        return $this;
    }
    public function removeUser(self $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getUsers() === $this) {
                $user->setUsers(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection<int, self>
     */
    public function getFollow(): Collection
    {
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
    /**
     * @return Collection<int, self>
     */
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
    /**
     * @return Collection<int, Story>
     */
    public function getLikeStory(): Collection
    {
        return $this->likeStory;
    }
    public function addLikeStory(Story $likeStory): static
    {
        if (!$this->likeStory->contains($likeStory)) {
            $this->likeStory->add($likeStory);
        }

        return $this;
    }
    public function removeLikeStory(Story $likeStory): static
    {
        $this->likeStory->removeElement($likeStory);

        return $this;
    }
    /**
     * @return Collection<int, Story>
     */
    public function getFollowStory(): Collection
    {
        return $this->followStory;
    }
    public function addFollowStory(Story $followStory): static
    {
        if (!$this->followStory->contains($followStory)) {
            $this->followStory->add($followStory);
        }

        return $this;
    }
    public function removeFollowStory(Story $followStory): static
    {
        $this->followStory->removeElement($followStory);

        return $this;
    }
    /**
     * @return Collection<int, Chapter>
     */
    public function getLikeChapter(): Collection
    {
        return $this->likeChapter;
    }
    public function addLikeChapter(Chapter $likeChapter): static
    {
        if (!$this->likeChapter->contains($likeChapter)) {
            $this->likeChapter->add($likeChapter);
        }

        return $this;
    }
    public function removeLikeChapter(Chapter $likeChapter): static
    {
        $this->likeChapter->removeElement($likeChapter);

        return $this;
    }
    /**
     * @return Collection<int, Chapter>
     */
    public function getHaveRead(): Collection
    {
        return $this->haveRead;
    }
    public function addHaveRead(Chapter $haveRead): static
    {
        if (!$this->haveRead->contains($haveRead)) {
            $this->haveRead->add($haveRead);
        }

        return $this;
    }
    public function removeHaveRead(Chapter $haveRead): static
    {
        $this->haveRead->removeElement($haveRead);

        return $this;
    }
}
