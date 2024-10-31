<table class="form-table" id="app-design">
    <p><?php echo __('Setting text and style for the floating widget.', 'ninjateam-telegram') ?></p>
    <tbody>
        <tr>
            <th scope="row"><label for="title"><?php echo __('Widget Text', 'ninjateam-telegram') ?></label></th>
            <td><input name="title" placeholder="Start a Conversation" type="text" id="title" value="<?php echo esc_attr($option['title']) ?>" class="regular-text"></td>
        </tr>

        <tr>
            <th scope="row"><label for="isShowBtnLabel"><?php echo __('Show Widget Label', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control" style="margin-top: 5px;">
                    <input type="checkbox" id="isShowBtnLabel" name="isShowBtnLabel" <?php checked($option['isShowBtnLabel'], 'ON') ?>>
                    <label for="isShowBtnLabel" class="green"></label>
                </div>
            </td>
        </tr>

        <tr class="<?php echo ($option['isShowBtnLabel'] === 'ON' ? '' : 'hidden') ?>">
            <th scope="row"><label for="btnLabel"><?php echo __('Widget Label Text', 'ninjateam-telegram') ?></label></th>
            <td><input name="btnLabel" placeholder="Need Help? <strong>Chat with us</strong>" type="text" id="btnLabel" value="<?php echo esc_attr($option['btnLabel']) ?>" class="regular-text"></td>
        </tr>

        <tr class="<?php echo ($option['isShowBtnLabel'] === 'ON' ? '' : 'hidden') ?>">
            <th scope="row"><label for="btnLabelWidth"><?php echo __('Widget Label Width(px)', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="range" style='--min:0; --max:500; --value:<?php echo $option['btnLabelWidth'] ?>; --text-value:"<?php echo $option['btnLabelWidth'] ?>";'>
                    <input id="btnLabelWidth" name="btnLabelWidth" type="range" min="0" max="500" value="<?php echo esc_attr($option['btnLabelWidth']) ?>" oninput="this.parentNode.style.setProperty('--value',this.value); this.parentNode.style.setProperty('--text-value', JSON.stringify(this.value))">
                    <output></output>
                    <div class='range__progress'></div>
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="textColor"><?php echo __('Widget Text Color', 'ninjateam-telegram') ?></label></th>
            <td><input type="text" id="textColor" name="textColor" value="<?php echo esc_attr($option['textColor']) ?>" class="textColor" data-default-color="#fff" /></td>
        </tr>

        <tr>
            <th scope="row"><label for="backgroundColor"><?php echo __('Widget Background Color', 'ninjateam-telegram') ?></label></th>
            <td><input id="backgroundColor" type="text" name="backgroundColor" value="<?php echo esc_attr($option['backgroundColor']) ?>" class="backgroundColor" data-default-color="#2aa7e8" /></td>
        </tr>
        <tr>
            <th scope="row"><label for=""><?php echo __('Widget Position', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="setting align">
                    <div class="button-group button-large" data-setting="align">
                        <button class="button btn-left <?php echo ($option['btnPosition'] == 'left' ? 'active' : '') ?>" value="left" type="button">
                            <?php echo __('Left', 'ninjateam-telegram') ?>
                        </button>
                        <button class="button btn-right <?php echo ($option['btnPosition'] == 'right' ? 'active' : '') ?>" value="right" type="button">
                            <?php echo __('Right', 'ninjateam-telegram') ?>
                        </button>
                    </div>
                    <input name="btnPosition" id="btnPosition" class="hidden" value="<?php echo esc_attr($option['btnPosition']) ?>" />
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for=""><?php echo __('Widget Distance', 'ninjateam-telegram') ?></label></th>
            <td>
                <div id="left-range-slider">
                    <div>Left</div>
                    <div class="range" style='--min:0; --max:500; --value:<?php echo $option['btnLeftDistance'] ?>; --text-value:"<?php echo $option['btnLeftDistance'] ?>";'>
                        <input id="btnLeftDistance" name="btnLeftDistance" type="range" min="0" max="500" value="<?php echo esc_attr($option['btnLeftDistance']) ?>" oninput="this.parentNode.style.setProperty('--value',this.value); this.parentNode.style.setProperty('--text-value', JSON.stringify(this.value))">
                        <output></output>
                        <div class='range__progress'></div>
                    </div>
                </div>
                <div id="right-range-slider">
                    <div>Right</div>
                    <div class="range" style='--min:0; --max:500; --value:<?php echo $option['btnRightDistance'] ?>; --text-value:"<?php echo $option['btnRightDistance'] ?>";'>
                        <input id="btnRightDistance" name="btnRightDistance" type="range" min="0" max="500" value="<?php echo esc_attr($option['btnRightDistance']) ?>" oninput="this.parentNode.style.setProperty('--value',this.value); this.parentNode.style.setProperty('--text-value', JSON.stringify(this.value))">
                        <output></output>
                        <div class='range__progress'></div>
                    </div>
                </div>
                <div>
                    <div>Bottom</div>
                    <div class="range" style='--min:0; --max:500; --value:<?php echo $option['btnBottomDistance'] ?>; --text-value:"<?php echo $option['btnBottomDistance'] ?>";'>
                        <input id="btnBottomDistance" name="btnBottomDistance" type="range" min="0" max="500" value="<?php echo esc_attr($option['btnBottomDistance']) ?>" oninput="this.parentNode.style.setProperty('--value',this.value); this.parentNode.style.setProperty('--text-value', JSON.stringify(this.value))">
                        <output></output>
                        <div class='range__progress'></div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for=""><?php echo __('Widget Scroll Bar', 'ninjateam-telegram') ?><span class="dashicons dashicons-editor-help njt-tele-tooltip"></span></label></th>
            <td>
                <div class="nta-tele-switch-control" style="margin-top: 5px;">
                    <input type="checkbox" id="isShowScroll" name="isShowScroll" <?php checked($option['isShowScroll'], 'ON') ?>>
                    <label for="isShowScroll" class="green"></label>
                </div>
            </td>
        </tr>
        <tr class="<?php echo ($option['isShowScroll'] === 'ON' ? '' : 'hidden') ?>">
            <th scope="row"><label for=""></label></th>
            <td>
                <div class="range" style='--min:300; --max:1000; --value:<?php echo $option['scrollHeight'] ?>; --text-value:"<?php echo $option['scrollHeight'] ?>";'>
                    <input id="scrollHeight" name="scrollHeight" type="range" min="300" max="1000" value="<?php echo esc_attr($option['scrollHeight']) ?>" oninput="this.parentNode.style.setProperty('--value',this.value); this.parentNode.style.setProperty('--text-value', JSON.stringify(this.value))">
                    <output></output>
                    <div class='range__progress'></div>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="responseText"><?php echo __('Response Time Text', 'ninjateam-telegram') ?></label></th>
            <td>
                <?php wp_editor($option['responseText'], 'responseText', $editor_settings); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="wp-description-wrap"><?php echo __('Description', 'ninjateam-telegram') ?></label></th>
            <td>
                <?php wp_editor($option['description'], 'description', $editor_settings_quicktags); ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="gdprContent"><?php echo __('GDPR Notice', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control" style="margin-top: 5px;">
                    <input type="checkbox" id="nta-tele-switch-gdpr" name="isShowGDPR" <?php checked($option['isShowGDPR'], 'ON') ?>>
                    <label for="nta-tele-switch-gdpr" class="green"></label>
                </div>
                <br />
                <div id="nta-gdpr-editor" class="<?php echo ($option['isShowGDPR'] === 'ON' ? '' : 'hidden') ?>">
                    <?php wp_editor($option['gdprContent'], 'gdprContent', $editor_settings_quicktags); ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<button class="button button-large button-primary tele-save"><?php echo __('Save Changes', 'ninjateam-telegram') ?><span></span></button>