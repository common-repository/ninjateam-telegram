<?php 
use NTA_Telegram\Helper;
?>
<div id="app-preview">
</div>

<script type="text/template" id="widget-preview">
    <div class="tele__btn_popup <%= settings.isLaunch ? 'tele__active' : '' %>">
        <div class="tele__btn_popup_txt"><%= settings.btnLabel %></div>
        <div class="tele__btn_popup_icon"></div>
    </div>
    <div class="tele__popup_chat_box <%= settings.isLaunch ? 'tele__active tele__pending tele__lauch' : '' %>">
    <div class="tele__popup_heading">
        <div class="tele__popup_title"><%= settings.title %></div>
        <div class="tele__popup_intro"><%= settings.description %></div>
    </div>
    <!-- /.tele__popup_heading -->
    <div class="tele__popup_content tele__popup_content_left">
        <div class="tele__popup_notice"><%= settings.responseText %></div>
        <% if (settings.isShowGDPR) { %>
            <div class="nta-tele-gdpr"><input id="nta-tele-gdpr" type="checkbox" value="accept">
                <label for="nta-tele-gdpr"><%= settings.gdprContent %></label>
            </div>
            <% }  
        %>
        <div class="tele__popup_content_list">
            <% _.each(accounts, function (account) { %>
            <div class="tele__popup_content_item">
                <a class="tele__stt <%= account.status === 'online' ? 'tele__stt_online' : 'tele__stt_offline' %>">
                    <% if (!_.isEmpty(account.avatar)) { %>
                        <div class="tele__popup_avatar">
                            <div class="tele__cs_img_wrap" style="background: url(<%= account.avatar %>) center center no-repeat; background-size: cover;"></div>
                        </div>
                    <% } else { %>
                        <div class="tele__popup_avatar nta-default-avt">
                            <?php echo Helper::print_icon(); ?>
                        </div>
                    <% } %>
                    <div class="tele__popup_txt">
                        <div class="tele__member_name"><%= account.accountName %></div>
                        <!-- /.tele__member_name -->
                        <div class="tele__member_duty"><%= account.title %></div>
                        <!-- /.tele__member_duty -->
                        <% if (account.status !== 'online') { %>
                        <div class="tele__member_status">
                            <%= account.status %>
                        </div>
                        <% } %>
                    </div>
                    <!-- /.tele__popup_txt -->
                </a>
            </div>
            <% }); %>
        </div>
    </div>
</div>
</script>