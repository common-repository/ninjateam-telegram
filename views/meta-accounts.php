<?php 
use NTA_Telegram\Helper;
?>
<table class="form-table" id="nta-custom-wc-button-settings">
    <tbody>
        <tr>
            <th scope="row">
                <label for="number">
                    <?php echo __('Username', 'ninjateam-telegram') ?>
                </label>
            </th>
            <td>
                <p>
                    <input type="text" class="widefat" id="number" name="number" value="<?php echo esc_attr($meta['number']) ?>" autocomplete="off">
                </p>
                <p class="description">
                    <?php echo __('Refer to <a href="https://telegram.org/faq#q-what-are-usernames-how-do-i-get-one" target="_blank">https://telegram.org/faq#q-what-are-usernames-how-do-i-get-one</a> for a detailed explanation.', 'ninjateam-telegram') ?>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="title"><?php echo __('Title', 'ninjateam-telegram') ?></label>
            </th>
            <td>
                <input type="text" id="title" name="title" value="<?php echo esc_attr($meta['title']) ?>" class="widefat" autocomplete="off">
            </td>
        </tr>
        <!-- <tr>
            <th scope="row">
                <label for="predefinedText"><?php //echo __('Predefined Text', 'ninjateam-telegram') ?></label>
            </th>
            <td>
                <textarea name="predefinedText" id="predefinedText" rows="3" class="widefat"><?php //echo esc_textarea($meta['predefinedText']) ?></textarea>
                <p class="description">
                    <?php //echo __('Use [njtele_page_title] and [njtele_page_url] shortcodes to output the page\'s title and URL respectively.', 'ninjateam-telegram') ?>
                </p>
            </td>
        </tr> -->
        <tr>
            <th scope="row">
                <label for="isAlwaysAvailable"><?php echo __('Always available online', 'ninjateam-telegram') ?></label>
            </th>
            <td>
                <div class="nta-tele-switch-control">
                    <input type="checkbox" id="nta-tele-switch" name="isAlwaysAvailable" <?php checked($meta['isAlwaysAvailable'], 'ON') ?>>
                    <label for="nta-tele-switch" class="green"></label>
                </div>
            </td>
        </tr>

        <tr class="nta-btncustom-offline <?php echo ($meta['isAlwaysAvailable'] === 'ON' ? 'hidden' : '') ?>">
            <th scope="row">
                <label><?php echo __('Custom Availability', 'ninjateam-telegram') ?></label>
            </th>
            <td>
                <table class="form-table time-available">
                    <tbody>
                        <?php foreach ($daysOfWeek as $dayKey) : ?>
                            <?php foreach ($meta['daysOfWeekWorking'][$dayKey]['workHours'] as $i => $workHour): ?>
                            <tr class="working-<?php echo esc_attr($dayKey) ?>">
                                <td width="150">
                                    <?php if($i === 0): ?>
                                        <input type="checkbox" id="daysOfWeekWorking[<?php echo esc_attr($dayKey) ?>][isWorkingOnDay]" name="daysOfWeekWorking[<?php echo ($dayKey) ?>][isWorkingOnDay]" <?php checked($meta["daysOfWeekWorking"][$dayKey]['isWorkingOnDay' ], 'ON') ?>>
                                        <label for="daysOfWeekWorking[<?php echo ($dayKey) ?>][isWorkingOnDay]"><?php echo __(ucfirst($dayKey), 'ninjateam-telegram') ?> </label>
                                    <?php endif ?>
                                </td>
                                <td width="100">
                                    <select name="daysOfWeekWorking[<?php echo esc_attr($dayKey) ?>][workHours][<?php echo $i ?>][startTime]" class="start-time"><?php echo Helper::buildTimeSelector($workHour['startTime']); ?></select>
                                </td>
                                <td width="100">
                                    <select name="daysOfWeekWorking[<?php echo esc_attr($dayKey) ?>][workHours][<?php echo $i ?>][endTime]" class="end-time"><?php echo Helper::buildTimeSelector($workHour['endTime']); ?></select>
                                </td>
                                <?php if ($i === 0): ?>
                                    <td><a href="javascript:;" class="add-custom-time">Add</a></td>
                                <?php endif;?>
                                <?php if ($i !== 0): ?>
                                    <td><a style="color: #a00" href="javascript:;" class="remove-custom-time">Remove</a></td>
                                <?php endif; ?>
                                <?php if ($dayKey === 'sunday' && $i === 0) : ?>
                                    <td>
                                        <a href="javascript:;" type="button" class="button" id="btn-apply-time"><?php echo __('Apply to All Days', 'ninjateam-telegram') ?></button>
                                    </td>
                                <?php endif ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="nta-btncustom-offline <?php echo ($meta['isAlwaysAvailable'] === 'ON' ? 'hidden' : '') ?>">
            <th scope="row"><label for="willBeBackText"><?php echo __('Description text when offline', 'ninjateam-telegram') ?></label></th>
            <td>
                <input type="text" id="willBeBackText" name="willBeBackText" value="<?php echo esc_attr($meta['willBeBackText']) ?>" class="widefat" autocomplete="off">
                <p class="description"><?php echo __('You can use shortcode [njtele_time_work] to display the exact time this account is back to work on a working day.', 'ninjateam-telegram') ?></p>
                <input type="text" id="dayOffsText" name="dayOffsText" value="<?php echo esc_attr($meta['dayOffsText']) ?>" class="widefat" autocomplete="off">
                <p class="description"><?php echo __('You can use this text to display on days this account does not work.', 'ninjateam-telegram') ?>
                </p>
            </td>
        </tr>
    </tbody>
</table>
