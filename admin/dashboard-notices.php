<?php
/**
 * Notices template
 */
?>
<div class="notice notice-success is-dismissible <?php echo $this->plugin_name; ?>-notice-welcome">
    <p>
        <?php
        printf(
                /* translators: %s: Name of this plugin */
                __('Thank you for installing %1$s!', 'wp-utility-and-performance'), $this->plugin_display_name
        );
        ?>
        <a href="<?php echo $setting_page; ?>"><?php esc_html_e('Click here', 'wp-utility-and-performance'); ?></a> <?php esc_html_e('to configure the plugin.', 'wp-utility-and-performance'); ?>
    </p>
</div>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $(document).on('click', '.<?php echo $this->plugin_name; ?>-notice-welcome button.notice-dismiss', function (event) {
            event.preventDefault();
            $.post(ajaxurl, {
                action: '<?php echo $this->plugin_name . '_dismiss_dashboard_notices'; ?>',
                nonce: '<?php echo wp_create_nonce($this->plugin_name . '-nonce'); ?>'
            });
            $('.<?php echo $this->plugin_name; ?>-notice-welcome').remove();
        });
    });
</script>