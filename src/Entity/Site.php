<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    /**
     * @var Collection<int, Meetup>
     */
    #[ORM\OneToMany(targetEntity: Meetup::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $meetups;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $users;

    public function __construct()
    {
        $this->meetups = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection<int, Meetup>
     */
    public function getMeetups(): Collection
    {
        return $this->meetups;
    }

    public function addMeetup(Meetup $meetup): static
    {
        if (!$this->meetups->contains($meetup)) {
            $this->meetups->add($meetup);
            $meetup->setSite($this);
        }

        return $this;
    }

    public function removeMeetup(Meetup $meetup): static
    {
        if ($this->meetups->removeElement($meetup)) {
            // set the owning side to null (unless already changed)
            if ($meetup->getSite() === $this) {
                $meetup->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setSite($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSite() === $this) {
                $user->setSite(null);
            }
        }

        return $this;
    }
}
