<?php

namespace App;

use App\Client\Item;
use App\Client\PackagingApi;
use App\Client\PackagingRequestFactory;
use App\Entity\Packaging;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\StreamWrapper;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Application
{
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly PackagingRequestFactory $requestFactory,
        private readonly PackagingApi $packagingApi,
    ) {
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        //parse request
        $body = json_decode((string) $request->getBody(), true);

        $itemsToPack = [];
        foreach ($body['products'] as $item) {
            $itemsToPack[] = new Product($item['id'], $item['width'], $item['height'], $item['length'], $item['weight']);
        }

        //load boxes
        $boxes = $this->entityManager->getRepository(Packaging::class)->findAll();
        $request = $this->requestFactory->create($itemsToPack, $boxes);
        $packages = $this->packagingApi->pack($request);

        $response = new Response();
        $response->getBody()->write(json_encode($packages));
        return $response->withAddedHeader("Content-Type", "application/json");

    }

}