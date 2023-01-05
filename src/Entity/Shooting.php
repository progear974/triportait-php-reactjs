<?php

namespace App\Entity;

use App\Repository\ShootingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShootingRepository::class)]
class Shooting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $code = null;

    #[ORM\Column]
    private ?int $nb_photos = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $filenames = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?\DateTimeInterface
    {
        return $this->code;
    }

    public function setCode(\DateTimeInterface $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNbPhotos(): ?int
    {
        return $this->nb_photos;
    }

    public function setNbPhotos(int $nb_photos): self
    {
        $this->nb_photos = $nb_photos;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFilenames(): array
    {
        return $this->filenames;
    }

    public function setFilenames(array $filenames): self
    {
        $this->filenames = $filenames;

        return $this;
    }
}
