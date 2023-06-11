<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Order;
class OrderDTO
{
    /**
     * @Assert\NotNull
     */
    private $order;
    /**
     * @Assert\Valid
     * @Assert\All({
     *     @Assert\Type(type="App\Entity\OrderItem")
     * })
     */
    private $orderItems;


    


    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getOrderItems()
    {
        return $this->orderItems;
    }

    public function setOrderItems($orderItemObjects): void
    {

    $this->orderItems = $orderItemObjects;
    }
}