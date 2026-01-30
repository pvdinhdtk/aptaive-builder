<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create WooCommerce Order from App
 */
function aptaive_create_order(WP_REST_Request $request)
{
    return aptaive_handle(function () use ($request) {

        if (!class_exists('WooCommerce')) {
            return aptaive_response([], 'WooCommerce not installed', 400);
        }

        /**
         * =========================
         * Auth (optional / required)
         * =========================
         */
        $user = null;

        $enable_guest_checkout = get_option('woocommerce_enable_guest_checkout') === 'yes';

        if ($enable_guest_checkout) {
            try {
                $user = aptaive_auth_user();
            } catch (Aptaive_Auth_Exception $e) {
                if ($e->code_name === 'TOKEN_EXPIRED') throw $e;
            }
        } else {
            $user = aptaive_auth_user();
        }

        // ---------- Input ----------
        $items         = (array) $request->get_param('items');
        $billing       = (array) $request->get_param('billing');
        $shipping      = (array) $request->get_param('shipping');
        $paymentMethod = sanitize_text_field($request->get_param('paymentMethod'));
        $customerNote  = sanitize_textarea_field($request->get_param('customerNote'));

        if (empty($items)) {
            return aptaive_response([], 'Sản phẩm cần đặt hàng là bắt buộc', 400);
        }

        // ---------- 1️⃣ Create order ----------
        $order = wc_create_order();

        // ---------- 2️⃣ Assign customer ----------
        if ($user) {
            $order->set_customer_id($user->ID);
        }

        // ---------- 3️⃣ Add items ----------
        foreach ($items as $item) {
            $productId   = (int) ($item['productId'] ?? 0);
            $variationId = (int) ($item['variationId'] ?? 0);
            $quantity    = max(1, (int) ($item['quantity'] ?? 1));

            if (!$productId) {
                return aptaive_response([], 'Invalid product id', 400);
            }

            $product = $variationId
                ? wc_get_product($variationId)
                : wc_get_product($productId);

            if (!$product) {
                return aptaive_response([], 'Invalid product', 400);
            }

            $order->add_product($product, $quantity);
        }

        // ---------- 4️⃣ Billing / Shipping ----------
        $billingCountry  = $billing['country'] ?? '';
        $shippingCountry = $shipping['country'] ?? '';

        $billingData = aptaive_filter_address_fields(
            $billing,
            'billing',
            $billingCountry
        );

        $shippingData = aptaive_filter_address_fields(
            $shipping,
            'shipping',
            $shippingCountry
        );

        if (!empty($billingData)) {
            $order->set_address($billingData, 'billing');
        }

        if (!empty($shippingData)) {
            $order->set_address($shippingData, 'shipping');
        }

        // ---------- 5️⃣ Payment method ----------
        if ($paymentMethod) {
            $gateways = WC()->payment_gateways()->get_available_payment_gateways();

            if (!isset($gateways[$paymentMethod])) {
                return aptaive_response([], 'Invalid payment method', 400);
            }

            $order->set_payment_method($gateways[$paymentMethod]);
        }

        // ---------- 6️⃣ Customer note ----------
        if ($customerNote) {
            $order->set_customer_note($customerNote);
        }

        // ---------- 7️⃣ Calculate & save ----------
        $order->calculate_totals();
        $order->save();

        return aptaive_response([
            'id' => $order->get_id(),
            'orderKey' => $order->get_order_key(),
        ]);
    });
}

/**
 * Filter billing / shipping fields based on WooCommerce schema
 *
 * @param array  $input   Data from client (short keys)
 * @param string $type    billing | shipping
 * @param string $country Country code (VN, US...)
 * @return array
 */
function aptaive_filter_address_fields(
    array $input,
    string $type = 'billing',
    string $country = ''
): array {
    if (!function_exists('WC')) {
        return [];
    }

    $prefix = $type . '_';

    // Get allowed Woo fields (billing_first_name, billing_address_1, ...)
    $allowedFields = WC()->countries->get_address_fields($country, $prefix);

    $result = [];

    foreach ($allowedFields as $key => $_field) {
        // billing_first_name -> first_name
        $shortKey = str_replace($prefix, '', $key);

        if (isset($input[$shortKey]) && $input[$shortKey] !== '') {
            $result[$shortKey] = sanitize_text_field($input[$shortKey]);
        }
    }

    return $result;
}
