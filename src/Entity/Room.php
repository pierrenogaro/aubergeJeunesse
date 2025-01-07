<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "bed_edit"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "bed_edit"])]
    private ?string $name = null;

    /**
     * @var Collection<int, Bed>
     */
    #[ORM\OneToMany(targetEntity: Bed::class, mappedBy: 'room', cascade: ['persist', 'remove'])]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete"])]
    private Collection $beds;

    #[ORM\Column]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "bed_edit", "bed_create"])]
    private ?int $capacity = null;

    #[ORM\Column(length: 255)]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "beds_read", "bed_edit", "bed_create"])]
    private ?string $type = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "beds_read", "bed_edit", "bed_create"])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "beds_read", "bed_edit", "bed_create"])]
    private ?bool $isAvailable = true;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\ManyToMany(targetEntity: Booking::class, mappedBy: 'room')]
    #[Groups(["rooms_read", "room_edit", "room_create", "room_delete", "bookings_read"])]
    private Collection $bookings;

    public function __construct()
    {
        $this->beds = new ArrayCollection();
        $this->bookings = new ArrayCollection();
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
     * @return Collection<int, Bed>
     */
    public function getBeds(): Collection
    {
        return $this->beds;
    }

    public function addBed(Bed $bed): static
    {
        if (!$this->beds->contains($bed)) {
            $this->beds->add($bed);
            $bed->setRoom($this);
        }

        return $this;
    }

    public function removeBed(Bed $bed): static
    {
        if ($this->beds->removeElement($bed)) {
            // set the owning side to null (unless already changed)
            if ($bed->getRoom() === $this) {
                $bed->setRoom(null);
            }
        }

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setAvailable(bool $isAvailable): static
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->addRoom($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            $booking->removeRoom($this);
        }

        return $this;
    }
}