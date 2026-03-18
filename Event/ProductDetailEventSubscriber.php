<?php

namespace Plugin\FavoriteCount\Event;

use Eccube\Event\TemplateEvent;
use Eccube\Repository\CustomerFavoriteProductRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductDetailEventSubscriber implements EventSubscriberInterface
{
    private CustomerFavoriteProductRepository $favoriteRepository;

    public function __construct(CustomerFavoriteProductRepository $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Product/detail.twig' => 'onProductDetail',
            'Product/list.twig' => 'onProductList',
        ];
    }

    public function onProductDetail(TemplateEvent $event): void
    {
        $Product = $event->getParameter('Product');
        if ($Product === null) {
            return;
        }

        $count = (int) $this->favoriteRepository
            ->createQueryBuilder('cfp')
            ->select('COUNT(cfp.id)')
            ->where('cfp.Product = :Product')
            ->setParameter('Product', $Product)
            ->getQuery()
            ->getSingleScalarResult();

        $event->setParameter('favorite_count', $count);

        // add-cart ボタンの直前に差し込む
        $source = preg_replace(
            '/(<button[^>]+add-cart)/',
            "{{ include('@FavoriteCount/favorite_count.twig') }}\n\$1",
            $event->getSource()
        );
        $event->setSource($source);
    }

    public function onProductList(TemplateEvent $event): void
    {
        $Products = $event->getParameter('pagination');
        if (empty($Products)) {
            return;
        }

        // 全商品のお気に入り件数を一括取得して product_id => count の辞書に
        $rows = $this->favoriteRepository
            ->createQueryBuilder('cfp')
            ->select('IDENTITY(cfp.Product) AS product_id, COUNT(cfp.id) AS cnt')
            ->groupBy('cfp.Product')
            ->getQuery()
            ->getArrayResult();

        $favoriteCounts = [];
        foreach ($rows as $row) {
            $favoriteCounts[(int) $row['product_id']] = (int) $row['cnt'];
        }

        $event->setParameter('favorite_counts', $favoriteCounts);

        // add-cart クラスを持つ button タグの直前に差し込む
        $source = preg_replace(
            '/(<button[^>]+add-cart)/',
            "{{ include('@FavoriteCount/favorite_count_list.twig') }}\n\$1",
            $event->getSource()
        );
        $event->setSource($source);
    }
}
