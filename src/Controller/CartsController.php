<?php

namespace App\Controller;

// use App\Entity\Cart;
// use App\Repository\CartRepository;

use App\Entity\Dish;
use App\Entity\Cart;
use App\Repository\CartRepository;
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartsController extends AbstractController
{

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    #[Route('/cart', name: 'cart')]
    public function index(CartRepository $cr, DishRepository $dr): Response
    {
        $session = $this->requestStack->getSession();

        $table_no = $session->get('table');

        $carts = $cr->findBy(['table_no' => $table_no]);

        $total_price = 0;
        if ($carts) {
            foreach ($carts as $cart) {
                $item_subtotal[] = $cart->getSubTotal();
            }

            $total_price = array_sum($item_subtotal);
        }








        return $this->render('cart/index.html.twig', [
            'carts' => $carts,
            'total_price' => $total_price
            // 'products' => $products
        ]);
    }


    #[Route('/cart/add/{table_no}/{product_id}', name: 'add.cart')]
    public function addToCart(Request $request, $product_id, $table_no, CartRepository $cr,  DishRepository $dr)
    {



        // dd($table_no);
        // dd($request->attributes->get('table_no'));
        $session = $this->requestStack->getSession();

        // $table_no = $session->get('table');

        // dd($request->request->get('product_qty'));

        $existing_carts = $cr->findOneBy(['table_no' => $table_no]);


        $qty = $request->request->get('product_qty');
        $extra_cheese = $request->request->get('extra_cheese');
        $extra_onion = $request->request->get('extra_onion');
        $product = $dr->findOneBy(['id' => $product_id]);

        if ($product->getDiscountedPrice() > 0) {
            $price = $product->getDiscountedPrice();
        } else {
            $price = $product->getPrice();
        }

        $name = $product->getName();
        $image = $product->getImage();

        $existing_carts = $cr->findBy(['table_no' => $table_no]);

        if ($existing_carts) {
            foreach ($existing_carts as $existing_cart) {
                $product_ids[] = $existing_cart->getProductId();
            }

            if (
                \in_array($product_id, $product_ids)
                && $existing_cart->getExCheese() == $extra_cheese
                && $existing_cart->getExOnion() == $extra_onion
            ) {
                // \dd('exist all same add qty');
                $existing_qty = $existing_cart->getQuantity();
                $added_qty = $qty;
                $new_qty = $existing_qty + $added_qty;

                $existing_cart->setQuantity($new_qty);
                $existing_cart->setSubTotal($new_qty * $price);

                $em = $this->getDoctrine()->getManager();

                $em->persist($existing_cart);
                $em->flush();
            } elseif (
                (\in_array($product_id, $product_ids)
                    && ($existing_cart->getExCheese() != $extra_cheese
                        || $existing_cart->getExOnion() != $extra_onion))
                || !in_array($product_id, $product_ids)

            ) {
                // \dd('exist but different extra or new product id - new cart');
                $cart = new Cart();

                $cart->setTableNo($table_no);
                $cart->setProductId($product_id);
                $cart->setProductName($name);
                $cart->setImage($image);
                $cart->setQuantity($qty);
                $cart->setPrice($price);
                $cart->setSubTotal($qty * $price);
                $cart->setExCheese($extra_cheese);
                $cart->setExOnion($extra_onion);

                $em = $this->getDoctrine()->getManager();

                $em->persist($cart);
                $em->flush();
            }
        } else {
            // \dd('Not exist - new cart');
            $cart = new Cart();

            $cart->setTableNo($table_no);
            $cart->setProductId($product_id);
            $cart->setProductName($name);
            $cart->setImage($image);
            $cart->setQuantity($qty);
            $cart->setPrice($price);
            $cart->setSubTotal($qty * $price);
            $cart->setExCheese($extra_cheese);
            $cart->setExOnion($extra_onion);

            $em = $this->getDoctrine()->getManager();

            $em->persist($cart);
            $em->flush();
        }

        $carts = $cr->findBy(['table_no' => $table_no]);

        foreach ($carts as $cart) {
            $item_subtotal[] = $cart->getSubTotal();
            $item_qty[] = $cart->getQuantity();
        }

        $total_price = array_sum($item_subtotal);
        $total_qty = array_sum($item_qty);




        return $this->json([
            'carts' => $carts,
            'total_price' => $total_price,
            'total_qty' => $total_qty,
            'count' => \count($carts)

        ]);
    }





    #[Route('/cart/remove/{table_no}/{product_id}', name: 'remove.cart')]
    public function removeCartItem(Request $request, $product_id, $table_no, CartRepository $cr)
    {
        $session = $this->requestStack->getSession();

        // \dd($request);


        $cart = $cr->findOneBy(['table_no' => $table_no, 'product_id' => $product_id]);

        if ($cart) {
            $em = $this->getDoctrine()->getManager();

            $em->remove($cart);
            $em->flush();
        }



        $carts = $cr->findBy(['table_no' => $table_no]);

        foreach ($carts as $cart) {
            $item_subtotal[] = $cart->getSubTotal();
            $item_qty[] = $cart->getQuantity();
        }

        $total_price = array_sum($item_subtotal);
        $total_qty = array_sum($item_qty);


        return $this->render('cart/index.html.twig', [
            'carts' => $carts,
            'total_price' => $total_price,
            'total_qty' => $total_qty,
            'count' => \count($carts)
        ]);
    }

    #[Route('/cart/update/{table_no}/{product_id}', name: 'update.cart')]
    public function updateCart(Request $request, $product_id, $table_no,  CartRepository $cr)
    {

        // \dd($request->request->get('product_qty'));

        $qty = $request->request->get('product_qty');

        $cart = $cr->findOneBy(['table_no' => $table_no, 'product_id' => $product_id]);

        if ($cart) {

            $price = $cart->getPrice();

            $cart->setQuantity($qty);
            $cart->setSubTotal($qty * $price);

            $em = $this->getDoctrine()->getManager();

            $em->persist($cart);
            $em->flush();
        }


        $carts = $cr->findBy(['table_no' => $table_no]);

        foreach ($carts as $cart) {
            $item_subtotal[] = $cart->getSubTotal();
            $item_qty[] = $cart->getQuantity();
        }

        $total_price = array_sum($item_subtotal);
        $total_qty = array_sum($item_qty);




        return $this->json([
            'carts' => $carts,
            'total_price' => $total_price,
            'total_qty' => $total_qty,
            'count' => \count($carts)

        ]);
    }



    public function getCartItems(CartRepository $cr)
    {
        $session = $this->requestStack->getSession();

        $carts = $cr->findBy(['table_no' => $session->get('table')]);

        $total_price = 0;
        $total_qty = 0;
        if ($carts) {
            # code...
            foreach ($carts as $cart) {
                $item_subtotal[] = $cart->getSubTotal();
                $item_qty[] = $cart->getQuantity();
            }

            $total_price = array_sum($item_subtotal);
            $total_qty = array_sum($item_qty);
        }





        return $this->render('common/_cartBox.html.twig', [
            'carts' => $carts,
            'total_price' => $total_price,
            'total_qty' => $total_qty
        ]);
    }
}
