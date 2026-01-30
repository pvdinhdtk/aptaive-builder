<?php

/**
 * =====================================================
 * Base legacy product class stub
 * =====================================================
 */
abstract class WC_Abstract_Legacy_Product {}


/**
 * =====================================================
 * WooCommerce Product stub
 * =====================================================
 */
class WC_Product extends WC_Abstract_Legacy_Product
{
    /** @return int */
    public function get_id() {}

    /** @return string */
    public function get_name() {}

    /** @return string */
    public function get_sku() {}

    /**
     * simple | variable | variation
     * @return string
     */
    public function get_type() {}

    /**
     * @param string $type
     * @return bool
     */
    public function is_type($type) {}

    /** @return string */
    public function get_price() {}

    /** @return string */
    public function get_regular_price() {}

    /** @return string */
    public function get_sale_price() {}

    /** @return bool */
    public function is_on_sale() {}

    /** @return bool */
    public function is_in_stock() {}

    /** @return string */
    public function get_stock_status() {}

    /** @return bool */
    public function managing_stock() {}

    /**
     * @return int|null
     */
    public function get_stock_quantity() {}

    /** @return int */
    public function get_image_id() {}

    /** @return array<int> */
    public function get_gallery_image_ids() {}

    /**
     * @param bool $for_display
     * @return array{
     *   price: array<int, string>,
     *   regular_price: array<int, string>,
     *   sale_price: array<int, string>
     * }
     */
    public function get_variation_prices($for_display = false) {}

    /**
     * Chỉ dùng cho product variable
     * @return array<int, array<string, mixed>>
     */
    public function get_available_variations() {}

    /**
     * @return array<string, mixed>
     */
    public function get_attributes() {}

    /** @return string */
    public function get_short_description() {}

    /** @return string */
    public function get_description() {}

    /**
     * Chỉ có ý nghĩa với product variation
     * @return int
     */
    public function get_parent_id() {}
}


/**
 * =====================================================
 * WooCommerce Order stub
 * =====================================================
 */
class WC_Order
{
    /** @return int */
    public function get_id() {}

    /** @return string */
    public function get_status() {}

    /**
     * @return \DateTimeInterface|null
     */
    public function get_date_created() {}

    /**
     * @return string
     */
    public function get_total() {}

    /**
     * @param WC_Product $product
     * @param int $quantity
     * @param array<string, mixed> $args
     * @return void
     */
    public function add_product($product, $quantity = 1, $args = []) {}

    /**
     * @param array<string, mixed> $address
     * @param string $type
     * @return void
     */
    public function set_address($address, $type = 'billing') {}

    /**
     * @param string $note
     * @return void
     */
    public function set_customer_note($note) {}

    /**
     * @param string|WC_Payment_Gateway $payment_method
     * @return void
     */
    public function set_payment_method($payment_method) {}

    /**
     * @param int $customer_id
     * @return void
     */
    public function set_customer_id($customer_id) {}

    /**
     * @param bool $and_taxes
     * @return void
     */
    public function calculate_totals($and_taxes = true) {}

    /** @return int */
    public function save() {}

    /**
     * @param string|null $type
     * @return array<int, WC_Order_Item_Product>
     */
    public function get_items($type = null) {}

    /**
     * @param WC_Order_Item_Product $item
     * @param bool $inc_tax
     * @return string
     */
    public function get_item_total($item, $inc_tax = false) {}

    /** @return string */
    public function get_order_key() {}
}


/**
 * =====================================================
 * WooCommerce Cart stub
 * =====================================================
 */
class WC_Cart
{
    /** @return bool */
    public function is_empty() {}
}


/**
 * =====================================================
 * WooCommerce Checkout stub
 * =====================================================
 */
class WC_Checkout
{
    /**
     * Woo cho phép truyền fieldset hoặc không
     *
     * @param string|null $fieldset
     * @return array<string, mixed>
     */
    public function get_checkout_fields($fieldset = null) {}
}


/**
 * =====================================================
 * WooCommerce Payment Gateway stubs
 * =====================================================
 */
class WC_Payment_Gateway
{
    /** @var string */
    public $id;

    /** @var string */
    public $title;

    /** @var string */
    public $method_title;

    /** @var string */
    public $method_description;

    /**
     * 'yes' | 'no'
     * @var string
     */
    public $enabled;

    /** @return string */
    public function get_title() {}

    /** @return string */
    public function get_description() {}
}

class WC_Payment_Gateways
{
    /**
     * @return array<string, WC_Payment_Gateway>
     */
    public function get_available_payment_gateways() {}
}


/**
 * =====================================================
 * WooCommerce main class stub
 * =====================================================
 */
class WooCommerce
{
    /** @var WC_Cart|null */
    public $cart;

    /** @var WC_Countries */
    public $countries;

    /**
     * @return WC_Checkout|null
     */
    public function checkout() {}

    /**
     * @return WC_Payment_Gateways
     */
    public function payment_gateways() {}
}

class WC_Countries
{
    /**
     * @return array<string, string>
     */
    public function get_countries() {}

    /**
     * @return array<string, string>
     */
    public function get_states() {}

    /**
     * @param string $type billing|shipping
     * @param string|null $country
     * @return array<string, mixed>
     */
    public function get_address_fields($type = 'billing', $country = null) {}
}

/**
 * =====================================================
 * WooCommerce Order Item Product stub
 * =====================================================
 */
class WC_Order_Item_Product
{
    /** @return int */
    public function get_id() {}

    /** @return string */
    public function get_name() {}

    /** @return int */
    public function get_quantity() {}

    /** @return string */
    public function get_subtotal() {}

    /** @return string */
    public function get_total() {}

    /** @return WC_Product|null */
    public function get_product() {}

    /** @return int */
    public function get_product_id() {}

    /** @return int */
    public function get_variation_id() {}

    /**
     * @return array<int, WC_Meta_Data>
     */
    public function get_meta_data() {}

    /**
     * @param string $hideprefix
     * @return array<int, object>
     */
    public function get_formatted_meta_data($hideprefix = '_') {}

    /**
     * @return array<string, string>
     */
    public function get_variation_attributes() {}
}

class WC_Order_Query
{
    /**
     * @param array<string, mixed> $args
     */
    public function __construct($args = []) {}

    /**
     * @return array<int, WC_Order>
     */
    public function get_orders() {}
}

class WC_Meta_Data
{
    /** @return string */
    public function get_key() {}

    /** @return mixed */
    public function get_value() {}
}

/**
 * =====================================================
 * Global helpers
 * =====================================================
 */

/**
 * @return WooCommerce
 */
function WC()
{
    return new WooCommerce();
}

/**
 * @param int $product_id
 * @return WC_Product|null
 */
function wc_get_product($product_id)
{
    return null;
}

/**
 * @param int    $product_id
 * @param string $taxonomy
 * @param array<string, mixed> $args
 * @return array<int, mixed>
 */
function wc_get_product_terms($product_id, $taxonomy, $args = []) {}

/**
 * @param string $attribute_name
 * @return string
 */
function wc_attribute_label($attribute_name) {}

/**
 * Convert attribute taxonomy name to slug
 *
 * @param string $taxonomy
 * @return string
 */
function wc_attribute_taxonomy_slug($taxonomy) {}

/**
 * Create a new WooCommerce order.
 *
 * @param array<string, mixed> $args
 * @return WC_Order
 */
function wc_create_order($args = []) {}

/**
 * @param string $status
 * @return string
 */
function wc_get_order_status_name($status) {}

/**
 * @param string|float $number
 * @param int $dp
 * @param bool $trim_zeros
 * @return string
 */
function wc_format_decimal($number, $dp = 2, $trim_zeros = false) {}

/**
 * Get orders.
 *
 * @param array<string, mixed> $args
 * @return array<int, WC_Order>|array{
 *   orders: array<int, WC_Order>,
 *   total: int,
 *   max_num_pages?: int
 * }
 */
function wc_get_orders($args = []) {}

/**
 * @param mixed $var
 * @return mixed
 */
function wc_clean($var) {}
