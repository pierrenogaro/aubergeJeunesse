<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["events_read", "event_edit", "event_create", "event_delete"])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["events_read", "event_edit", "event_create", "event_delete"])]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(["events_read", "event_edit", "event_create", "event_delete"])]
    private ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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
}
