<?php
defined('ABSPATH') || exit;

/**
 * GET /wp-json/aptaive/v1/posts
 */
function aptaive_get_posts(WP_REST_Request $request)
{
    // Pagination
    $page     = max(1, (int) $request->get_param('page'));
    $per_page = min(50, max(1, (int) ($request->get_param('per_page') ?? 10)));

    // Sorting
    $orderby = $request->get_param('orderby') ?: 'date';
    $order   = strtoupper($request->get_param('order') ?: 'DESC');

    if (!in_array($orderby, ['date', 'title'], true)) {
        $orderby = 'date';
    }
    if (!in_array($order, ['ASC', 'DESC'], true)) {
        $order = 'DESC';
    }

    // Search
    $search = sanitize_text_field($request->get_param('search'));

    // Category list
    $category_param = $request->get_param('category');
    $categories     = [];

    if (!empty($category_param)) {
        $categories = array_map('trim', explode(',', $category_param));
    }

    // Query args
    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        's'              => $search,
        'orderby'        => $orderby,
        'order'          => $order,
    ];

    if (!empty($categories)) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'category',
                'field'    => is_numeric($categories[0]) ? 'term_id' : 'slug',
                'terms'    => $categories,
            ],
        ];
    }

    $query = new WP_Query($args);

    $posts = [];

    foreach ($query->posts as $post) {
        $posts[] = [
            'id'    => $post->ID,
            'title' => get_the_title($post),
            'slug'  => $post->post_name,
            'date'  => get_the_date('c', $post),
            'image' => get_the_post_thumbnail_url($post->ID, 'medium') ?: null,
        ];
    }

    // 👉 TRẢ ĐÚNG SHAPE CHO FREEZED
    return aptaive_response([
        'page'      => $page,
        'perPage'   => $per_page,
        'total'     => (int) $query->found_posts,
        'totalPage' => (int) $query->max_num_pages,
        'hasMore'   => $page < $query->max_num_pages,
        'posts'     => $posts,
    ]);
}
