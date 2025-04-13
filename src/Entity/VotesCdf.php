<?php

namespace App\Entity;

use App\Repository\VotesCdfRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VotesCdfRepository::class)]
class VotesCdf
{
    #[ORM\Id]
    #[ORM\Column(length: 10)]
    private ?string $voter = null;

    #[ORM\Column(length: 10)]
    private ?string $three = null;

    #[ORM\Column(length: 10)]
    private ?string $two = null;

    #[ORM\Column(length: 10)]
    private ?string $four = null;

    #[ORM\Column(length: 10)]
    private ?string $five = null;

    #[ORM\Column(length: 10)]
    private ?string $oh = null;

    #[ORM\Column(length: 10)]
    private ?string $bld = null;

    #[ORM\Column(length: 10)]
    private ?string $pyra = null;

    #[ORM\Column(length: 10)]
    private ?string $skewb = null;

    public function getVoter(): ?string
    {
        return $this->voter;
    }

    public function setVoter(string $voter): self
    {
        $this->voter = $voter;

        return $this;
    }

    public function getThree(): ?string
    {
        return $this->three;
    }

    public function setThree(string $three): self
    {
        $this->three = $three;

        return $this;
    }

    public function getTwo(): ?string
    {
        return $this->two;
    }

    public function setTwo(string $two): self
    {
        $this->two = $two;

        return $this;
    }

    public function getFour(): ?string
    {
        return $this->four;
    }

    public function setFour(string $four): self
    {
        $this->four = $four;

        return $this;
    }

    public function getFive(): ?string
    {
        return $this->five;
    }

    public function setFive(string $five): self
    {
        $this->five = $five;

        return $this;
    }

    public function getOh(): ?string
    {
        return $this->oh;
    }

    public function setOh(string $oh): self
    {
        $this->oh = $oh;

        return $this;
    }

    public function getBld(): ?string
    {
        return $this->bld;
    }

    public function setBld(string $bld): self
    {
        $this->bld = $bld;

        return $this;
    }

    public function getPyra(): ?string
    {
        return $this->pyra;
    }

    public function setPyra(string $pyra): self
    {
        $this->pyra = $pyra;

        return $this;
    }

    public function getSkewb(): ?string
    {
        return $this->skewb;
    }

    public function setSkewb(string $skewb): self
    {
        $this->skewb = $skewb;

        return $this;
    }
}
