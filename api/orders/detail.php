<?php

defined('ABSPATH') || exit;

function aptaive_get_order_detail(WP_REST_Request $request)
{
    return aptaive_handle(function () use ($request) {

        if (!class_exists('WooCommerce') || !function_exists('wc_get_order')) {
            return aptaive_response([], 'WooCommerce not available', 400);
        }

        /**
         * =========================
         * Params
         * =========================
         */
        $order_id  = absint($request->get_param('order_id'));
        $order_key = sanitize_text_field($request->get_param('key'));

        if (!$order_id) {
            return aptaive_response([], 'Invalid order id', 400);
        }

        $order = wc_get_order($order_id);

        if (!$order) {
            return aptaive_response([], 'Order not found', 404);
        }

        /**
         * =========================
         * Auth (JWT optional)
         * =========================
         */
        $user = null;

        try {
            $user = aptaive_auth_user();
        } catch (Aptaive_Auth_Exception $e) {
            if ($e->code_name === 'TOKEN_EXPIRED') {
                throw $e;
            }
            $user = null; // guest
        }

        /**
         * =========================
         * Permission check
         * =========================
         */
        $order_user_id  = (int) $order->get_user_id(); // 0 = guest
        $viewer_user_id = $user ? (int) $user->ID : 0;

        // Order của user → bắt buộc đúng user
        if ($order_user_id > 0 && $order_user_id !== $viewer_user_id) {
            return aptaive_response([], 'Unauthorized', 401);
        }

        // Guest order → bắt buộc có order_key đúng
        if ($order_user_id === 0) {
            if (!$order_key || $order_key !== $order->get_order_key()) {
                return aptaive_response([], 'Invalid order key', 401);
            }
        }

        /**
         * =========================
         * Order basic info
         * =========================
         */
        $order_data = [
            'id'            => $order->get_id(),
            'status'        => $order->get_status(),
            'statusLabel'   => wc_get_order_status_name($order->get_status()),
            'createdAt'     => $order->get_date_created()
                ? $order->get_date_created()->date('c')
                : null,
            'paymentMethod' => $order->get_payment_method(),
            'paymentTitle'  => $order->get_payment_method_title(),
            'currency'      => $order->get_currency(),
            'note'          => $order->get_customer_note(),
            'canPay'        => $order->needs_payment(),
            'canCancel'     => $order->has_status(['pending', 'on-hold']),
        ];

        /**
         * =========================
         * Billing & Shipping
         * =========================
         */
        $billing  = new stdClass();
        $shipping = new stdClass();

        foreach ($order->get_data()['billing'] as $key => $value) {
            if ($value !== '') {
                $billing->$key = $value;
            }
        }

        foreach ($order->get_data()['shipping'] as $key => $value) {
            if ($value !== '') {
                $shipping->$key = $value;
            }
        }

        /**
         * =========================
         * Items
         * =========================
         */
        $items = [];

        foreach ($order->get_items() as $item) {

            if (!$item instanceof WC_Order_Item_Product) {
                continue;
            }

            $product   = $item->get_product();
            $image     = null;
            $variationText = null;

            // Image
            if ($product && $product->get_image_id()) {
                $image = wp_get_attachment_image_url(
                    $product->get_image_id(),
                    'medium'
                );
            }

            // Variation text
            if ($item->get_variation_id()) {
                $variation = [];

                foreach ($item->get_formatted_meta_data() as $meta) {
                    if (
                        isset($meta->key, $meta->value) &&
                        strpos($meta->key, 'pa_') === 0
                    ) {
                        $variation[] = wc_attribute_label($meta->key) . ': ' . $meta->value;
                    }
                }

                if (!empty($variation)) {
                    $variationText = implode(', ', $variation);
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

        /**
         * =========================
         * Totals (Woo style)
         * =========================
         */
        $totals = [];

        foreach ($order->get_order_item_totals() as $key => $total) {
            $totals[] = [
                'key'   => $key,
                'label' => $total['label'],
                'value' => html_entity_decode(
                    wp_strip_all_tags($total['value']),
                    ENT_QUOTES,
                    'UTF-8'
                ),
            ];
        }

        /**
         * =========================
         * Payment instructions (BACS)
         * =========================
         */
        $payment_instructions = null;

        if ($order->get_payment_method() === 'bacs') {

            $accounts = get_option('woocommerce_bacs_accounts', []);
            $formatted_accounts = [];

            if (is_array($accounts)) {
                foreach ($accounts as $account) {
                    $formatted_accounts[] = [
                        'accountName'   => $account['account_name'] ?? '',
                        'bankName'      => $account['bank_name'] ?? '',
                        'accountNumber' => $account['account_number'] ?? '',
                        'iban'          => $account['iban'] ?? '',
                        'bic'           => $account['bic'] ?? '',
                    ];
                }
            }

            $payment_instructions = [
                'type'        => 'bacs',
                'title'       => __('Thông tin chuyển khoản ngân hàng', 'woocommerce'),
                'description' => wpautop(
                    wptexturize(get_option('woocommerce_bacs_description'))
                ),
                'accounts'    => $formatted_accounts,
            ];
        }

        /**
         * =========================
         * Final response
         * =========================
         */
        return aptaive_response([
            'order'               => $order_data,
            'billing'             => $billing,
            'shipping'            => $shipping,
            'items'               => $items,
            'totals'              => $totals,
            'paymentInstructions' => $payment_instructions,
        ]);
    });
}
