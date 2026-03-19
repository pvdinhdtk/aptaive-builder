<?php

defined('ABSPATH') || exit;

function aptaive_get_checkout_data()
{
    return aptaive_handle(function () {

        if (!class_exists('WooCommerce')) {
            return aptaive_response([], 'WooCommerce not installed', 400);
        }

        if (!function_exists('WC')) {
            return aptaive_response([], 'WooCommerce not initialized', 400);
        }

        /**
         * =========================
         * Auth (WooCommerce rule)
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

        /**
         * =========================
         * Checkout fields (schema)
         * =========================
         */
        $checkout = WC()->checkout();

        if (!$checkout) {
            return aptaive_response([], 'Checkout not available', 400);
        }

        $fields = [
            'billing'  => [],
            'shipping' => [],
            'order'    => [],
        ];

        foreach (['billing', 'shipping', 'order'] as $section) {
            $section_fields = $checkout->get_checkout_fields($section);

            foreach ($section_fields as $key => $field) {
                $clientKey = aptaive_strip_checkout_prefix($key, $section);

                $fields[$section][] = [
                    'key'         => $clientKey,
                    'label'       => $field['label'] ?? '',
                    'type'        => $field['type'] ?? 'text',
                    'required'    => (bool) ($field['required'] ?? false),
                    'class'       => $field['class'] ?? [],
                    'priority'    => $field['priority'] ?? 0,
                    'options' => is_array($field['options'] ?? null)
                        ? $field['options']
                        : null,
                    'placeholder' => $field['placeholder'] ?? '',
                ];
            }
        }

        /**
         * =========================
         * Values (dynamic prefill)
         * =========================
         * ⚠️ KHÔNG hardcode field
         * ⚠️ Map đúng theo key trả về
         */
        $values = [
            'billing'  => [],
            'shipping' => [],
        ];

        if ($user instanceof WP_User) {
            foreach (['billing', 'shipping'] as $section) {
                foreach ($fields[$section] as $field) {
                    $meta_key = $section . '_' . $field['key'];

                    // email là special case
                    if ($meta_key === 'billing_email') {
                        $values[$section][$field['key']] = $user->user_email;
                        continue;
                    }

                    $value = get_user_meta($user->ID, $meta_key, true);

                    if ($value !== '' && $value !== null) {
                        $values[$section][$field['key']] = $value;
                    }
                }
            }
        }

        /**
         * =========================
         * Payment methods
         * =========================
         */
        $gateways = WC()->payment_gateways()->get_available_payment_gateways();

        $payment_methods = [];

        foreach ($gateways as $gateway) {
            if ($gateway->enabled !== 'yes') {
                continue;
            }

            $payment_methods[] = [
                'id'          => $gateway->id,
                'title'       => $gateway->get_title(),
                'description' => wp_strip_all_tags($gateway->get_description()),
            ];
        }

        /**
         * =========================
         * Final response
         * =========================
         */
        return aptaive_response([
            'fields'          => $fields,          // schema
            'values'          => $values,          // dynamic data
            'paymentMethods' => $payment_methods,
            'requireLogin'   => !$enable_guest_checkout,
        ]);
    });
}


/**
 * Strip billing_ / shipping_ prefix for client
 */
function aptaive_strip_checkout_prefix(string $key, string $section): string
{
    if ($section === 'billing' && strpos($key, 'billing_') === 0) {
        return substr($key, 8);
    }

    if ($section === 'shipping' && strpos($key, 'shipping_') === 0) {
        return substr($key, 9);
    }

    return $key;
}
