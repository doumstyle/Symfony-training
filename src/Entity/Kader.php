<?php

namespace App\Entity;

use App\Repository\KaderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KaderRepository::class)]
class Kader
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $kesra = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKesra(): ?string
    {
        return $this->kesra;
    }

    public function setKesra(string $kesra): static
    {
        $this->kesra = $kesra;

        return $this;
    }
}
