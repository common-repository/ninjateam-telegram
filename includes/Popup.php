<?php
namespace NTA_Telegram;

use NTA_Telegram\Fields;
use NTA_Telegram\PostType;

defined('ABSPATH') || exit;
class Popup
{
    protected static $instance = null;

    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
            self::$instance->doHooks();
        }
        return self::$instance;
    }

    public function __construct()
    {
    }

    private function doHooks(){
        add_action('wp_enqueue_scripts', [$this, 'enqueue_global_scripts_styles']);
        add_action('wp_footer', [$this, 'show_widget']);
    }

    public function enqueue_global_scripts_styles(){
        wp_register_style('nta-css-tele-popup', NTA_TELEGRAM_PLUGIN_URL . 'assets/css/style.css');
        wp_enqueue_style('nta-css-tele-popup');
        wp_style_add_data('nta-css-tele-popup', 'rtl', 'replace');

        //This base script for add_inline_script in shortcode
        wp_enqueue_script('nta-tele-libs', Helper::AssetResolve('libs/njt-telegram.js'), ['jquery'], NTA_TELEGRAM_VERSION, true);

        if ( function_exists('wp_timezone_string') ) {
            $timezone = wp_timezone_string();
        } else {
            $timezone = Helper::wp_timezone_string();
        }

        wp_register_script('nta-js-tele-global', NTA_TELEGRAM_PLUGIN_URL . 'assets/js/telegram-button.js', ['jquery'], NTA_TELEGRAM_VERSION, true);
        wp_localize_script('nta-js-tele-global', 'njt_tele_global', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ajax-nonce'),
            'defaultAvatarSVG' => Helper::print_icon(),
            'defaultAvatarUrl' => NTA_TELEGRAM_PLUGIN_URL . 'assets/img/telegram_logo.svg',
            'timezone' => $timezone,
            'i18n' => I18n::getTranslation()
        ]);
        wp_enqueue_script('nta-js-tele-global');
    }

    public function show_widget()
    {
        $displayOption = Fields::getWidgetDisplay();
        $postId = get_the_ID();
        
        if ( $this->notShowInPage($postId, $displayOption) ) return;

        $activeAccounts = $this->get_accounts_active_and_meta();
        if ( count($activeAccounts) < 1 ) return;

        if (    wp_is_mobile() && $displayOption['showOnMobile'] === "OFF"
            || !wp_is_mobile() && $displayOption['showOnDesktop'] === "OFF"
            || ( $displayOption['showOnMobile'] === "OFF" && $displayOption['showOnDesktop'] === "OFF" )
        ) {
            return;
        }

        echo '<div id="tele"></div>';
        $this->enqueue_scripts_styles($activeAccounts, $displayOption);
    }

    public function enqueue_scripts_styles($activeAccounts, $displayOption)
    {
        $stylesOption = Fields::getWidgetStyles();
        $analyticsOption = Fields::getAnalyticsSetting();
        wp_register_script('nta-js-tele-popup', NTA_TELEGRAM_PLUGIN_URL . 'assets/js/telegram-popup.js', ['jquery']);
        wp_localize_script('nta-js-tele-popup', 'njt_tele', [
            'gdprStatus' => Helper::checkGDPR($stylesOption),
            'accounts' => $activeAccounts,
            'options' => [
                'display' => $displayOption,
                'styles' => $stylesOption,
                'analytics' => $analyticsOption
            ]
        ]);
        wp_enqueue_script('nta-js-tele-popup');
    }

    public function notShowInPage($postId, $option)
    {
        $isPageOrShop = apply_filters('njt_telegram_is_page_or_shop_filter', is_page());
        $postId       = apply_filters('njt_telegram_get_post_id_filter', $postId);

        if ($option['displayCondition'] == 'includePages') {
            if (is_array($option['includePages']) && $isPageOrShop && in_array(strval($postId), $option['includePages'])) {
                return false;
            } 
            return true;
        } else if ($option['displayCondition'] == 'excludePages') {
            if (is_array($option['excludePages']) && $isPageOrShop && in_array(strval($postId), $option['excludePages'])) {
                return true;
            } 
        }

        return false;
    }

    public function get_accounts_active_and_meta(){
        $postType = PostType::getInstance();
        $results = array_map(function ($account){
            $meta = get_post_meta($account->ID, 'nta_tele_account_info', true);
            $avatar = get_the_post_thumbnail_url($account->ID);
            return array_merge(array(
                'accountId' => $account->ID,
                'accountName' => $account->post_title,
                'avatar' => $avatar !== false ? $avatar : '',
            ), $meta);
        }, $postType->get_active_widget_accounts());
        return $results;
    }

    // public function show_popup_view()
    // {
    //     //prevent Oxygen builder
    //     if (isset($_GET['ct_builder']) && !isset($_GET['oxygen_iframe'])) {
    //         return;
    //     }
}
