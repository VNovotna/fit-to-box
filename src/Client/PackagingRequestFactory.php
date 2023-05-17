<?php

declare(strict_types=1);

namespace App\Client;

class PackagingRequestFactory
{
    public function __construct(
        private readonly string $username,
        private readonly string $apiKey
    ) {
    }

    public function create(array $products, array $bins): PackagingRequest
    {
        return new PackagingRequest($this->username, $this->apiKey, $products, $bins);
    }
}