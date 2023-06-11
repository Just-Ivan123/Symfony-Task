<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class OrderItemRepository extends EntityRepository
{
    public function findByOrder(int $orderId)
    {
        return $this->createQueryBuilder('oi')
            ->where('oi.order = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getResult();
    }
}