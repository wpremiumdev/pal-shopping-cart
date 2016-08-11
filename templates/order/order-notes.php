<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $post;
$post_id = isset($post->ID) ? $post->ID : '';
$PSC_Common_Function = new PSC_Common_Function();
$result_note = $PSC_Common_Function->get_all_order_note_by_post_id($post_id);
?>
<span class="psc_display_note"></span>
<?php
if (isset($result_note) && !empty($result_note) && strlen($result_note) > 0) {
    echo $result_note;
}
?>
<hr>
<div class="psc_cuatom_notes">    
    <div class="psc_custom_notes_add">
        <p><strong><?php echo __('Add Note', 'pal-shopping-cart'); ?></strong></p>
        <input type="hidden" name="note_post_id" value="<?php echo $post_id; ?>" id="note_post_id" class="note_post_id">
    </div>
    <div class="psc_custom_notes_textarea">
        <textarea id="psc_notes_create" class="psc_notes_create" name="psc_notes_create"></textarea>
    </div>
    <div class="psc_custom_notes_dropdown_buttons">
        <select id="psc_custom_notes_dropdown" class="psc_custom_notes_dropdown" name="psc_custom_notes_dropdown">
            <option value="private"><?php echo __('Private note', 'pal-shopping-cart') ?></option>
            <option value="customer"><?php echo __('Note to customer', 'pal-shopping-cart') ?></option>
        </select>
        <a href="javascript:;" class="psc_add_note_buttons button"><?php echo __('Add Note', 'pal-shopping-cart') ?></a>
    </div>
</div>
