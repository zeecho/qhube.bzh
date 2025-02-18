<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'Events')]
class Event
{
    #[ORM\Id]
    #[ORM\Column(length: 6)]
    private ?string $id = null;

    #[ORM\Column(length: 54)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $rank = null;

    #[ORM\Column(length: 10)]
    private ?string $format = null;

    #[ORM\Column(name: 'cellName', length: 45)]
    private ?string $cellName = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function getCellName(): ?string
    {
        return $this->cellName;
    }

}
