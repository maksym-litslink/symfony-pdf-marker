<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TestRepository::class)
 */
class Test
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Pdf::class, inversedBy="tests")
     * @ORM\JoinColumn(nullable=true)
     */
    private $pdf;

    /**
     * @ORM\Column(type="integer")
     */
    private $startOffset;

    /**
     * @ORM\Column(type="integer")
     */
    private $end_offset;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPdf(): ?Pdf
    {
        return $this->pdf;
    }

    public function setPdf(?Pdf $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getStartOffset(): ?int
    {
        return $this->startOffset;
    }

    public function setStartOffset(int $startOffset): self
    {
        $this->startOffset = $startOffset;

        return $this;
    }

    public function getEndOffset(): ?int
    {
        return $this->end_offset;
    }

    public function setEndOffset(int $end_offset): self
    {
        $this->end_offset = $end_offset;

        return $this;
    }
}
