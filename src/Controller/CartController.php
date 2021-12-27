<?php

namespace App\Controller;

// use App\Entity\Cart;
// use App\Repository\CartRepository;

use App\Entity\Dish;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    #[Route('/cart', name: 'cart')]
    public function index(): Response
    {
        $session = $this->requestStack->getSession();

        $table_no = $session->get('table');



        $cart = $session->get('cart', []);

        if ($cart) {
            $filter = array($table_no);

            foreach ($cart as $item) {
                # code...
                $cartByTable = array_filter($cart, function ($var) use ($filter) {
                    return in_array($var['table_no'], $filter);
                });
            }



            // \dd($cartByTable);



            // \dd($cartByTableNo);

            // \dd($cart);
            $total_price = 0;
            $total_qty = 0;



            foreach ($cartByTable as $item) {
                $price  = $item['sub_total'];
                $qty = $item['quantity'];
                $total_qty += $qty;
                $total_price += $price;
            }

            $session->set('table_cart_total_price', $total_price);
            $session->set('table_cart_total_qty', $total_qty);
            $session->set('cart_by_table', $cartByTable);
        }






        // foreach ($cart as $item) {
        //     // \dd($item);
        //     // \dd($item['table_no']);
        //     if ($table_no == $item['table_no']) {
        //         # code...
        //         $price  = $item['sub_total'];
        //         $total += $price;
        //     }
        // }


        // \dd($total);




        return $this->render('cart/index.html.twig', []);
    }


    #[Route('/cart/store/{table_no}/{product_id}', name: 'add.to.cart')]
    public function addToCart(Request $request, $product_id, $table_no,  DishRepository $dr)
    {

        // dd($table_no);
        // dd($request->attributes->get('table_no'));
        $session = $this->requestStack->getSession();










        $cart = $session->get('cart', []);

        // \dd($cart);

        $product = $dr->findOneBy(['id' => $product_id]);
        if ($product->getDiscountedPrice() > 0) {
            $price = $product->getDiscountedPrice();
        } else {
            $price = $product->getPrice();
        }

        $qty = \intval($request->request->get('product_qty'));

        $sub_total_price = $qty * $price;



        $cheese = $request->request->get('extra_cheese');
        $onion = $request->request->get('extra_onion');




        # code
        // if cart is empty then this the first product
        if (!$cart) {

            $cart = [
                $product_id => [
                    "table_no" => $table_no,
                    "product_id" => $product_id,
                    "name" => $product->getName(),
                    "quantity" => $qty,
                    "price" => $price,
                    'sub_total' => $sub_total_price,
                    "image" => $product->getImage(),
                    'cheese' => $cheese,
                    'onion' => $onion
                ]
            ];

            $session->set('cart', $cart);

            // \dd($cart);

            $filter = array($table_no);

            foreach ($cart as $item) {
                # code...
                $cartByTable = array_filter($cart, function ($var) use ($filter) {
                    return in_array($var['table_no'], $filter);
                });
            }

            $session->set('cart_by_table', $cartByTable);

            $total_price = 0;
            $total_qty = 0;



            foreach ($cartByTable as $item) {
                $price  = $item['sub_total'];
                $qty = $item['quantity'];
                $total_qty += $qty;
                $total_price += $price;
            }

            $session->set('table_cart_total_price', $total_price);
            $session->set('table_cart_total_qty', $total_qty);



            // // \dd($session->get('cart'));
            $cart_by_table = $session->get('cart_by_table', []);
            $total_price = $session->get('table_cart_total_price');
            $total_qty =  $session->get('table_cart_total_qty');
            $count = \count($cart_by_table);


            return $this->json([
                'status' => 'ok', 'cart_by_table' => $cart_by_table,
                'total_price' => $total_price, 'total_qty' => $total_qty, 'count' => $count
            ]);
        }

        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$product_id])) {



            $cart[$product_id]['quantity'] += $qty;
            $cart[$product_id]['cheese'] = $cheese;
            $cart[$product_id]['onion'] = $onion;
            $cart[$product_id]['sub_total'] += $sub_total_price;



            $session->set('cart', $cart);

            // \dd($session->get('cart'));
            $this->getCartItems();
            $cart_by_table = $session->get('cart_by_table');
            $total_price = $session->get('table_cart_total_price');
            $total_qty =  $session->get('table_cart_total_qty');
            $count = \count($cart_by_table);


            return $this->json([
                'status' => 'ok', 'cart_by_table' => $cart_by_table,
                'total_price' => $total_price, 'total_qty' => $total_qty, 'count' => $count
            ]);
        }

        // if item not exist in cart then add to cart with quantity = 1
        $cart[$product_id] = [
            "table_no" => $table_no,
            "product_id" => $product_id,
            "name" => $product->getName(),
            "quantity" => $qty,
            "price" => $price,
            'sub_total' => $qty * $price,
            "image" => $product->getImage(),
            'cheese' => $cheese,
            'onion' => $onion
        ];

        $session->set('cart', $cart);

        $this->getCartItems();

        $cart_by_table = $session->get('cart_by_table');
        $total_price = $session->get('table_cart_total_price');
        $total_qty =  $session->get('table_cart_total_qty');
        $count = \count($cart_by_table);

        return $this->json([
            'status' => 'ok', 'cart_by_table' => $cart_by_table,
            'total_price' => $total_price, 'total_qty' => $total_qty, 'count' => $count
        ]);
    }

    /*

    #[Route('/cart/remove/{table_no}/{product_id}', name: 'remove.cart')]
    public function removeCartItem(Request $request, $product_id, $table_no)
    {
        $session = $this->requestStack->getSession();





        $cart = $session->get('cart');
        if ($cart[$product_id]['table_no'] == $table_no) {
            unset($cart[$product_id]);
        }

        $session->set('cart', $cart);

        $this->getCartItems();







        // \dd($cartByTable);
        // \dd($session->get('cart_by_table'));




        // \dd($cartByTable);









        return $this->render('cart/index.html.twig');
    }

    */

    /*

    #[Route('/cart/update/{table_no}/{product_id}', name: 'update.cart')]
    public function updateCart(Request $request, $product_id, $table_no,  DishRepository $dr)
    {

        $session = $this->requestStack->getSession();

        $cart = $session->get('cart', []);

        $qty = $request->request->get('product_qty');

        $product = $dr->findOneBy(['id' => $product_id]);
        if ($product->getDiscountedPrice() > 0) {
            $price = $product->getDiscountedPrice();
        } else {
            $price = $product->getPrice();
        }

        $sub_total_price = $qty * $price;




        $cart[$product_id]['quantity'] = $qty;
        $cart[$product_id]['sub_total'] = $sub_total_price;
        $cart[$product_id]['table_no'] = $table_no;



        $session->set('cart', $cart);

        // \dd($session->get('cart'));
        $this->getCartItems();
        $cart_by_table = $session->get('cart_by_table');
        $total_price = $session->get('table_cart_total_price');
        $total_qty =  $session->get('table_cart_total_qty');
        $count = \count($cart_by_table);


        return $this->json([
            'status' => 'ok', 'cart_by_table' => $cart_by_table,
            'total_price' => $total_price, 'total_qty' => $total_qty, 'count' => $count
        ]);
    }

    */



    public function getCartItems()
    {
        $session = $this->requestStack->getSession();

        $table_no = $session->get('table');



        $cart = $session->get('cart', []);
        $filter = array($table_no);

        foreach ($cart as $item) {
            # code...
            $cartByTable = array_filter($cart, function ($var) use ($filter) {
                return in_array($var['table_no'], $filter);
            });
        }




        // \dd($cart);
        $total_price = 0;
        $total_qty = 0;

        if ($cartByTable) {
            # code...
            foreach ($cartByTable as $item) {
                $price  = $item['sub_total'];
                $qty = $item['quantity'];
                $total_qty += $qty;
                $total_price += $price;
            }

            $session->set('table_cart_total_price', $total_price);
            $session->set('table_cart_total_qty', $total_qty);
            $session->set('cart_by_table', $cartByTable);
        }





        return $this->render('common/_cartBox.html.twig', []);
    }
}
