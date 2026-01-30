<?php
defined('ABSPATH') || exit;

function aptaive_get_post_categories(WP_REST_Request $request)
{
    $terms = get_terms([
        'taxonomy'   => 'category',
        'hide_empty' => false,
        'orderby'    => 'term_order',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($terms)) {
        return aptaive_response([], 'Failed to fetch post categories', 500);
    }

    $data = [];

    foreach ($terms as $term) {
        if ($term instanceof WP_Term) {
            $data[] = aptaive_format_post_category($term);
        }
    }

    return aptaive_response($data);
}

function aptaive_format_post_category(WP_Term $term)
{
    return [
        'id'          => (int) $term->term_id,
        'name'        => $term->name,
        'slug'        => $term->slug,
        'parent'      => (int) $term->parent,
        'count'       => (int) $term->count,
        'description' => $term->description,
    ];
}
