<?php
defined('ABSPATH') || exit;

function aptaive_get_product_detail(WP_REST_Request $request)
{
    if (!class_exists('WooCommerce')) {
        return aptaive_response([], 'WooCommerce not installed', 400);
    }

    $product_id = (int) $request->get_param('id');
    if (!$product_id) {
        return aptaive_response([], 'Product ID is required', 400);
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return aptaive_response([], 'Product not found', 404);
    }

    /** PRICE (reuse logic như list) */
    $price_data = aptaive_get_product_list_price($product);

    /** IMAGES */
    $images = [];

    // Ảnh đại diện
    if ($product->get_image_id()) {
        $images[] = wp_get_attachment_image_url(
            $product->get_image_id(),
            'large'
        );
    }

    // Gallery
    foreach ($product->get_gallery_image_ids() as $image_id) {
        $images[] = wp_get_attachment_image_url($image_id, 'large');
    }

    /** STOCK */
    $stock_status = $product->get_stock_status(); // instock | outofstock | onbackorder
    $in_stock     = $product->is_in_stock();

    /** VARIATIONS */
    $variations = [];

    if ($product->is_type('variable')) {
        foreach ($product->get_available_variations() as $variation_data) {
            $variation = wc_get_product($variation_data['variation_id']);
            if (!$variation) continue;

            $variation_price = aptaive_get_product_list_price($variation);

            $variations[] = [
                'id'    => $variation->get_id(),
                'sku'   => $variation->get_sku(),
                'price' => $variation_price['price'] ?? null,
                'regularPrice' => $variation_price['regularPrice'] ?? null,
                'onSale' => $variation_price['onSale'] ?? false,
                'inStock' => $variation->is_in_stock(),
                'attributes' => $variation->get_attributes(),
                'image' => $variation->get_image_id()
                    ? wp_get_attachment_image_url(
                        $variation->get_image_id(),
                        'medium'
                    )
                    : null,
            ];
        }
    }

    /** ATTRIBUTES (dùng hiển thị chọn size, màu…) */
    /** ATTRIBUTES (dùng hiển thị chọn size, màu…) */
    $attributes = [];

    foreach ($product->get_attributes() as $attribute) {
        if ($attribute->is_taxonomy()) {

            $taxonomy = $attribute->get_name(); // pa_kich-thuoc
            $slug = wc_attribute_taxonomy_slug($taxonomy); // kich-thuoc

            $terms = wc_get_product_terms(
                $product->get_id(),
                $taxonomy,
                ['fields' => 'names']
            );

            $attributes[] = [
                'name' => wc_attribute_label($taxonomy), // Kích thước
                'slug' => $slug,                          // kich-thuoc
                'options' => $terms,                      // [5KG,10KG,20KG]
            ];
        } else {
            // Custom attribute (non-taxonomy)
            $attributes[] = [
                'name' => $attribute->get_name(),
                'slug' => sanitize_title($attribute->get_name()),
                'options' => $attribute->get_options(),
            ];
        }
    }

    return aptaive_response([
        'id'    => $product->get_id(),
        'name'  => $product->get_name(),
        'type'  => $product->get_type(), // simple | variable

        /** PRICE */
        'price'         => $price_data['price'] ?? null,
        'regularPrice' => $price_data['regularPrice'] ?? null,
        'onSale'       => $price_data['onSale'] ?? false,

        /** STOCK */
        'inStock'     => $in_stock,
        'stockStatus' => $stock_status,

        /** CONTENT */
        'shortDescription' => wpautop($product->get_short_description()),
        'description'      => wpautop($product->get_description()),

        /** MEDIA */
        'images' => $images,

        /** VARIANTS */
        'attributes' => $attributes,
        'variations' => $variations,
    ]);
}
