<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;

class CartService
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function cartInfo()
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



        // \dd($cartByTable);

        $session->set('cart_by_table', $cartByTable);

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
    }
}
