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

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $folder = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $single_filenames = [];

    #[ORM\Column(length: 255)]
    private ?string $print_filename = null;

    #[ORM\Column]
    private ?bool $zip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function getSingleFilenames(): array
    {
        return $this->single_filenames;
    }

    public function setSingleFilenames(array $single_filenames): self
    {
        $this->single_filenames = $single_filenames;

        return $this;
    }

    public function getPrintFilename(): ?string
    {
        return $this->print_filename;
    }

    public function setPrintFilename(string $print_filename): self
    {
        $this->print_filename = $print_filename;

        return $this;
    }

    public function isZip(): ?bool
    {
        return $this->zip;
    }

    public function setZip(bool $zip): self
    {
        $this->zip = $zip;

        return $this;
    }
}
