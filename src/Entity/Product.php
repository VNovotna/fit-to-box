<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity]
class Product
{

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::FLOAT)]
    private float $width;

    #[ORM\Column(type: Types::FLOAT)]
    private float $height;

    #[ORM\Column(type: Types::FLOAT)]
    private float $length;

    #[ORM\Column(type: Types::FLOAT)]
    private float $weight;

    /**
     * @var Collection<int, ResponseCache>
     */
    #[ORM\ManyToMany(targetEntity: ResponseCache::class, mappedBy: 'products')]
    private Collection $responses;


    public function __construct(int $id, float $width, float $height, float $length, float $weight)
    {
        $this->responses = new ArrayCollection();
        $this->id = $id;
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->weight = $weight;
    }


    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ResponseCache[]
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function serialize(): array
    {
        return [
            'id' => (string) $this->id,
            'w' => $this->width,
            'h' => $this->height,
            'd' => $this->length,
            'wg' => $this->weight,
            'q' => 1, //TODO quantity
        ];
    }
}