<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class OrderDTO
{
    /**
     * @Assert\NotBlank
     */
    private $status = false;

    /**
     * @Assert\Valid
     */
    private $orderItems;

   

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

    public function setOrderItems($orderItems): void
    {
        $this->orderItems = $orderItems;
    }
}