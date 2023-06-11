<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }


    public function addOrderItem(OrderItem $orderItem)
    {
        $orderItem->setOrder($this);
    }

    public function removeOrderItem(OrderItem $orderItem)
    {
        $orderItem->setOrder(null);
    }

    public function setOrderItems(array $orderItems)
    {
        foreach ($orderItems as $orderItem) {
            $this->addOrderItem($orderItem);
        }
    }
}