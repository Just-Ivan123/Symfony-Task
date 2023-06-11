<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Exception\APIException;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderItem;

class OrderControllerTest extends WebTestCase
{
    public function testCreateOrder(): void
    {
        $client = static::createClient();

       
        $product1 = new Product('Product 1');

        $product2 = new Product('Product 2');

        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        $entityManager->persist($product1);
        $entityManager->persist($product2);
        $entityManager->flush();
        $product1Id = $product1->getId();
        $product1Name = $product1->getName();
        $product2Id = $product2->getId();
        $product2Name = $product2->getName();
       
        $requestData = [
            'items' => [
                [
                    'quantity' => 1,
                    'product' => [
                        'id' => $product1Id,
                        'name' => $product1Name,
                    ],
                ],
                [
                    'quantity' => 1,
                    'product' => [
                        'id' => $product2Id,
                        'name' => $product2Name,
                    ],
                ],
            ],
        ];

        $client->request('POST', '/orders', [], [], [], json_encode($requestData));

        $response = $client->getResponse();
        $content = $response->getContent();
    
        echo $content;

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    public function testCreateOrderWithInvalidData(): void
    {
    $client = static::createClient();

    $product = new Product('Product 1');
    $product->softDelete();
    $entityManager = $client->getContainer()->get('doctrine')->getManager();
    $entityManager->persist($product);
    $entityManager->flush();

    $product1Id = $product->getId();
    $product1Name = $product->getName();

    $requestData = [
        'items' => [
            [
                'quantity' => -1, 
                'product' => [
                    'id' => $product1Id,
                    'name' => $product1Name,
                ],
            ],
        ],
    ];

    $client->request('POST', '/orders', [], [], [], json_encode($requestData));

    $response = $client->getResponse();
    $content = $response->getContent();
    echo $content;
    $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());

    $exceptionData = json_decode($content, true);
    $this->assertArrayHasKey('message', $exceptionData);
    $this->assertArrayHasKey('statusCode', $exceptionData);
    $this->assertArrayHasKey('errors', $exceptionData);
    }

    public function testGetOrder(): void
{
    $client = static::createClient();

    $order = new Order();
    $entityManager = $client->getContainer()->get('doctrine')->getManager();
    $entityManager->persist($order);
    $entityManager->flush();

    $orderId = 1;

    $client->request('GET', '/orders/' . $orderId);

    $response = $client->getResponse();
    $content = $response->getContent();
    echo $content;
    $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

    $responseData = json_decode($content, true);
    $this->assertEquals($orderId, $responseData['order']['id']);
   
}
public function testGetOrderWithInvalidId(): void
{
    $client = static::createClient();

    $invalidId = 9999; 

    $client->request('GET', '/orders/' . $invalidId);

    $response = $client->getResponse();
    $content = $response->getContent();
    echo $content;
    $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    $this->assertJson($content);

    $responseData = json_decode($content, true);

    $this->assertArrayHasKey('message', $responseData);
    $this->assertEquals('Order not found', $responseData['message']);
}

public function testUpdateOrderMissingFields(): void
{
    $client = static::createClient();

    $product1 = new Product('Product 1');
    $product2 = new Product('Product 2');

    $entityManager = $client->getContainer()->get('doctrine')->getManager();
    $entityManager->persist($product1);
    $entityManager->persist($product2);
    $entityManager->flush();
   
    $order = new Order();
    $entityManager->persist($order);
    $entityManager->flush();
    $orderId = $order->getId();

    $requestData = [
        'items' => [
            [
                'quantity' => 1,
                'product' => [
                    'id' => $product1->getId(),
                ],
            ],
            [
                'product' => [
                    'id' => $product2->getId(),
                ],
            ],
        ],
    ];

    $client->request('PUT', '/orders/' . $orderId, [], [], [], json_encode($requestData));

    $response = $client->getResponse();
    $content = $response->getContent();

    echo $content;

    $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
}
public function testDeleteOrder(): void
{
    $client = static::createClient();

    $order = new Order();
    $entityManager = $client->getContainer()->get('doctrine')->getManager();
    $entityManager->persist($order);

    $product1 = new Product('Product 1');
    $product2 = new Product('Product 2');

    $orderItem1 = new OrderItem($product1, $order, 1);
    $orderItem2 = new OrderItem($product2, $order, 2);

    $entityManager->persist($product1);
    $entityManager->persist($product2);
    $entityManager->persist($orderItem1);
    $entityManager->persist($orderItem2);

    $entityManager->flush();

    $orderId = $order->getId();

    $client->request('DELETE', '/orders/' . $orderId);

    $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

    $order = $entityManager->getRepository(Order::class)->find($orderId);
    $orderItems = $entityManager->getRepository(OrderItem::class)->findBy(['order' => $order]);

    $this->assertNull($order);
    $this->assertEmpty($orderItems);
}
public function testConfirmOrder(): void
{
    $client = static::createClient();

    $order = new Order();
    $entityManager = $client->getContainer()->get('doctrine')->getManager();
    $entityManager->persist($order);
    $entityManager->flush();
    $orderId = $order->getId();

    $client->request('PUT', '/orders/' . $orderId . '/confirm');

    $this->assertEquals(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

    $updatedOrder = $entityManager->getRepository(Order::class)->find($orderId);
    $this->assertTrue($updatedOrder->getStatus());
}
}