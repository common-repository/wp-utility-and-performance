<div class="wpup-page-wrapper wrap">
    <div class="wpup-page-header">
        <div class="wpup-container wpup-flex-box">
            <div class="wpup-logo-wrapper wpup-headline">
                <a href="#">
                    <img src="<?php echo WPUP_PLUGIN_URI . '/assets/images/wordpress-utility-and-performance-plugin-thememantis-logo.png' ?>" class="wpup-logo" alt="<?php echo esc_html('WP Utility & Performance WordPress Plugin', 'wp-utility-and-performance') ?>">
                </a>
            </div>
        </div>
    </div>
    <?php
    if (isset($this->message)) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>
        <?php
    }
    if (isset($this->errorMessage)) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>
        <?php
    }
    ?>

    <div class="wpup-container wpup-settings">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- Content -->
                <div id="post-body-content">
                    <h1 class="screen-reader-text"><?php esc_html_e('WP Utility and Performance', 'wp-utility-and-performance'); ?></h1>
                    <form action="options-general.php?page=<?php echo $this->plugin_name; ?>" method="post">
                        <?php
                        $setting_postbox = array(
                            array(
                                'h3' => esc_html__('Script & Style Settings', 'wp-utility-and-performance'),
                                'inside' => array(
                                    array(
                                        'id' => 'wpup_css_files_version',
                                        'text' => esc_html__('Hide CSS Files Version', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_js_files_version',
                                        'text' => esc_html__('Hide JS Files Version', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_dashicons',
                                        'text' => esc_html__('Disable Dashicons in Frontend', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_wp_embed',
                                        'text' => esc_html__('Disable Embeds', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_wp_emojis',
                                        'text' => esc_html__('Disable Emojis', 'wp-utility-and-performance'),
                                    ),
                                )
                            ),
                            array(
                                'h3' => esc_html__('WordPress Meta Tag Settings', 'wp-utility-and-performance'),
                                'inside' => array(
                                    array(
                                        'id' => 'wpup_rsd_link',
                                        'text' => esc_html__('Remove RSD Link', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_wp_version_generator_tag',
                                        'text' => esc_html__('Hide WordPress Version', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_xml_rpc',
                                        'text' => esc_html__('Disable XML RPC', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_wlwmanifest_link',
                                        'text' => esc_html__('Remove WLW Manifest Link', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_shortlink',
                                        'text' => esc_html__('Remove Shortlink', 'wp-utility-and-performance'),
                                    ),
                                )
                            ),
                            array(
                                'h3' => esc_html__('WordPress Autoupdate Settings', 'wp-utility-and-performance'),
                                'inside' => array(
                                    array(
                                        'id' => 'wpup_plugin_autoupdate',
                                        'text' => esc_html__('Enable Plugin Autoupdate', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_theme_autoupdate',
                                        'text' => esc_html__('Enable Theme Autoupdate', 'wp-utility-and-performance'),
                                    ),
                                )
                            ),
                            array(
                                'h3' => esc_html__('RSS Feed Settings', 'wp-utility-and-performance'),
                                'inside' => array(
                                    array(
                                        'id' => 'wpup_rss_feed',
                                        'text' => esc_html__('Disable RSS Feed', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_rss_feed_links',
                                        'text' => esc_html__('Remove RSS Feed Links', 'wp-utility-and-performance'),
                                    ),
                                    array(
                                        'id' => 'wpup_feature_image_to_rss_feed',
                                        'text' => esc_html__('Add Feature Image To RSS Feed', 'wp-utility-and-performance'),
                                    ),
                                )
                            ),
                            array(
                                'h3' => esc_html__('WordPress Widget Settings', 'wp-utility-and-performance'),
                                'inside' => array(
                                    array(
                                        'id' => 'wpup_enable_shortcode_widget',
                                        'text' => esc_html__('Enable Shortcode in WP Widgets', 'wp-utility-and-performance'),
                                    ),
                                )
                            ),
                        );
                        foreach ($setting_postbox as $postbox) {
                            ?>
                            <div class="postbox">
                                <h3 class="hndle"><?php echo $postbox['h3']; ?></h3>
                                <?php
                                foreach ($postbox['inside'] as $inside) {
                                    ?>
                                    <div class="inside">
                                        <div class="wpup-row">
                                            <div class="wpup-label"><label for="<?php echo $inside['id'] ?>"><?php echo $inside['text'] ?></label></div>
                                            <div class="wpup-field">
                                                <input class="wpup-switch-checkbox" name="<?php echo $inside['id'] ?>" type="checkbox" id="<?php echo $inside['id'] ?>" value="yes" <?php checked('yes', $this->settings[$inside['id']]); ?> />
                                                <label for="<?php echo $inside['id'] ?>" class="wpup-switch-toggle button-status"></label>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                        }
                        wp_nonce_field($this->plugin_name, $this->plugin_name . '_nonce');
                        ?>
                        <p>
                            <input name="submit" type="submit" name="Submit" class="button button-primary" value="<?php echo esc_attr__('Save WP Utility & Performance Settings', 'wp-utility-and-performance'); ?>" />
                        </p>
                    </form>
                </div>
                <!-- Sidebar -->
                <div id="postbox-container-1" class="postbox-container">
                    <?php require_once( WPUP_PLUGIN_DIR . '/admin/sidebar.php' ); ?>
                </div>
                <!-- /postbox-container -->
            </div>
            <!-- /postbox -->
        </div>
        <!-- /post-body-content -->
    </div>
</div>
