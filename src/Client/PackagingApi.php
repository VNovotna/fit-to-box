<?php

declare(strict_types=1);

namespace App\Client;

use App\Entity\Packaging;
use App\Entity\Product;
use App\Entity\ResponseCache;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception;
use GuzzleHttp\RequestOptions;

class PackagingApi
{
    public function __construct(private readonly Client $apiClient, private readonly EntityManager $entityManager)
    {
    }

    public function pack(PackagingRequest $request): array
    {
        $cachedResponse = $this->loadFromCache($request);
        if(!empty($cachedResponse)){
            return $cachedResponse;
        }

        try {
            return $this->doRequest($request);
        } catch (Exception | ApiException $e) {
            //TODO log somewhere 
            return $this->fallbackCalculation();
        }
    }

    private function loadFromCache(PackagingRequest $request): array 
    {
        // this is bad, it is possible to write it whole in SQL (somehow)
        // point is to get all responses containing product and intersect them
        // also it shoud by done by product dimensions and not id
        $responses = array_map(fn($product) => $product->getResponses(), $request->getProducts());
        var_dump($responses);
        //var_dump(array_intersect(...$responses));
        return [];

    }

    private function doRequest(PackagingRequest $request): array
    {
        $response = $this->apiClient->get('/packer/packIntoMany', [
            RequestOptions::QUERY => ['query' => json_encode($request)],
        ]);
        $data = json_decode((string) $response->getBody(), true)['response'];

        if ($data['status'] !== 1) {
            throw new ApiException(); //TODO pass errors array
        }

        $packagins = [];
        foreach ($data['bins_packed'] as $bin) {
            $usedPackaging = $this->entityManager->find(Packaging::class, $bin['bin_data']['id']);
            $cache = new ResponseCache($usedPackaging);
            foreach ($bin['items'] as $productData) {
                $product = $this->entityManager->find(Product::class, (int) $productData['id']);
                if ($product === null) {
                    $product = new Product((int) $productData['id'], $productData['w'], $productData['h'], $productData['d'], $productData['wg']);
                    $this->entityManager->persist($product);
                }
                $cache->addProduct($product);
            }
            $this->entityManager->persist($cache);
            $packagins[] = $usedPackaging;
        }
        $this->entityManager->flush();

        return $packagins;
    }

    private function fallbackCalculation(): array
    {
        return [];
    }
}