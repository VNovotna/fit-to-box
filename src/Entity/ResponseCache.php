<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity]
class ResponseCache
{

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'responses')]
    #[JoinTable(name: 'products_responses')]
    private Collection $products;

    #[ManyToOne(targetEntity: Packaging::class)]
    private Packaging $packaging;


    public function __construct(Packaging $packaging)
    {
        $this->products = new ArrayCollection();
        $this->packaging = $packaging;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function addProduct(Product $product)
    {
        $this->products[] = $product;
    }
}