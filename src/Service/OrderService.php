<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\OrderItem;
use App\DTO\OrderDTO;
use App\DTO\OrderItemDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\APIException;
use App\Repository\OrderRepository;

class OrderService
{
    private $entityManager;
    private $orderRepository;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $orderRepository;
        $this->validator = $validator;
    }

    public function createOrder(array $requestData): OrderDTO
    {
        $this->entityManager->getConnection()->beginTransaction();

    try {
        $order = new Order();
        $this->entityManager->persist($order);

        $orderItemObjects = [];
        $productRepository = $this->entityManager->getRepository(Product::class);
        $validator = $this->validator;

        foreach ($requestData['items'] as $itemData) {
            $product = $productRepository->find($itemData['product']['id']);
            $violations = $validator->validate($product);
            if (count($violations) > 0) {
                throw new APIException('Validation failed for Product', 400, $violations);
            }
            $orderItem = new OrderItem($product, $order, $itemData['quantity']);
            $this->entityManager->persist($orderItem);
            $violations = $validator->validate($orderItem);
            if (count($violations) > 0) {
                throw new APIException('Validation failed for OrderItem', 400, $violations);
            }
            $orderItemDTO = new OrderItemDTO();
            $orderItemDTO->setProduct($orderItem->getProduct());
            $orderItemDTO->setQuantity($orderItem->getQuantity());
            $orderItemObjects [] = $orderItemDTO;
        }

        $violations = $validator->validate($order);
        if (count($violations) > 0) {
            throw new APIException('Validation failed for Order', 400, $violations);
        }

        $this->entityManager->flush();
        $this->entityManager->getConnection()->commit();

        $orderDTO = new OrderDTO();
        $orderDTO->setOrder($order);
        $orderDTO->setOrderItems($orderItemObjects);

        return $orderDTO;
    } catch (\Exception $e) {
        $this->entityManager->getConnection()->rollBack();
        throw $e;
    }
    }

    public function getOrder(int $id): OrderDTO
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            throw new APIException('Order not found', 404);
        }
        $orderItemObjects = [];
        $orderItems = $this->entityManager->getRepository(OrderItem::class)->findBy(['order' => $order]);
        $orderDTO = new OrderDTO();
        $orderDTO->setOrder($order);
        foreach($orderItems as $orderItem){
            $orderItemDTO = new OrderItemDTO();
            $orderItemDTO->setProduct($orderItem->getProduct());
            $orderItemDTO->setQuantity($orderItem->getQuantity());
            $orderItemObjects [] = $orderItemDTO;
        }
        $orderDTO->setOrderItems($orderItemObjects);
        return $orderDTO;
    }

    public function updateOrder(int $orderId, array $requestData): OrderDTO
    {
    $this->entityManager->getConnection()->beginTransaction();

    try {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new APIException('Order not found', 404);
        }

        
        if (isset($requestData['status'])) {
            $order->setStatus($requestData['status']);
        }

        
        if (isset($requestData['items'])) {
            $orderItemObjects = [];
            $productRepository = $this->entityManager->getRepository(Product::class);
            $validator = $this->validator;

            foreach ($requestData['items'] as $itemData) {
                
                if (isset($itemData['product']['id'])) {
                    $product = $productRepository->find($itemData['product']['id']);
                    $violations = $validator->validate($product);
                    if (count($violations) > 0) {
                        throw new APIException('Validation failed for Product', 400, $violations);
                    }
                } else {
                    throw new APIException('Product ID is missing', 400);
                }

                $orderItem = new OrderItem($product, $order);

                if (isset($itemData['quantity'])) {
                    $orderItem->setQuantity($itemData['quantity']);
                }

                $this->entityManager->persist($orderItem);
                $violations = $validator->validate($orderItem);
                if (count($violations) > 0) {
                    throw new APIException('Validation failed for OrderItem', 400, $violations);
                }

                $orderItemDTO = new OrderItemDTO();
                $orderItemDTO->setProduct($orderItem->getProduct());
                $orderItemDTO->setQuantity($orderItem->getQuantity());
                $orderItemObjects[] = $orderItemDTO;
            }

        }


        $this->entityManager->flush();
        $this->entityManager->getConnection()->commit();

        $orderDTO = new OrderDTO();
        $orderDTO->setOrder($order);
        $orderDTO->setOrderItems($orderItemObjects);

        return $orderDTO;
    } catch (\Exception $e) {
        $this->entityManager->getConnection()->rollBack();
        throw $e;
    }
    }

    public function deleteOrder(int $orderId): void
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new APIException('Order not found', 404);
        }

        $this->entityManager->getConnection()->beginTransaction();

        try {
            $orderItems = $this->entityManager->getRepository(OrderItem::class)->findBy(['order' => $order]);
            foreach ($orderItems as $orderItem) {
                $this->entityManager->remove($orderItem);
            }
            $this->entityManager->remove($order);

            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            throw $e;
        }
    }

    public function confirmOrder(int $orderId): void
    {
        $order = $this->orderRepository->find($orderId);
        if (!$order) {
            throw new APIException('Order not found', 404);
        }
    
        $order->setStatus(true);
        $this->entityManager->flush();
    }
}