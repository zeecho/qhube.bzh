<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, unique: true)]
    private ?string $wcaId = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $country_iso2 = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $delegate_status = null;

    #[ORM\Column]
    private ?int $wcaWebsiteId = null;

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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->wcaId;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role): self
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCountryIso2(): ?string
    {
        return $this->country_iso2;
    }

    public function setCountryIso2(?string $country_iso2): self
    {
        $this->country_iso2 = $country_iso2;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getDelegateStatus(): ?string
    {
        return $this->delegate_status;
    }

    public function setDelegateStatus(?string $delegate_status): self
    {
        $this->delegate_status = $delegate_status;

        return $this;
    }

    public function getWcaWebsiteId(): ?int
    {
        return $this->wcaWebsiteId;
    }

    public function setWcaWebsiteId(int $wcaWebsiteId): self
    {
        $this->wcaWebsiteId = $wcaWebsiteId;

        return $this;
    }
}
