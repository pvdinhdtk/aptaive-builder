<?php
defined('ABSPATH') || exit;

function aptaive_get_products(WP_REST_Request $request)
{
    if (!class_exists('WooCommerce')) {
        return aptaive_response([], 'WooCommerce not installed', 400);
    }

    /* =====================
     * Pagination
     * ===================== */
    $page     = max(1, (int) $request->get_param('page'));
    $per_page = min(50, max(1, (int) ($request->get_param('per_page') ?? 10)));

    /* =====================
     * Sorting
     * ===================== */
    $orderby = $request->get_param('orderby') ?: 'date';
    $order   = strtoupper($request->get_param('order') ?: 'DESC');

    $allowed_orderby = ['date', 'price', 'title', 'rating', 'popularity'];
    if (!in_array($orderby, $allowed_orderby, true)) {
        $orderby = 'date';
    }
    if (!in_array($order, ['ASC', 'DESC'], true)) {
        $order = 'DESC';
    }

    /* =====================
     * Search
     * ===================== */
    $search = sanitize_text_field($request->get_param('search'));

    /* =====================
     * Category (single | multiple)
     * ===================== */
    $category_param = $request->get_param('category');
    $categories = [];

    if (!empty($category_param)) {
        $categories = array_filter(
            array_map('trim', explode(',', (string) $category_param))
        );
    }

    /* =====================
     * Query args
     * ===================== */
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        's'              => $search,
        'orderby'        => $orderby === 'price' ? 'meta_value_num' : $orderby,
        'order'          => $order,
    ];

    if ($orderby === 'price') {
        $args['meta_key'] = '_price';
    }

    if (!empty($categories)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'product_cat',
                'field'    => is_numeric($categories[0]) ? 'term_id' : 'slug',
                'terms'    => $categories,
                'operator' => 'IN',
            ],
        ];
    }

    /* =====================
     * Query
     * ===================== */
    $query = new WP_Query($args);

    $products = [];

    foreach ($query->posts as $post) {
        $product = wc_get_product($post->ID);
        if (!$product) continue;

        $price_data = aptaive_get_product_list_price($product);

        $products[] = [
            'id'           => $product->get_id(),
            'name'         => $product->get_name(),
            'type'         => $product->get_type(),
            'price'        => $price_data['price'] ?? null,
            'regularPrice' => $price_data['regularPrice'] ?? null,
            'onSale'       => $price_data['onSale'] ?? false,
            'image'        => wp_get_attachment_image_url(
                $product->get_image_id(),
                'medium'
            ),
        ];
    }

    /* =====================
     * Response (FLAT)
     * ===================== */
    return aptaive_response([
        'page'      => $page,
        'perPage'   => $per_page,
        'total'     => (int) $query->found_posts,
        'totalPage' => (int) $query->max_num_pages,
        'hasMore'   => $page < $query->max_num_pages,
        'products'  => $products,
    ]);
}


/* =====================
 * Price helper
 * ===================== */
function aptaive_get_product_list_price(WC_Product $product)
{
    // VARIABLE PRODUCT
    if ($product->is_type('variable')) {
        $prices = $product->get_variation_prices(true);

        if (empty($prices['price']) || empty($prices['regular_price'])) {
            return null;
        }

        return [
            'price'         => (float) min($prices['price']),          // giá bán thấp nhất
            'regularPrice'  => (float) min($prices['regular_price']),  // giá gốc thấp nhất
            'onSale'        => $product->is_on_sale(),
            'type'          => 'variable',
        ];
    }

    // SIMPLE PRODUCT
    return [
        'price'         => (float) $product->get_price(),
        'regularPrice'  => (float) $product->get_regular_price(),
        'onSale'        => $product->is_on_sale(),
        'type'          => 'simple',
    ];
}
