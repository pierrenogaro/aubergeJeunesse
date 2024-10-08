<?php

namespace App\Entity;

use App\Repository\BedRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BedRepository::class)]
class Bed
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["beds_read", "bed_create", "bed_edit", "rooms_read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'beds')]
    #[Groups(["beds_read", "bed_create", "bed_edit"])]
    private ?Room $room = null;

    #[ORM\Column]
    #[Groups(["beds_read", "bed_create", "bed_edit", "rooms_read"])]
    private ?int $number = null;

    #[ORM\Column]
    #[Groups(["beds_read", "bed_create", "bed_edit", "rooms_read"])]
    private ?bool $isAvailable = true;

    #[ORM\Column(length: 255)]
    #[Groups(["beds_read", "bed_create", "bed_edit", "rooms_read"])]
    private ?string $bedNumber = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

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

    public function getBedNumber(): ?string
    {
        return $this->bedNumber;
    }

    public function setBedNumber(string $bedNumber): static
    {
        $this->bedNumber = $bedNumber;

        return $this;
    }
}