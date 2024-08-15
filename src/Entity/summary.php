<?php

namespace App\Entity;

use App\Repository\SummaryFileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SummaryFileRepository::class)]
class summary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Ruta_Archivo;

    #[ORM\Column(type: 'datetime')]
    private $fecha;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRuta_Archivo(): ?string
    {
        return $this->Ruta_Archivo;
    }

    public function setRuta_Archivo(string $Ruta_Archivo): self
    {
        $this->Ruta_Archivo = $Ruta_Archivo;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }
}
