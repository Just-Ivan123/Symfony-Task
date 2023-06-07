<?php

namespace App\Controller;

use App\Entity\Order;
use App\Exception\ApiException;
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
    private $orderService;
    private $entityManager;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function createOrder(Request $request): JsonResponse
    {
        try {
            $requestData = json_decode($request->getContent(), true);
            $orderDTO = $this->$orderService->createOrder($requestData);
            return $this->json($orderDTO, Response::HTTP_CREATED);
        } catch (ApiException $e) {
            
            return $this->json($e->toArray(), Response::HTTP_BAD_REQUEST);
        }
    }

}