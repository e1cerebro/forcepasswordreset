<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Chris_Force_Password_Reset
 * @subpackage Chris_Force_Password_Reset/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
        
        <form method="post" name="cleanup_options" action="options.php">

            <?php 
                //Grab all options
                $options = get_option($this->plugin_name);
                
                // Cleanup
                $administrator = $options['administrator'];
                $editor = $options['editor'];
                $author = $options['author'];
                $contributor = $options['contributor'];
                $subscriber = $options['subscriber'];
                $number_of_days = $options['number_of_days'];

                settings_fields($this->plugin_name); 
                            
            ?>
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <fieldset>
                <h3>Number of Days Before Reset</h3>
                <legend class="screen-reader-text"><span><?php _e('Number of Days Before Reset',$this->plugin_name); ?></span></legend>
                <input type="number" min="1" class="regular-text" id="<?php echo $this->plugin_name; ?>-number_of_days" name="<?php echo $this->plugin_name; ?>[number_of_days]" value="<?php if(!empty($number_of_days)) echo $number_of_days; ?>" />
            </fieldset>

            <h3>User Roles</h3>
            <!-- remove some meta and generators from the <head> -->
            <fieldset>
                <legend class="screen-reader-text"><span>Administrator</span></legend>
                <label for="<?php echo $this->plugin_name; ?>-administrator">
                    <input type="checkbox"  id="<?php echo $this->plugin_name; ?>-administrator" name="<?php echo $this->plugin_name; ?>[administrator]" value="1" <?php checked($administrator, 1); ?> />
                    <span><?php esc_attr_e('Administrator', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <!-- remove injected CSS from comments widgets -->
            <fieldset>
                <legend class="screen-reader-text"><span>Editor</span></legend>
                <label for="<?php echo $this->plugin_name; ?>-editor">
                    <input type="checkbox" id="<?php echo $this->plugin_name; ?>-editor" name="<?php echo $this->plugin_name; ?>[editor]" value="1" <?php checked($editor, 1); ?>/>
                    <span><?php esc_attr_e('Editor', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <!-- remove injected CSS from gallery -->
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Author', $this->plugin_name); ?></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-author">
                    <input type="checkbox" id="<?php echo $this->plugin_name; ?>-author" name="<?php echo $this->plugin_name; ?>[author]" value="1" <?php checked($author, 1); ?> />
                    <span><?php esc_attr_e('Author', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <!-- add post,page or product slug class to body class -->
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Contributor', $this->plugin_name); ?></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-contributor">
                    <input type="checkbox" id="<?php echo $this->plugin_name;?>-contributor" name="<?php echo $this->plugin_name; ?>[contributor]" value="1" <?php checked($contributor, 1); ?> />
                    <span><?php esc_attr_e('Contributor', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <!-- load jQuery from CDN -->
            <fieldset>
                <legend class="screen-reader-text"><span><?php _e('Subscriber', $this->plugin_name); ?></span></legend>
                <label for="<?php echo $this->plugin_name; ?>-subscriber">
                    <input type="checkbox" id="<?php echo $this->plugin_name; ?>-subscriber" name="<?php echo $this->plugin_name; ?>[subscriber]" value="1" <?php checked($subscriber, 1); ?> />
                    <span><?php esc_attr_e('Subscriber', $this->plugin_name); ?></span>
                </label>
            </fieldset>

            <?php submit_button('Save all changes', 'primary','submit', TRUE); ?>

        </form>



</div>
