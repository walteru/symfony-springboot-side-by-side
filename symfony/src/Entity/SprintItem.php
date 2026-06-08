<?php

namespace App\Entity;

use App\Repository\SprintItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SprintItemRepository::class)]
#[ORM\Table(name: 'sprint_item')]
class SprintItem
{
    // El id es asignado explícitamente (1..5) para mantener la paridad de contrato
    // con el lado Spring Boot. Por eso no hay estrategia de generación automática.
    #[ORM\Id]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 120)]
    private string $title;

    #[ORM\Column(length: 60)]
    private string $category;

    #[ORM\Column(length: 30)]
    private string $status;

    #[ORM\Column]
    private int $weight;

    public function __construct(int $id, string $title, string $category, string $status, int $weight)
    {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->status = $status;
        $this->weight = $weight;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }
}
