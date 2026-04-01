<?php
defined('ABSPATH') || exit;

function aptaive_get_products(WP_REST_Request $request)
{
    if (!class_exists('WooCommerce') || !function_exists('wc_get_products')) {
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
        'status'   => 'publish',
        'limit'    => $per_page,
        'page'     => $page,
        'paginate' => true,
        'return'   => 'objects',
        'orderby'  => $orderby,
        'order'    => $order,
    ];

    if ($search !== '') {
        $args['search'] = $search;
    }

    if (!empty($categories)) {
        $args['category'] = aptaive_resolve_product_category_slugs($categories);
    }

    /* =====================
     * Query
     * ===================== */
    $query = wc_get_products($args);

    $products = [];

    foreach ($query->products as $product) {
        if (!$product instanceof WC_Product) continue;
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
            ) ?: null,
        ];
    }

    /* =====================
     * Response (FLAT)
     * ===================== */
    return aptaive_response([
        'page'      => $page,
        'perPage'   => $per_page,
        'total'     => (int) $query->total,
        'totalPage' => (int) $query->max_num_pages,
        'hasMore'   => $page < $query->max_num_pages,
        'products'  => $products,
    ]);
}

function aptaive_resolve_product_category_slugs(array $categories): array
{
    $slugs = [];
    $term_ids = [];

    foreach ($categories as $category) {
        if (is_numeric($category)) {
            $term_ids[] = (int) $category;
            continue;
        }

        $slug = sanitize_title($category);
        if ($slug !== '') {
            $slugs[] = $slug;
        }
    }

    if (!empty($term_ids)) {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
            'include'    => $term_ids,
        ]);

        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                if ($term instanceof WP_Term && $term->slug !== '') {
                    $slugs[] = $term->slug;
                }
            }
        }
    }

    return array_values(array_unique($slugs));
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
