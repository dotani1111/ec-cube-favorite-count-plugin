<?php

namespace Plugin\FavoriteCount\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Eccube\Entity\CustomerFavoriteProduct;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\ProductRepository;

class LoadFavoriteData extends Fixture
{
    private CustomerRepository $customerRepository;
    private ProductRepository $productRepository;

    public function __construct(CustomerRepository $customerRepository, ProductRepository $productRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->productRepository = $productRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $customers = $this->customerRepository->findBy([], ['id' => 'ASC'], 3);
        $products = $this->productRepository->findBy([], ['id' => 'ASC'], 3);

        if (empty($customers) || empty($products)) {
            return;
        }

        // 商品1に3人分のお気に入りを登録
        $favorites = [
            [$customers[0], $products[0]],
            [$customers[1], $products[0]],
            [$customers[2], $products[0]],
            // 商品2に1人分
            [$customers[0], $products[1]],
        ];

        foreach ($favorites as [$customer, $product]) {
            $favorite = new CustomerFavoriteProduct();
            $favorite->setCustomer($customer);
            $favorite->setProduct($product);
            $manager->persist($favorite);
        }

        $manager->flush();
    }
}
