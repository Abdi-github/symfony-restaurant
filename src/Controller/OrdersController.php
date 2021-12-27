<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CartRepository;
use App\Repository\DishRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Stripe;

class OrdersController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    // #[Route('/order', name: 'order')]
    // public function index(): Response
    // {
    //     return $this->render('order/index.html.twig', [
    //         'controller_name' => 'OrderController',
    //     ]);
    // }
    #[Route('/order', name: 'order')]
    public function order(Request $request, $stripeAPI,  CartRepository $cr): Response
    {



        Stripe::setApiKey($stripeAPI);

        $table_no = $request->query->get('table_no');

        $carts = $cr->findBy(['table_no' => $table_no]);

        if ($carts) {
            foreach ($carts as $item) {

                $price = $item->getPrice();
                $name = $item->getProductName();
                $qty = $item->getQuantity();





                $line_items_data[] =   [
                    'price_data' => [
                        'currency' => 'CHF',
                        'unit_amount' => $price * 100,
                        'product_data' => [
                            'name' => $name,

                        ],
                    ],
                    'quantity' =>  $qty,
                ];
            }
        }

        // \dd($line_items_data);

        $stripeSession = \Stripe\Checkout\Session::create([
            'line_items' => [$line_items_data],

            'metadata' => [
                'order_no' => \uniqid(),
                'table_no' => $table_no,
                'customer_id' => 'table' . '_' . $table_no . '_' . \uniqid(),
            ],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        // \dd($stripeSession);

        foreach ($carts as $item) {
            $order_item = new OrderItem();

            $order_item->setProductId($item->getProductId());
            $order_item->setName($item->getProductName());
            $order_item->setQty($item->getQuantity());

            $order_item->setPrice($item->getPrice());
            $order_item->setExCheese($item->getExCheese());
            $order_item->setExOnion($item->getExOnion());

            $order_item->setCustomerId($stripeSession->metadata->customer_id);

            $em = $this->getDoctrine()->getManager();

            $em->persist($order_item);
            $em->flush();
        }

        return $this->redirect($stripeSession->url, 303);
    }



    #[Route('/webhook', name: 'webhook')]
    public function webhook($stripeAPI, OrderRepository $or, OrderItemRepository $oir, CartRepository $cr): Response
    {

        $session = $this->requestStack->getSession();

        // \dump($session->get('cart_by_table'));

        Stripe::setApiKey($stripeAPI);

        $em = $this->getDoctrine()->getManager();
        $json_str = \file_get_contents('php://input');
        $event = \json_decode($json_str);

        $type = $event->type;
        $object = $event->data->object;



        // Handle the event
        switch ($type) {

            case 'checkout.session.completed':
                // \dump($object);
                $order = new Order();

                $order->setOrderNo($object->metadata->order_no);
                $order->setTableNo($object->metadata->table_no);
                $order->setPaymentIntentId($object->payment_intent);
                $order->setPrice($object->amount_total / 100);
                $order->setPaymentStatus($object->payment_status);

                $orderItems = $oir->findBy(['customer_id' => $object->metadata->customer_id]);

                foreach ($orderItems as $orderItem) {
                    # code...
                    $orderItem->setOrders($order);
                }


                $em->persist($orderItem);

                $em->persist($order);

                $em->flush();

                $carts = $cr->findBy(['table_no' => $object->metadata->table_no]);

                foreach ($carts as $item) {
                    # code...
                    $em->remove($item);

                    $em->flush();
                }











                break;
                // ... handle other event types



                break;

            default:
                // Unexpected event type

                return new Response(Response::HTTP_BAD_REQUEST);
                exit();
        }

        return new Response(Response::HTTP_OK);
    }



    #[Route('/success-url', name: 'success_url')]
    public function successUrl(): Response
    {
        $session = $this->requestStack->getSession();








        return $this->render('payment/success.html.twig');
    }


    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {

        return $this->render('payment/cancel.html.twig', []);
    }
}
