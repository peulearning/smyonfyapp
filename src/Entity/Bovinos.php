<?php

namespace App\Entity;

use App\Repository\BovinosRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BovinosRepository::class)]
class Bovinos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $codigo = null;

    #[ORM\Column]
    private ?float $leite = null;

    #[ORM\Column]
    private ?float $racao = null;

    #[ORM\Column]
    private ?float $peso = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_nascimento = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $data_abatimento = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): static
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getLeite(): ?float
    {
        return $this->leite;
    }

    public function setLeite(float $leite): static
    {
        $this->leite = $leite;

        return $this;
    }

    public function getRacao(): ?float
    {
        return $this->racao;
    }

    public function setRacao(float $racao): static
    {
        $this->racao = $racao;

        return $this;
    }

    public function getPeso(): ?float
    {
        return $this->peso;
    }

    public function setPeso(float $peso): static
    {
        $this->peso = $peso;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeInterface
    {
        return $this->data_nascimento;
    }

    public function setDataNascimento(?\DateTimeInterface $data_nascimento): static
    {
        $this->data_nascimento = $data_nascimento;

        return $this;
    }

    public function getDataAbatimento(): ?\DateTimeInterface
    {
        return $this->data_abatimento;
    }

    public function setDataAbatimento(?\DateTimeInterface $data_abatimento): static
    {
        $this->data_abatimento = $data_abatimento;

        return $this;
    }
}
