<?php

if (!defined('ABSPATH')) {
    exit;
}

function aptaive_get_orders(WP_REST_Request $request)
{
    return aptaive_handle(function () use ($request) {

        if (!class_exists('WooCommerce')) {
            return aptaive_response([], 'WooCommerce not installed', 400);
        }

        /* =====================
         * Auth
         * ===================== */
        $user = aptaive_auth_user();

        /* =====================
         * Pagination
         * ===================== */
        $page = max(1, (int) $request->get_param('page'));

        $perPage = (int) $request->get_param('per_page');
        if ($perPage <= 0) {
            $perPage = 10;
        }
        $perPage = min(50, $perPage);

        /* =====================
         * Order statuses
         * ===================== */
        $statuses = [
            'pending',
            'processing',
            'on-hold',
            'completed',
            'cancelled',
            'refunded',
            'failed',
        ];

        /* =====================
         * Count total
         * ===================== */
        $countQuery = new WC_Order_Query([
            'customer_id' => $user->ID,
            'status'      => $statuses,
            'return'      => 'ids',
            'limit'       => -1,
        ]);

        $total = count($countQuery->get_orders());
        $totalPage = (int) ceil($total / $perPage);
        $hasMore = $page < $totalPage;

        /* =====================
         * Query orders
         * ===================== */
        $query = new WC_Order_Query([
            'customer_id' => $user->ID,
            'status'      => $statuses,
            'limit'       => $perPage,
            'page'        => $page,
            'orderby'     => 'date',
            'order'       => 'DESC',
            'return'      => 'objects',
        ]);

        /** @var WC_Order[] $orders */
        $orders = $query->get_orders();

        $result = [];

        foreach ($orders as $order) {

            $items = [];

            foreach ($order->get_items('line_item') as $item) {
                /** @var WC_Order_Item_Product $item */
                $product = $item->get_product();

                // Image
                $image = null;
                if ($product && $product->get_image_id()) {
                    $image = wp_get_attachment_image_url(
                        $product->get_image_id(),
                        'medium'
                    );
                }

                // Variation text
                $variationText = null;
                $meta = $item->get_meta_data();

                if (!empty($meta)) {
                    $parts = [];
                    foreach ($meta as $m) {
                        if (!empty($m->value)) {
                            $parts[] = wc_clean($m->value);
                        }
                    }
                    if ($parts) {
                        $variationText = implode(', ', $parts);
                    }
                }

                $items[] = [
                    'id'        => $item->get_id(),
                    'productId' => $item->get_product_id(),
                    'name'      => $item->get_name(),
                    'image'     => $image,
                    'variation' => $variationText,
                    'quantity'  => (int) $item->get_quantity(),
                    'total'     => (float) $item->get_total(),
                ];
            }

            $result[] = [
                'id'          => $order->get_id(),
                'status'      => $order->get_status(),
                'statusLabel' => wc_get_order_status_name($order->get_status()),
                'total'       => (float) $order->get_total(),
                'items'       => $items,
            ];
        }

        /* =====================
         * FLAT RESPONSE – FREEZED
         * ===================== */
        return aptaive_response([
            'page'      => $page,
            'perPage'   => $perPage,
            'total'     => $total,
            'totalPage' => $totalPage,
            'hasMore'   => $hasMore,
            'orders'    => $result,
        ]);
    });
}
