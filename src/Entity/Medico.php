<?php

namespace App\Entity;

use App\Repository\MedicosRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=MedicosRepository::class)
 */
class Medico implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;
    /**
     * @ORM\Column(type="integer")
     */
    private int $crm;
    /**
     * @ORM\Column(type="string")
     */
    private string $nome;

    /**
     * @ORM\ManyToOne(targetEntity=Especialidade::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $especialidade;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getCrm(): string
    {
        return $this->crm;
    }

    public function setCrm(string $crm): self
    {
        $this->crm = $crm;

        return $this;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEspecialidade(): ?Especialidade
    {
        return $this->especialidade;
    }

    public function setEspecialidade(?Especialidade $especialidade): self
    {
        $this->especialidade = $especialidade;

        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'crm' => $this->getCrm(),
            'nome' => $this->getNome(),
            'especialidade_id' => $this->getEspecialidade()->getId(),
            '_links' => [
                [
                    'rel' => 'self',
                    'path' => "/medicos/{$this->getId()}"
                ],
                [
                    'rel' => 'especialidade',
                    'path' => "/especialidades/{$this->getEspecialidade()->getId()}"
                ]
            ]
        ];
    }
}
