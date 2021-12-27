<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
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

class OrderController extends AbstractController
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

    /*
    #[Route('/order', name: 'order')]
    public function order(Request $request, $stripeAPI, DishRepository $dr, OrderItemRepository $oir): Response
    {
        $session = $this->requestStack->getSession();



        $table_no = $session->get('table');

        $cart_detail = $session->get('cart_by_table');


        // \dd($cart_detail);



        foreach ($cart_detail as $item) {



            $product_id[] = $item['product_id'];

            $products = $dr->findBy(['id' => $product_id]);

            foreach ($products as $product) {
                if ($product->getDiscountedPrice() > 0) {
                    $price = $product->getDiscountedPrice();
                } else {
                    $price = $product->getPrice();
                }
            }


            $line_items_data[] =   [
                'price_data' => [
                    'currency' => 'CHF',
                    'unit_amount' => $price * 100,
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                ],
                'quantity' =>  $item['quantity'],
            ];
        }

        // \dd($product);
        // \dd($line_items_data);


        Stripe::setApiKey($stripeAPI);

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




        // $stripe = new \Stripe\StripeClient($stripeAPI);
        // $line_items_details = $stripe->checkout->sessions->allLineItems($stripeSession->id)->data;
        // \dd($stripeSession);

        // \dd($line_items_details);



        // \dd($sub_total_amounts);


        foreach ($cart_detail as $item) {
            $order_item = new OrderItem();

            $order_item->setProductId($item['product_id']);
            $order_item->setQty($item['quantity']);

            $order_item->setPrice($item['price']);
            $order_item->setExCheese($item['cheese']);
            $order_item->setExOnion($item['onion']);

            $order_item->setCustomerId($stripeSession->metadata->customer_id);

            $em = $this->getDoctrine()->getManager();

            $em->persist($order_item);
            $em->flush();
        }








        return $this->redirect($stripeSession->url, 303);
    }

*/

    #[Route('/webhook', name: 'webhook')]
    public function webhook($stripeAPI, OrderRepository $or, OrderItemRepository $oir): Response
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







                break;
                // ... handle other event types

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


        $cart = $session->get('cart');
        $table_no = $session->get('table');

        foreach ($cart as $key => $value) {
            $product_ids[] = $key;

            foreach ($product_ids as $product_id) {
                # code...
                if ($cart[$product_id]['table_no'] == $table_no) {
                    unset($cart[$product_id]);
                }
            }
        }

        $session->set('cart', $cart);







        return $this->render('payment/success.html.twig');
    }


    #[Route('/cancel-url', name: 'cancel_url')]
    public function cancelUrl(): Response
    {

        return $this->render('payment/cancel.html.twig', []);
    }
}
