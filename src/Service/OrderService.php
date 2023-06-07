<?php

namespace App\Service;

use App\Entity\Order;
use App\DTO\OrderDTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Exception\ApiException;
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
         // Создание DTO и заполнение данными из запроса
         $orderDTO = new OrderDTO();
         $orderDTO->setOrderItems($requestData['items']);
 
         // Валидация DTO с использованием штатных валидаций Symfony
         $violations = $this->validator->validate($orderDTO);
 
         // Проверка наличия ошибок валидации
         if (count($violations) > 0) {
            throw new APIException('Validation failed', 400, $violations);
         }
         $order = new Order();
         $order->setOrderItems($orderDTO->getOrderItems());
         $this->entityManager->persist($order);
         $this->entityManager->flush();
         // Возвращение отвалидированной DTO
         return $orderDTO;
    }

    public function getOrder(int $orderId): Order
    {
        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new APIException('Order not found', 404);
        }

        return $order;
    }

    public function updateOrder(Order $order, array $requestData): OrderDTO
    {
        // Создание DTO и заполнение данными из запроса
        $orderDTO = new OrderDTO();
        $orderDTO->setStatus($requestData['status']);
        $orderDTO->setOrderItems($requestData['items']);

        // Валидация DTO с использованием штатных валидаций Symfony
        $violations = $this->validator->validate($orderDTO);

        // Проверка наличия ошибок валидации
        if (count($violations) > 0) {
            throw new APIException('Validation failed', 400, $violations);
        }

        // Обновление сущности заказа
        $order->setStatus($orderDTO->getStatus());
        $order->setOrderItems($orderDTO->getOrderItems());

        $this->entityManager->flush();

        // Возвращение отвалидированной DTO
        return $orderDTO;
    }

    public function deleteOrder(int $orderId): void
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw new APIException('Order not found', 404);
        }
        $this->entityManager->remove($order);
        $this->entityManager->flush();
    }

    public function confirmOrder(int $orderId): Order
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if ($order) {
        $order->setStatus(true);
        $this->entityManager->flush();
        }
        $orderDTO = new OrderDTO();
        $orderDTO->setOrderItems($order->getOrderItems());
        $orderDTO->setStatus(true);
        return $orderDTO;
    }

}