<?php 
use NTA_Telegram\Helper;
?>
<p><?php echo __('Display Telegram click to chat button on WooCommerce single product pages.', 'ninjateam-telegram') ?></p>
<table class="form-table">
    <tbody>
        <tr>
            <th scope="row"><label for="nta-tele-switch-control"><?php echo __('Enabled', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-switch-control">
                    <input type="checkbox" id="nta-tele-switch" name="isShow" <?php checked($option['isShow'], 'ON') ?>>
                    <label for="nta-tele-switch" class="green"></label>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="position"><?php echo __('Button position', 'ninjateam-telegram') ?></label></th>
            <td>
                <div class="nta-tele-d-row nta-tele-border-box">
                    <div class="nta-tele-radio-select-img">
                        <input type="radio" id="nta-wa_before_atc" name="position" value="before_atc" <?php checked($option['position'], 'before_atc') ?>>
                        <label for="nta-wa_before_atc">
                            <div class="nta-tele-radio-img-wrap">
                                <img src="<?php echo NTA_TELEGRAM_PLUGIN_URL . 'assets/img/woo_settings/before_add_to_cart.png' ?>">
                            </div>    
                            <span><?php echo __('Before Add to Cart button', 'ninjateam-telegram') ?></span>
                        </label>
                    </div>
                    <div class="nta-tele-radio-select-img">
                        <input type="radio" id="nta-wa_after_atc" name="position" value="after_atc" <?php checked($option['position'], 'after_atc') ?>>
                        <label for="nta-wa_after_atc">
                            <div class="nta-tele-radio-img-wrap">
                                <img src="<?php echo NTA_TELEGRAM_PLUGIN_URL . 'assets/img/woo_settings/after_add_to_cart.png' ?>">
                            </div>    
                            <span><?php echo __('After Add to Cart button', 'ninjateam-telegram') ?></span>
                        </label>
                    </div>
                    <div class="nta-tele-radio-select-img">
                        <input type="radio" id="nta-wa_after_short_description" name="position" value="after_short_description" <?php checked($option['position'], 'after_short_description') ?>>
                        <label for="nta-wa_after_short_description">
                            <div class="nta-tele-radio-img-wrap">
                                <img src="<?php echo NTA_TELEGRAM_PLUGIN_URL . 'assets/img/woo_settings/after_short_desc.png' ?>">
                            </div>
                            <span><?php echo __('After short description', 'ninjateam-telegram') ?></span>
                        </label>
                    </div>
                    <div class="nta-tele-radio-select-img">
                        <input type="radio" id="nta-wa_after_long_description" name="position" value="after_long_description" <?php checked($option['position'], 'after_long_description') ?>>
                        <label for="nta-wa_after_long_description">
                            <div class="nta-tele-radio-img-wrap">
                                <img src="<?php echo NTA_TELEGRAM_PLUGIN_URL . 'assets/img/woo_settings/after_long_desc.png' ?>">
                            </div>
                            <span><?php echo __('After long description', 'ninjateam-telegram') ?></span>
                        </label>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="selected_accounts"><?php echo __('Select accounts to display', 'ninjateam-telegram') ?></label></th>
            <td>
                <div id="app-woo"></div>
            </td>
        </tr>
    </tbody>
</table>
<button class="button button-large button-primary tele-save"><?php echo __('Save Changes', 'ninjateam-telegram') ?><span></span></button>

<script type="text/template" id="selectedAccountTemplate">
    <div class="search-account">
        <input id="input-users" class="ui-autocomplete-loading" type="text" autocomplete="off" placeholder="Search account by enter name or title">
    </div>
    <br/>
</script>

<script type="text/template" id="accountItemView">
<div class="nta-list-items">
    <div class="box-content">
        <div class="box-row">
            <div class="account-avatar">
                <% if (!_.isEmpty(account.avatar)) { %>
                    <div class="tele_img_wrap" style="background: url(<%= account.avatar %>) center center no-repeat; background-size: cover;"></div>
                <% } else { %>
                    <?php echo Helper::print_icon(); ?>
                <% } %>
            </div>
            <div class="container-block">
                <h4><%= account.accountName %></h4>
                <p><%= account.title %></p>
                <p>
                    <% _.each(daysOfWeek, function (day) { %>
                        <% if (account.isAlwaysAvailable == 'ON') { %>
                            <span class="active-date"><%= day[1] %></span>
                        <% } else { %>
                            <span class="<%= (account['daysOfWeekWorking'][day[0]]['isWorkingOnDay'] === 'ON') ? 'active-date' : '' %>"><%= day[1] %></span>
                        <% } %>
                    <% }); %>
                </p>
            </div>
        </div>
    </div>
</div>
</script>

<script type="text/template" id="accountListTemplate">
    <label class="nta-list-status">
        <strong>
            <% if (_.isEmpty(activeAccounts)) { %>
            <?php echo __('Please select accounts you want them to display in Telegram Chat Widget', 'ninjateam-telegram') ?>
            <% } else { %>
            <?php echo __('Selected Accounts:', 'ninjateam-telegram') ?>
            <% } %>
        </strong>
    </label>
    <% if (!_.isEmpty(activeAccounts)) { %>
    <div class="nta-list-box-accounts postbox" id="sortable">
        <% _.each(activeAccounts, function (account) { %>
            <div class="nta-list-items" data-index="<%= account.accountId %>" data-position="<%= account.wc_position %>">
                <div class="box-content box-content-woo">
                    <div class="box-row">
                        <div class="account-avatar">
                            <% if (!_.isEmpty(account.avatar)) { %>
                                <div class="tele_img_wrap" style="background: url(<%= account.avatar %>) center center no-repeat; background-size: cover;"></div>
                            <% } else { %>
                                <?php echo Helper::print_icon(); ?>
                            <% } %>
                        </div>
                        <div class="container-block">
                            <a href="<%= account.edit_link %>">
                                <h4><%= account.accountName %></h4>
                            </a>
                            <p><%= account.title %></p>
                            <p>
                            <% _.each(daysOfWeek, function (day) { %>
                                <% if (account.isAlwaysAvailable == 'ON') { %>
                                <span class="active-date"><%= day[1] %></span>
                            <% } else { %>
                                <span class="<%= (account['daysOfWeekWorking'][day[0]]['isWorkingOnDay'] === 'ON') ? 'active-date' : '' %>"><%= day[1] %></span>
                                <% } %>
                            <% }); %>
                            </p>
                            <a data-remove="<%= account.accountId %>" class="btn-remove-account">Remove</a>
                        </div>
                        <div class="icon-block">
                            <img src="<?php echo NTA_TELEGRAM_PLUGIN_URL . 'assets/img/bar-sortable.svg' ?>" width="20px">
                        </div>
                    </div>
                </div>
            </div>
        <% }); %>
    </div>
    <% } %>
</script>
