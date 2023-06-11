<?php

namespace App\Controller;

use App\Entity\Order;
use App\Exception\APIException;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/orders")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("", methods={"POST"})
     */
    protected $orderService;
    protected $entityManager;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(Request $request): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $orderDTO = $this->orderService->createOrder($requestData);
            return $this->json($orderDTO, Response::HTTP_CREATED);
        } catch (APIException $e) {
            
            return $this->json($e->toArray(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function getOrder(int $id): JsonResponse
    {
        try {
            $orderDTO = $this->orderService->getOrder($id);
            return $this->json($orderDTO);
        } catch (APIException $e) {
            return $this->json($e->toArray(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function updateOrder(int $id, Request $request): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $orderDTO = $this->orderService->updateOrder($id, $requestData);
            return $this->json($orderDTO);
        } catch (APIException $e) {
            return $this->json($e->toArray(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deleteOrder(int $id): JsonResponse
    {
        try {
            $this->orderService->deleteOrder($id);
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (APIException $e) {
            return $this->json($e->toArray(), Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/{id}/confirm", methods={"PUT"})
     */
    public function confirmOrder(int $id): JsonResponse
    {
        try {
            $this->orderService->confirmOrder($id);
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (APIException $e) {
            return $this->json($e->toArray(), Response::HTTP_NOT_FOUND);
        }
    }
}