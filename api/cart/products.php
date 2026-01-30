<?php

defined('ABSPATH') || exit;

function aptaive_get_cart_products(WP_REST_Request $request)
{
    if (!class_exists('WooCommerce') || !function_exists('WC')) {
        return aptaive_response([], 'WooCommerce not available', 400);
    }

    $items = $request->get_param('items');

    if (!is_array($items) || empty($items)) {
        return aptaive_response([], 'Cart items empty', 400);
    }

    $result = [];

    foreach ($items as $item) {
        $product_id   = isset($item['productId']) ? intval($item['productId']) : 0;
        $variation_id = isset($item['variationId']) ? intval($item['variationId']) : 0;
        $quantity     = isset($item['quantity']) ? intval($item['quantity']) : 1;

        if (!$product_id || $quantity <= 0) {
            continue;
        }

        // ========================
        // LOAD PRODUCT / VARIATION
        // ========================
        if ($variation_id) {
            $product = wc_get_product($variation_id);
        } else {
            $product = wc_get_product($product_id);
        }

        if (!$product) {
            continue;
        }

        // ========================
        // IMAGE
        // ========================
        $image_id = $product->get_image_id();
        $image    = $image_id
            ? wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail')
            : null;

        // ========================
        // PRICE
        // ========================
        $price         = $product->get_price();
        $regular_price = $product->get_regular_price();

        $price_value         = $price !== '' ? floatval($price) : null;
        $regular_price_value = $regular_price !== '' ? floatval($regular_price) : null;

        // ========================
        // VARIATION ATTRIBUTES
        // ========================
        $variations = [];

        if ($product->is_type('variation')) {
            foreach ($product->get_attributes() as $taxonomy => $term_slug) {
                $label = wc_attribute_label($taxonomy);
                $term  = get_term_by('slug', $term_slug, $taxonomy);

                $variations[] = [
                    'name'  => $label,
                    'value' => $term ? $term->name : $term_slug,
                ];
            }
        }

        // ========================
        // STOCK / SALE INFO
        // ========================
        $stock_status = $product->get_stock_status(); // instock | outofstock | onbackorder
        $max_quantity = $product->managing_stock()
            ? (int) $product->get_stock_quantity()
            : null;

        $on_sale = $product->is_on_sale();

        // ========================
        // TOTALPRICE (price * quantity)
        // ========================
        $totalPrice = $price_value !== null
            ? $price_value * $quantity
            : null;

        // ========================
        // BUILD RESPONSE
        // ========================
        $result[] = [
            'productId'    => $product_id,
            'variationId'  => $variation_id ?: null,
            'name'         => $product->get_name(),
            'image'        => $image,
            'price'        => $price_value,
            'regularPrice' => $regular_price_value,
            'quantity'     => $quantity,
            'totalPrice'        => $totalPrice,

            'stockStatus'  => $stock_status,
            'maxQuantity'  => $max_quantity,
            'onSale'       => $on_sale,

            'variations'   => $variations,
        ];
    }

    return aptaive_response($result);
}
