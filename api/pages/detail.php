<?php
defined('ABSPATH') || exit;

function aptaive_get_page_detail(WP_REST_Request $request)
{
    $page_id = (int) $request->get_param('id');

    if (!$page_id) {
        return aptaive_response([], 'Page ID is required', 400);
    }

    $page = get_post($page_id);

    // Page không tồn tại hoặc không phải page
    if (
        !$page ||
        $page->post_type !== 'page' ||
        $page->post_status !== 'publish'
    ) {
        return aptaive_response([], 'Page not found', 404);
    }

    /** FEATURED IMAGE */
    $image = null;
    if (has_post_thumbnail($page_id)) {
        $image = wp_get_attachment_image_url(
            get_post_thumbnail_id($page_id),
            'large'
        );
    }

    return aptaive_response([
        'id'      => $page->ID,
        'title'   => get_the_title($page),
        'slug'    => $page->post_name,
        'image'   => $image,
        'content' => apply_filters(
            // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core WordPress content filter.
            'the_content',
            $page->post_content
        ),
        'date'    => get_post_time('c', false, $page->ID),
    ]);
}
