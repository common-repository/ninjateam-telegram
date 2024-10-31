<p><?php echo __('Enable Telegram trackers to monitor your Telegram widget, button and other guest activities.', 'ninjateam-telegram') ?></p>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="enabledGoogle"><?php echo __('Google Analytics', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control">
                    <input type="checkbox" id="enabledGoogle" name="enabledGoogle" <?php checked($option['enabledGoogle'], 'ON') ?>>
                    <label for="enabledGoogle" class="green"></label>
                </div>
                <p class="description"><?php echo __('Gain insights of Telegram tracking in Google Analytics > Behavior > Events', 'ninjateam-telegram') ?></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="enabledFacebook"><?php echo __('Facebook Pixel', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control">
                    <input type="checkbox" id="enabledFacebook" name="enabledFacebook" <?php checked($option['enabledFacebook'], 'ON') ?>>
                    <label for="enabledFacebook" class="green"></label>
                </div>
                <p class="description"><?php echo __('Access Facebook for Business and view recorded events in Traffic Analysis Report', 'ninjateam-telegram') ?></p>
            </td>
        </tr>
</tbody>
</table>
<button class="button button-large button-primary tele-save"><?php echo __('Save Changes', 'ninjateam-telegram') ?><span></span></button>