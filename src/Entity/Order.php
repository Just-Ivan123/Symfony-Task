<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
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

    /**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order", cascade={"persist", "remove"})
     */
    private $orderItems;
    
    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(boolean $status): void
    {
        $this->status = $status;
    }

    public function getOrderItems()
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem)
    {
        $this->orderItems->add($orderItem);
        $orderItem->setOrder($this);
    }

    public function removeOrderItem(OrderItem $orderItem)
    {
        $this->orderItems->removeElement($orderItem);
        $orderItem->setOrder(null);
    }
}