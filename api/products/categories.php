<?php
defined('ABSPATH') || exit;

function aptaive_get_product_categories(WP_REST_Request $request)
{
    if (!class_exists('WooCommerce')) {
        return aptaive_response([], 'WooCommerce not installed', 400);
    }

    $terms = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'meta_key'   => 'order',
        'orderby'    => 'meta_value_num',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($terms)) {
        return aptaive_response([], 'Failed to fetch categories', 500);
    }

    $data = [];

    foreach ($terms as $term) {
        if ($term instanceof WP_Term) {
            $data[] = aptaive_format_category($term);
        }
    }

    return aptaive_response($data);
}

function aptaive_format_category(WP_Term $term)
{
    $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);

    return [
        'id'     => (int) $term->term_id,
        'name'   => $term->name,
        'slug'   => $term->slug,
        'parent' => (int) $term->parent, // ⭐ dùng cho client xếp children
        'count'  => (int) $term->count,
        'image'  => $thumbnail_id
            ? wp_get_attachment_image_url($thumbnail_id, 'medium')
            : null,
    ];
}
