<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
    /**
     * @ORM\Column(type="boolean")
     * @Assert\IsFalse(message="The value of isDeleted must be false.")
     */
    private $isDeleted = false;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function softDelete(): void
    {
        $this->isDeleted = true;
        $this->deletedAt = new \DateTime();
    }

    public function restore(): void
    {
        $this->isDeleted = false;
        $this->deletedAt = null;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}