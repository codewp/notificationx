<?php

class NotificationX_WooCommerce_Extension extends NotificationX_Extension {
    /**
     *  Type of notification.
     *
     * @var string
     */
    public $type = 'woocommerce';
    public $template = 'woo_template';
    public $themeName = 'theme';
    /**
     * An array of all notifications
     *
     * @var [type]
     */
    protected $notifications = [];

    public function __construct() {
        parent::__construct();
        $this->notifications = $this->get_notifications( $this->type );

        add_filter( 'nx_notification_link', array( $this, 'notification_link' ), 10, 2 );
    }
    /**
     * Main Screen Hooks
     */
    public function init_hooks(){
        add_filter( 'nx_show_image_options', array( $this, 'image_options' ) );
        add_filter( 'nx_metabox_tabs', array( $this, 'add_fields' ) );
        add_filter( 'nx_display_types_hide_data', array( $this, 'hide_fields' ) );
        add_filter( 'nx_conversion_from', array( $this, 'toggle_fields' ) );
    }
    /**
     * Builder Hooks
     */
    public function init_builder_hooks(){
        add_filter( 'nx_builder_tabs', array( $this, 'add_builder_fields' ) );
        add_filter( 'nx_display_types_hide_data', array( $this, 'hide_builder_fields' ) );
        add_filter( 'nx_builder_tabs', array( $this, 'builder_toggle_fields' ) );
    }

    public function notification_link( $link, $settings ){
        if( $settings->display_type === 'conversions' && $settings->conversion_url === 'none' ) {
            return '';
        }

        return $link;
    }

    /**
     * Image Options
     *
     * @param array $options
     * @return void
     */
    public function image_options( $options ){
        if( class_exists( 'WooCommerce' ) ) {
            $new = array(
                'product_image' => __('Product Image' , 'notificationx')
            );
            return array_merge( $new, $options );
        }
        return $options;
    }
    /**
     * Needed Fields
     */
    private function init_fields(){
        $fields = [];

        if( ! class_exists( 'WooCommerce' ) ) {
            $fields['has_no_woo'] = array(
                'type'     => 'message',
                'message'    => __('You have to install WooCommerce plugin first.' , 'notificationx'),
                'priority' => 0,
            );
        }

        $fields['woo_template'] = array(
            'type'     => 'template',
            'label'    => __('Notification Template' , 'notificationx'),
            'priority' => 90,
            'defaults' => [
                __('{{name}} recently purchased', 'notificationx'), '{{title}}', '{{time}}'
            ],
            'variables' => [
                '{{name}}', '{{title}}', '{{time}}'
            ],
        );

        return $fields;
    }
    /**
     * This function is responsible for adding fields in main screen
     *
     * @param array $options
     * @return void
     */
    public function add_fields( $options ){
        $fields = $this->init_fields();

        foreach ( $fields as $name => $field ) {
            if( $name === 'has_no_woo' ) {
                $options[ 'source_tab' ]['sections']['config']['fields'][ $name ] = $field;
            }
            if( $name === 'woo_template' ) {
                $options[ 'content_tab' ]['sections']['content_config']['fields'][ $name ] = $field;
            }
        }

        return $options;
    }
    /**
     * This function is responsible for adding fields in builder
     *
     * @param array $options
     * @return void
     */
    public function add_builder_fields( $options ){
        $fields = $this->init_fields();
        unset( $fields[ $this->template ] );
        
        foreach ( $fields as $name => $field ) {
            $options[ 'source_tab' ]['sections']['config']['fields'][ $name ] = $field;
        }

        return $options;
    }
    /**
     * This functions is hooked
     * 
     * @hooked nx_public_action
     * @return void
     */
    public function public_actions(){
        if( ! $this->is_created( $this->type ) ) {
            return;
        }
        add_action( 'woocommerce_new_order_item', array( $this, 'save_new_orders' ), 10, 3 );
    }
    /**
     * This functions is hooked
     * 
     * @hooked nx_admin_action
     * @return void
     */
    public function admin_actions(){
        if( ! $this->is_created( $this->type ) ) {
            return;
        }
        add_action( 'woocommerce_order_status_changed', array( $this, 'status_transition' ), 10, 4 );
    }
    /**
     * This function is responsible for hide fields in main screen
     *
     * @param array $options
     * @return void
     */
    public function hide_fields( $options ) {
        $fields = $this->init_fields();
        foreach ( $fields as $name => $field ) {
            foreach( $options as $opt_key => $opt_value ) {
                $options[ $opt_key ][ 'fields' ][] = $name;
            }
        }
        return $options;
    }
    /**
     * This function is reponsible for hide fields on toggle
     * in builder
     *
     * @param array $options
     * @return void
     */
    public function hide_builder_fields( $options ) {
        $fields = $this->init_fields();
        foreach ( $fields as $name => $field ) {
            foreach( $options as $opt_key => $opt_value ) {
                $options[ $opt_key ][ 'fields' ][] = $name;
            }
        }
        return $options;
    }
    /**
     * Some toggleData & hideData manipulation.
     *
     * @param array $options
     * @return void
     */
    public function toggle_fields( $options ) {
        $fields = array_keys( $this->init_fields() );
        $fields = array_merge( [ 'show_notification_image' ], $fields );

        $options['dependency'][ $this->type ]['fields'] = array_merge( $fields, $options['dependency'][ $this->type ]['fields']);
        $options['dependency'][ $this->type ]['sections'] = array_merge( [ 'image' ], $options['dependency'][ $this->type ]['sections']);
        return $options;
    }
    /**
     * This function is responsible for builder fields
     *
     * @param array $options
     * @return void
     */
    public function builder_toggle_fields( $options ) {
        $fields = $this->init_fields();
        unset( $fields[ $this->template ] );
        $old_fields = $options['source_tab']['sections']['config']['fields']['conversion_from']['dependency'][ $this->type ]['fields'];
        $options['source_tab']['sections']['config']['fields']['conversion_from']['dependency'][ $this->type ]['fields'] = array_merge( array_keys( $fields ), $old_fields);
        return $options;
    }
    /**
     * This function is responsible for making the notification ready for first time we make the notification.
     *
     * @param string $type
     * @param array $data
     * @return void
     */
    public function get_notification_ready( $type, $data = array() ){
        if( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        if( $this->type === $type ) {
            if( ! is_null( $orders = $this->get_orders( $data ) ) ) {
                $this->update_notifications( $this->type, $orders );
            }
        }
    }

    public function status_transition( $id, $from, $to, $order ){
        
        $items = $order->get_items();
        $status = [ 'on-hold', 'cancelled', 'refunded', 'failed', 'pending' ];
        $done = [ 'completed', 'processing' ];



        if( in_array( $from, $done ) && in_array( $to, $status ) ) {
            foreach( $items as $item ) {
                $key = $id . '-' . $item->get_id();
                if( ! isset( $this->notifications[ $key ] ) ) continue;
                unset( $this->notifications[ $key ] );
            }
            $this->update_notifications( $this->type, $this->notifications );
        }

        if( in_array( $from, $status ) && in_array( $to, $done ) ) {
            $orders = [];

            foreach( $items as $item ) {
                $key = $id . '-' . $item->get_id();
                if( isset( $this->notifications[ $key ] ) ) continue;
                $single_notification = $this->ordered_product( $item->get_id(), $item, $order );
                if( ! empty( $single_notification ) ) {
                    $this->save( $this->type, $single_notification, $key );
                }
            }
        }

        return;
    }
    /**
     * Get all the orders from database using a date query
     * for limitation.
     *
     * @param array $data
     * @return void
     */
    public function get_orders( $data = array() ) {
        if( empty( $data ) ) return null;
        $orders = [];
        $from = strtotime( date( get_option( 'date_format' ), strtotime( '-' . intval( $data[ '_nx_meta_display_from' ] ) . ' days') ) );
        $wc_orders = wc_get_orders( [
            'status' => 'processing',
            'date_created' => '>' . $from,
        ] );
        foreach( $wc_orders as $order ) {
            $items = $order->get_items();
            foreach( $items as $item ) {
                $orders[ $order->get_id() . '-' . $item->get_id() ] = $this->ordered_product( $item->get_id(), $item, $order );
            }
        }
        return $orders;
    }
    /**
     * It will generate and save a notification
     * when orders are placed.
     *
     * @param int $item_id
     * @param  WC_Order_Item_Product $item
     * @param int $order_id
     * @return void
     */
    public function save_new_orders( $item_id,  $item,  $order_id ){    
        $single_notification = $this->ordered_product( $item_id, $item, $order_id );

        if( ! empty( $single_notification ) ) {
            $key = $order_id . '-' . $item_id;
            $this->save( $this->type, $single_notification, $key );
        }
    }
    /**
     * This function is responsible for making ready the orders data.
     *
     * @param int $item_id
     * @param WC_Order_Item_Product $item
     * @param int $order_id
     * @return void
     */
    public function ordered_product( $item_id, $item, $order_id ) {
        if( $item instanceof WC_Order_Item_Shipping ) {
            return;
        }

        if( is_int( $order_id ) ) {
            $order = new WC_Order( $order_id );
            $status = $order->get_status();
            $done = [ 'completed', 'processing' ];
            if( ! in_array( $status, $done ) ){
                return;
            }
        } else {
            $order = $order_id;
        }

        $date = $order->get_date_created();

        if( ! empty( $product_data = $this->ready_product_data( $item->get_data() ) ) ) {
            $new_order['id']   = is_int( $order_id ) ? $order_id : $order_id->get_id();
            $new_order['product_id'] = $item->get_product_id();
            $new_order['title']      = $product_data['title'];
            $new_order['link']       = $product_data['link'];
        }
        $new_order['timestamp'] = $date->getTimestamp();

        return array_merge( $new_order, $this->buyer( $order ));
    }
    /**
     * This function is responsible for getting 
     * the buyer name from order.
     *
     * @param WC_Order $order
     * @return void
     */
    protected function buyer( WC_Order $order ){
        $user = $order->get_user();
        if( $user ) {
            $main_user = get_userdata( $user->ID );
            return array(
                'name' => $main_user->first_name . ' ' . substr($main_user->last_name, 0, 1),
                'user_id' => $user->ID,
                'email' => $order->user_email,
            );
        }
        return array(
            'name' => $order->get_billing_first_name() . ' ' . substr($order->get_billing_last_name(), 0, 1),
            'email' => $order->get_billing_email(),
        );
    }
    /**
     * It will take an array to make data clean
     *
     * @param array $data
     * @return void
     */
    protected function ready_product_data( $data ){
        if( empty( $data ) ) {
            return;
        }
        return array(
            'title' => $data['name'],
            'link' => get_permalink( $data['product_id'] ),
        );
    }

    public function frontend_html( $data = [], $settings = false, $args = [] ){
        if( class_exists( 'WooCommerce' ) ) {
            return parent::frontend_html( $data, $settings, $args );
        }
    }

}