<?php

declare(strict_types=1);

namespace App\Client;

class PackagingRequest implements \JsonSerializable
{
    /**
     * @var \App\Entity\Product[]
     */
    private array $items;

    /**
     * @var \App\Entity\Packaging[]
     */
    private array $bins;

    public function __construct(
        private readonly string $username,
        private readonly string $apiKey,
        array $items,
        array $bins,
    ) {
        $this->items = $items;
        $this->bins = $bins;
    }

    /**
     * @return \App\Entity\Product[]
     */
    public function getProducts(): array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        return [
            'username' => $this->username,
            'api_key' => $this->apiKey,
            'items' => array_map(fn($item) => $item->serialize(), $this->items),
            'bins' => array_map(fn($item) => $item->serialize(), $this->bins),
        ];
    }
}