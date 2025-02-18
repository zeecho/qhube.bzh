<?php

namespace App\Entity;

use App\Repository\PeopleIdRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeopleIdRepository::class)]
class PeopleId
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $wcaId = null;

    #[ORM\Column(length: 255)]
    private ?string $countryShort = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWcaId(): ?string
    {
        return $this->wcaId;
    }

    public function setWcaId(string $wcaId): self
    {
        $this->wcaId = $wcaId;

        return $this;
    }

    public function getCountryShort(): ?string
    {
        return $this->countryShort;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryShort = $countryCode;

        return $this;
    }
}
