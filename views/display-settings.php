<table class="form-table">
    <p><?php echo __('Setting text and style for the floating widget.', 'ninjateam-telegram') ?></p>
    <tbody>
        <tr>
            <th scope="row"><label for="time_symbols"><?php echo __('Time Symbols', 'ninjateam-telegram') ?></label></th>
            <td>
                <input name="time_symbols[hourSymbol]" placeholder="h" type="text" id="time_symbols-hour" value="<?php echo esc_attr($option['time_symbols'][0]) ?>" class="small-text code" style="text-align: center">
                <span>:<span>
                        <input name="time_symbols[minSymbol]" placeholder="m" type="text" id="time_symbols-minutes" value="<?php echo esc_attr($option['time_symbols'][1]) ?>" class="small-text code" style="text-align: center">
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="nta-tele-switch-control"><?php echo __('Show on desktop', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control">
                    <input type="checkbox" id="nta-tele-switch" name="showOnDesktop" <?php checked($option['showOnDesktop'], 'ON') ?>>
                    <label for="nta-tele-switch" class="green"></label>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="nta-tele-switch-control"><?php echo __('Show on mobile', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control">
                    <input type="checkbox" id="nta-tele-switch-mb" name="showOnMobile" <?php checked($option['showOnMobile'], 'ON') ?>>
                    <label for="nta-tele-switch-mb" class="green"></label>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="displayCondition"><?php echo __('Display', 'ninjateam-telegram') ?></label></th>
            <td>
                <select name="displayCondition" id="displayCondition">
                    <option <?php selected($option['displayCondition'], 'excludePages'); ?> value="excludePages"><?php echo __("Show on all pages except", "ninjateam-telegram") ?></option>
                    <option <?php selected($option['displayCondition'], 'includePages'); ?> value="includePages"><?php echo __("Show on these pages...", "ninjateam-telegram") ?></option>
                </select>
                <p class="description"><?php _e("Please select 'Show on all pages except' if you telent to display the widget on WooCommerce pages.", 'ninjateam-telegram') ?></p>
            </td>
        </tr>
        <th scope="row">
            <!-- <label for="widget_show_on_pages">
                <?php // echo __('Select pages', 'ninjateam-telegram') ?>
            </label> -->
        </th>
        <td class="nta-tele-pages-content include-pages <?php echo esc_attr($option['displayCondition'] == 'includePages' ? '' : 'hide-select') ?>">
            <input type="checkbox" id="include-pages-checkall" />
            <label for="include-pages-checkall">All</label>
            <ul id="nta-tele-display-pages-list">
                <?php
                $array_includes = $option['includePages'];
                if (!$array_includes) {
                    $array_includes = array();
                }
                while ($get_pages_query->have_posts()) : $get_pages_query->the_post();
                ?>
                    <li>
                        <input <?php if (in_array(get_the_ID(), $array_includes)) {
                                    echo 'checked="checked"';
                                } ?> name="includePages[]" class="includePages" type="checkbox" value="<?php esc_attr(the_ID()) ?>" id="nta-tele-hide-page-<?php esc_attr(the_ID()) ?>" />
                        <label for="nta-tele-hide-page-<?php esc_attr(the_ID()) ?>"><?php esc_html(the_title()) ?></label>
                    </li>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>
        </td>

        <td class="nta-tele-pages-content exclude-pages <?php echo esc_attr($option['displayCondition'] == 'excludePages' ? '' : 'hide-select') ?>">
            <input type="checkbox" id="exclude-pages-checkall" />
            <label for="exclude-pages-checkall">All</label>
            <ul id="nta-tele-display-pages-list">
                <?php
                $array_excludes = $option['excludePages'];
                if (!$array_excludes) {
                    $array_excludes = array();
                }
                while ($get_pages_query->have_posts()) : $get_pages_query->the_post();
                ?>
                    <li>
                        <input <?php if (in_array(get_the_ID(), $array_excludes)) {
                                    echo 'checked="checked"';
                                } ?> name="excludePages[]" class="excludePages" type="checkbox" value="<?php esc_attr(the_ID()) ?>" id="nta-tele-show-page-<?php esc_attr(the_ID()) ?>" />
                        <label for="nta-tele-show-page-<?php esc_attr(the_ID()) ?>"><?php esc_html(the_title()) ?></label>
                    </li>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>
        </td>
        </tr>
    </tbody>
</table>
<button class="button button-large button-primary tele-save"><?php echo __('Save Changes', 'ninjateam-telegram') ?><span></span></button>