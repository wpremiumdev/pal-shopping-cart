<?php

class Paypal_Shopping_Cart_Order_Action_Now {

    public function __construct() {
        $this->order_id = intval($_GET['psc_order']);
        $this->order_action = sanitize_text_field($_GET['psc_action']);
        $this->order_processing = 'processing';
        $this->order_pending = 'pending';
        $this->order_completed = 'completed';
        $this->order_cancelled = 'cancelled';
        $this->order_failed = 'failed';
        $this->order_on_hold = 'on-hold';
    }

    public function order_change_status($posted = null) {
        if ((isset($this->order_action) && !empty($this->order_action)) && 'view' != $this->order_action) {
            $this->update_post_order_status();
            $this->update_postmeta_order_status_after();
        } else if ((isset($this->order_action) && !empty($this->order_action)) && 'view' == $this->order_action) {
            $this->update_postmeta_order_view($this->order_id);
        }
    }

    public function update_post_order_status() {

        if ((isset($this->order_id) && !empty($this->order_id)) && (isset($this->order_action) && !empty($this->order_action))) {
           // update_post_meta($this->order_id, '_order_action_status', $this->order_action);
            wp_update_post( array('ID'=>$this->order_id,'post_status'=>$this->order_action) );
            $this->insert_order_note_by_action();
        }
    }
    
    public function insert_order_note_by_action(){
    
    $time = current_time('mysql');
    $current_user = wp_get_current_user();
    $result = array(
            'comment_post_ID' => isset($this->order_id) ? $this->order_id : '',
            'comment_author' => isset($current_user->user_login) ? $current_user->user_login : '',
            'comment_author_email' => isset($current_user->user_email) ? $current_user->user_email : '',
            'comment_author_url' => '',
            'comment_author_IP' => '',
            'comment_date' => $time,
            'comment_date_gmt' => $time,
            'comment_content' => 'Order status changed from Pending Payment to '.ucfirst($this->order_action),
            'comment_karma' => 0,
            'comment_approved' => 0,
            'comment_agent' => 'Pal-Shopping-Cart',
            'comment_type' => 'order_note',
            'comment_parent' => 0,
            'user_id' => isset($current_user->ID) ? $current_user->ID : ''
        );
        wp_insert_comment($result);
        return;
    }

    public function update_postmeta_order_status_after() {
        $post_url = admin_url('edit.php?post_type=' . 'psc_order');
        wp_redirect($post_url);
    }

    public function update_postmeta_order_view($id) {
        $post_url = admin_url('post.php?post=' . $id . '&action=edit');
        wp_redirect($post_url);
    }
}