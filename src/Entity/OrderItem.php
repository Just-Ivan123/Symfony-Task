<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
   
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $order;

    /**
     * @ORM\OneToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
   
    private $product;

    /**
    * @ORM\Column(type="integer", options={"default": 1}, name="quantity_column")
    * @Assert\GreaterThan(value=0)
    */
   
    private $quantity;
    
    public function __construct(Product $product, Order $order, int $quantity = 1)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->order = $order;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): void
    {
        $this->order = $order;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}