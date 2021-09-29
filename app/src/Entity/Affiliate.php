<?php

namespace App\Entity;

use App\Repository\AffiliateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AffiliateRepository::class)
 */
class Affiliate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidOrderedTimeGenerator")
     * @ORM\Column(type="uuid_binary_ordered_time", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=300, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=320, nullable=false, unique="true")
     * @Assert\NotBlank()
     * @Assert\Email(normalizer = "trim")
     */
    private $email;

    public function getId()
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function toArray(): array
    {
        return [
            "id" => (string) $this->getId(),
            "email" => $this->getEmail(),
            "firstname" => $this->getFirstname(),
            "lastname" => $this->getLastname()
        ];
    }
}
