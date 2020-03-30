<?php

namespace SGI\Theme\WooCommerce\Product;

use \SGI\Theme\WooCommerce\Core\Calculator as Calculator;

use function \SGI\Theme\WooCommerce\Core\Utils\fetch_unit_measure as fetch_unit_measure;

class Price
{

    private $calculator;

    public function __construct()
    {

        $this->calculator = Calculator::get_instance();

        add_filter('woocommerce_product_get_price', array( $this, 'rabat_price_set' ), 10, 2 );
        add_filter('woocommerce_product_get_regular_price', array( $this, 'rabat_price_set' ), 10, 2 );

        add_filter('woocommerce_cart_item_price', [&$this,'cart_single_price_set'], 999, 3);

        add_filter('sgi/theme/woocommerce/cart/show_rabat', [&$this, 'show_product_rabat'], 20, 1);

    }

    public function cart_single_price_set($price, $cart_item, $cart_key)
    {

        $price = $this->calculator->get_product_price(
            $cart_item['data']->get_price(),
            $cart_item['data']->get_id()
        );

        return \wc_price($price);

    }


    public function rabat_price_set($price, $product)
    {

        if (is_admin())
            return $price;

        $product_id  = $product->get_id();
        $quantity    = fetch_unit_measure($product_id);
        $rabat_price = $this->calculator->get_product_price($price, $product_id);

        return $rabat_price * $quantity

    }

    public function show_product_rabat($product_id)
    {

        $rabat = $this->calculator->get_product_rabat($product_id);

        return sprintf(
            '%.2f %%',
            floatval($rabat['ProdRabat'])
        );

    }


}