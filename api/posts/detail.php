<?php
defined('ABSPATH') || exit;

function aptaive_get_post_detail(WP_REST_Request $request)
{
    $post_id = (int) $request->get_param('id');

    if (!$post_id) {
        return aptaive_response([], 'Post ID is required', 400);
    }

    $post = get_post($post_id);

    if (!$post || $post->post_status !== 'publish') {
        return aptaive_response([], 'Post not found', 404);
    }

    /** FEATURED IMAGE */
    $image = null;
    if (has_post_thumbnail($post_id)) {
        $image = wp_get_attachment_image_url(
            get_post_thumbnail_id($post_id),
            'large'
        );
    }

    return aptaive_response([
        'id'    => $post->ID,
        'title' => get_the_title($post),
        'slug'  => $post->post_name,
        'image' => $image,
        'content' => apply_filters('the_content', $post->post_content),
        'date' => get_post_time('c', false, $post->ID),
    ]);
}
