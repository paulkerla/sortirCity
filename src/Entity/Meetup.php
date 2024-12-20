<?php

namespace App\Entity;


use App\Repository\MeetupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: MeetupRepository::class)]
class Meetup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'The activity title is required.')]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startdatetime = null;
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $registrationlimitdate = null;


    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column]
    private ?int $maxregistrations = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $meetupinfos = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'meetups')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'meetups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $organizer = null;

    #[ORM\ManyToOne(inversedBy: 'meetups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

//    #[ORM\ManyToOne(inversedBy: 'meetups')]
    #[ORM\ManyToOne(targetEntity: Place::class, cascade: ['persist'], inversedBy: 'meetups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    /**
     * @var State|null
     */
    #[ORM\ManyToOne(inversedBy: 'meetups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?State $state = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getStartdatetime(): ?\DateTimeInterface
    {
        return $this->startdatetime;
    }

    public function setStartdatetime(\DateTimeInterface $startdatetime): static
    {
        $this->startdatetime = $startdatetime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRegistrationlimitdate(): ?\DateTimeInterface
    {
        return $this->registrationlimitdate;
    }

    public function setRegistrationlimitdate(\DateTimeInterface $registrationlimitdate): static
    {
        $this->registrationlimitdate = $registrationlimitdate;

        return $this;
    }

    public function getMaxregistrations(): ?int
    {
        return $this->maxregistrations;
    }

    public function setMaxregistrations(int $maxregistrations): static
    {
        $this->maxregistrations = $maxregistrations;

        return $this;
    }

    public function getMeetupinfos(): ?string
    {
        return $this->meetupinfos;
    }

    public function setMeetupinfos(?string $meetupinfos): static
    {
        $this->meetupinfos = $meetupinfos;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): static
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function validatePlaceOrNewPlace(ExecutionContextInterface $context): void
    {
        if (!$this->place && !$this->newPlace) {
            $context->buildViolation('Please select an existing place or create a new one.')
                ->atPath('place')
                ->addViolation();
        }
    }
}
