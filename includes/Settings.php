<?php
namespace NTA_Telegram;
use NTA_Telegram\Helper;
use NTA_Telegram\Fields;
use NTA_Telegram\PostType;

defined('ABSPATH') || exit;
/**
 * Settings Page
 */
class Settings
{
    protected $option;
    protected $option_group = 'nta_telegram_group';
    protected $option_design = 'nta_telegram_design';
    protected $option_button_group = 'nta_telegram_button_group';
    protected $option_woo_button_group = 'nta_tele_woo_button_group';
    protected $option_ga_group = 'nta_tele_ga_group';

    protected $settings;

    private $floatingWidgetSlug = '';
    private $settingSlug = '';

    protected static $instance = null;

    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
            self::$instance->doHooks();
        }
        return self::$instance;
    }

    private function doHooks(){
        add_action('admin_init', [$this, 'register_setting']);
        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action("admin_footer", [$this, 'admin_footer']);

        add_action('wp_ajax_njt_tele_set_account_position', [$this, 'set_account_position']);
        add_action('wp_ajax_njt_tele_load_accounts_ajax', [$this, 'load_accounts_ajax']);
        add_action('wp_ajax_njt_tele_set_account_status', [$this, 'set_account_status']);

        add_action('wp_ajax_njt_tele_save_display_setting', [$this, 'save_display_setting']);
        add_action('wp_ajax_njt_tele_save_design_setting', [$this, 'save_design_setting']);
        add_action('wp_ajax_njt_tele_save_woocommerce_setting', [$this, 'save_woocommerce_setting']);
        add_action('wp_ajax_njt_tele_save_analytics_setting', [$this, 'save_analytics_setting']);


        add_filter('plugin_action_links_' . NTA_TELEGRAM_BASE_NAME, [$this, 'addActionLinks']);
        add_filter('plugin_row_meta', [$this, 'pluginRowMeta'], 10, 2);
    }

    public function __construct()
    {
    }

    public function addActionLinks($links)
    {
        $links = array_merge(array(
            '<a href="' . esc_url(admin_url('/admin.php?page=nta_telegram_floating_widget')) . '">' . __('Settings', 'ninjateam-telegram') . '</a>',
        ), $links);

        return $links;
    }

    public function pluginRowMeta($links, $file){
        if ( strpos( $file, 'telegram.php' ) !== false ) {
            $new_links = array(
              'doc' => '<a href="https://ninjateam.org/wordpress-telegram-chat-tutorial/" target="_blank">'. __("Documentation", "filebird") .'</a>'
            );
            
            $links = array_merge( $links, $new_links );
          }
          
        return $links;
    }

    public function admin_menu()
    {
        $edit_account_link = 'post-new.php?post_type=telegram-accounts';

        add_menu_page('NTA Telegram', 'Telegram', 'manage_options', 'nta_telegram', [$this, 'create_page_setting_widget'], NTA_TELEGRAM_PLUGIN_URL . 'assets/img/telegram-menu.svg', 60);
        add_submenu_page('nta_telegram', __('Add New account', 'ninjateam-telegram'), __('Add New account', 'ninjateam-telegram'), 'manage_options', $edit_account_link);
        $this->floatingWidgetSlug = add_submenu_page('nta_telegram', __('Floating Widget', 'ninjateam-telegram'), __('Floating Widget', 'ninjateam-telegram'), 'manage_options', 'nta_telegram_floating_widget', [$this, 'floating_widget_view']);
        $this->settingSlug = add_submenu_page('nta_telegram', __('Settings', 'ninjateam-telegram'), __('Settings', 'ninjateam-telegram'), 'manage_options', 'nta_telegram_setting', [$this, 'create_page_setting_widget']);
    }

    function admin_footer()
    {
        $screen = get_current_screen();
        if ($screen->base !== $this->floatingWidgetSlug) return;
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/design-preview.php';
    }

    public function admin_enqueue_scripts($hook_suffix)
    {
        if ($hook_suffix === 'edit.php' || $hook_suffix === 'post-new.php' || $hook_suffix === 'post.php') {
            if (get_post_type() !== 'telegram-accounts') return;
        } else {
            if (!in_array($hook_suffix, [$this->settingSlug, $this->floatingWidgetSlug])) {
                return;
            }
        }
        
        wp_register_style('nta-tele-css', NTA_TELEGRAM_PLUGIN_URL . 'assets/css/admin.css', ['wp-color-picker']);
        wp_enqueue_style('nta-tele-css');

        wp_register_style('nta-tippy-css', NTA_TELEGRAM_PLUGIN_URL . 'assets/css/tooltip.css');
        wp_enqueue_style('nta-tippy-css');

        wp_register_style('nta-tele-widget', NTA_TELEGRAM_PLUGIN_URL . 'assets/css/style.css');
        wp_enqueue_style('nta-tele-widget');
        wp_enqueue_style('tele-ui-range', NTA_TELEGRAM_PLUGIN_URL . 'assets/libs/ui-range.css');

        if ( function_exists('wp_timezone_string') ) {
            $timezone = wp_timezone_string();
        } else {
            $timezone = Helper::wp_timezone_string();
        }

        wp_register_script(
            'nta-tele-js', 
            Helper::AssetResolve('js/app.js'),
            [
                'jquery', 
                'wp-color-picker', 
                'backbone', 
                'underscore', 
                'jquery-ui-tabs',
                'jquery-ui-sortable', 
                'jquery-ui-autocomplete'
            ], 
            NTA_TELEGRAM_VERSION,
            true
        );
        wp_localize_script('nta-tele-js', 'njt_tele', [
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('njt-tele-nonce'),
            'settings' => [
                'widget' => [
                    'styles' => Fields::getWidgetStyles()
                ]
            ],
            'timezone' => $timezone,
        ]);
        wp_enqueue_script('nta-tele-js');
        wp_enqueue_script('jquery-validate', NTA_TELEGRAM_PLUGIN_URL . 'assets/libs/jquery.validate.min.js');
    }

    public function page_display_settings_section_callback()
    {
        $option = Fields::getWidgetDisplay();
        $option['time_symbols'] = explode(":", $option['time_symbols']);
        $get_pages_query = new \WP_Query(array("posts_per_page" => -1, "post_type" => "page", "post_status" => "publish"));
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/display-settings.php';
    }

    public function page_design_settings_section_callback()
    {
        $option = Fields::getWidgetStyles();
        $get_pages_query = new \WP_Query(array("posts_per_page" => -1, "post_type" => "page", "post_status" => "publish"));
        $editor_settings = [
            'media_buttons' => false,
            'textarea_rows' => get_option('default_post_edit_rows', 5),
            'quicktags' => false,
            'teeny' => true,
        ];
        $editor_settings_quicktags = [
            'media_buttons' => false,
            'textarea_rows' => get_option('default_post_edit_rows', 5),
            'quicktags' => true,
            'teeny' => true,
        ];
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/design-settings.php';
    }

    public function page_selected_accounts_section_callback()
    {
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/selected-accounts.php';
    }

    public function woocommerce_button_callback()
    {
        $option = Fields::getWoocommerceSetting();
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/woocommerce-button.php';
    }

    public function analytics_callback()
    {
        $option = Fields::getAnalyticsSetting();
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/analytics.php';
    }

    public function create_page_setting_widget()
    {
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/settings.php';
    }

    public function floating_widget_view()
    {
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/floating-widget-settings.php';
    }

    public function register_setting()
    {
        register_setting($this->option_group, 'nta_telegram_setting');
        register_setting($this->option_design, 'nta_telegram_setting');
        register_setting($this->option_woo_button_group, 'nta_tele_woobutton_setting', [$this, 'save_woobutton_setting']);
        register_setting($this->option_ga_group, 'nta_tele_ga_setting', [$this, 'save_ga_setting']);

        add_settings_section('page_selected_accounts_section', '', [$this, 'page_selected_accounts_section_callback'], 'floating-widget-telegram-1');
        add_settings_section('page_design_settings_section', '', [$this, 'page_design_settings_section_callback'], 'floating-widget-telegram-2');
        add_settings_section('page_display_settings_section', '', [$this, 'page_display_settings_section_callback'], 'floating-widget-telegram-3');
        add_settings_section('nta_woocommerce_button', '', [$this, 'woocommerce_button_callback'], 'settings-telegram-1');
        add_settings_section('nta_analytics', '', [$this, 'analytics_callback'], 'settings-telegram-2');
    }

    public function save_woobutton_setting()
    {
        $new_input = [];

        $new_input['nta_woo_button_position'] = sanitize_text_field($_POST['nta_woo_button_position']);
        $new_input['nta_woo_button_status'] = isset($_POST['nta_woo_button_status']) ? 'ON' : 'OFF';
        return $new_input;
    }

    public function save_ga_setting()
    {
        if (isset($_POST['nta_tele_ga_status'])) {
            return '1';
        }
        return '0';
    }

    public function save_display_setting()
    {
        check_ajax_referer('njt-tele-nonce', 'nonce', true);
        $new_input = [];

        $excludePages = Helper::sanitize_array($_POST['excludePages']);
        $includePages = Helper::sanitize_array($_POST['includePages']);

        $new_input = Fields::getWidgetDisplay();
        $new_input['displayCondition'] = sanitize_text_field($_POST['displayCondition']);
        $new_input['excludePages'] = empty($excludePages) ? array() : $excludePages;
        $new_input['includePages'] = empty($includePages) ? array() : $includePages;
        $new_input['showOnDesktop'] = isset($_POST['showOnDesktop']) ? 'ON' : 'OFF';
        $new_input['showOnMobile'] = isset($_POST['showOnMobile']) ? 'ON' : 'OFF';

        $time_symbols = Helper::sanitize_array($_POST['time_symbols']);
        $new_input['time_symbols'] = wp_unslash($time_symbols['hourSymbol']) . ':' . wp_unslash($time_symbols['minSymbol']);

        update_option('nta_tele_widget_display', $new_input);
        wp_send_json_success();
    }

    public function save_design_setting()
    {
        check_ajax_referer('njt-tele-nonce', 'nonce', true);

        $new_input = [];

        $new_input = Fields::getWidgetStyles();
        $new_input['title'] = sanitize_text_field(wp_unslash($_POST['title']));
        $new_input['textColor'] = sanitize_hex_color($_POST['textColor']);
        $new_input['backgroundColor'] = sanitize_hex_color($_POST['backgroundColor']);
        $new_input['description'] = wp_kses_post(wp_unslash($_POST['description']));
        $new_input['responseText'] = sanitize_text_field(wp_unslash($_POST['responseText']));
        $new_input['scrollHeight'] = sanitize_text_field($_POST['scrollHeight']);
        $new_input['isShowScroll'] = isset($_POST['isShowScroll']) ? 'ON' : 'OFF';
        $new_input['isShowResponseText'] = isset($_POST['isShowResponseText']) ? 'ON' : 'OFF';

        $new_input['btnLabel'] = wp_kses_post(wp_unslash($_POST['btnLabel'])); // It can be an html tag
        $new_input['btnPosition'] = sanitize_text_field($_POST['btnPosition']);
        $new_input['btnLabelWidth'] = sanitize_text_field($_POST['btnLabelWidth']);
        $new_input['btnLeftDistance'] = sanitize_text_field($_POST['btnLeftDistance']);
        $new_input['btnRightDistance'] = sanitize_text_field($_POST['btnRightDistance']);
        $new_input['btnBottomDistance'] = sanitize_text_field($_POST['btnBottomDistance']);
        $new_input['isShowBtnLabel'] = isset($_POST['isShowBtnLabel']) ? 'ON' : 'OFF';

        $new_input['isShowGDPR'] = isset($_POST['isShowGDPR']) ? 'ON' : 'OFF';
        $new_input['gdprContent'] = wp_kses_post(wp_unslash($_POST['gdprContent']));

        update_option('nta_tele_widget_styles', $new_input);
        wp_send_json_success();
    }

    public function save_woocommerce_setting(){
        check_ajax_referer('njt-tele-nonce', 'nonce', true);

        $new_input = [];

        $new_input = Fields::getWoocommerceSetting();
        $new_input['position'] = sanitize_text_field($_POST['position']);
        $new_input['isShow'] = isset($_POST['isShow']) ? 'ON' : 'OFF';
        
        update_option('nta_tele_woocommerce', $new_input);
        wp_send_json_success();
    }

    public function save_analytics_setting(){
        check_ajax_referer('njt-tele-nonce', 'nonce', true);

        $new_input = [];

        $new_input = Fields::getAnalyticsSetting();
        $new_input['enabledGoogle'] = isset($_POST['enabledGoogle']) ? 'ON' : 'OFF';
        $new_input['enabledFacebook'] = isset($_POST['enabledFacebook']) ? 'ON' : 'OFF';
        
        update_option('nta_tele_analytics', $new_input);
        wp_send_json_success();
    }

    public function set_account_position()
    {
        check_ajax_referer('njt-tele-nonce', 'nonce', true);

        $positions = Helper::sanitize_array($_POST['positions']);
        $type = sanitize_text_field($_POST['type']);

        foreach ($positions as $index => $id) {
            update_post_meta($id, "nta_tele_{$type}", $index);
        }

        wp_send_json_success();
    }

    public function load_accounts_ajax()
    {
        check_ajax_referer('njt-tele-nonce', 'nonce', true);
        $postType = PostType::getInstance();
        $accountsList = $postType->get_posts();
        $results = array_map(function ($account) {
            $meta = get_post_meta($account->ID, 'nta_tele_account_info', true);
            $avatar = get_the_post_thumbnail_url($account->ID);
            $wg_show = get_post_meta($account->ID, 'nta_tele_widget_show', true);
            $wg_position = get_post_meta($account->ID, 'nta_tele_widget_position', true);
            $wc_show = get_post_meta($account->ID, 'nta_tele_wc_show', true);
            $wc_position = get_post_meta($account->ID, 'nta_tele_wc_position', true);

            return array_merge(array(
                'accountId' => $account->ID,
                'accountName' => $account->post_title,
                'edit_link' => get_edit_post_link($account->ID),
                'avatar' => $avatar !== false ? $avatar : '',
                'widget_show' => $wg_show,
                'widget_position' => $wg_position,
                'wc_show' => $wc_show,
                'wc_position' => $wc_position,
            ),$meta);
        }, $accountsList);
        wp_send_json_success($results);
    }

    public function set_account_status()
    {
        check_ajax_referer('njt-tele-nonce', 'nonce', true);
        $id = sanitize_text_field($_POST['accountId']);
        $type = sanitize_text_field($_POST['type']);
        $status = sanitize_text_field($_POST['status']);
        update_post_meta($id, "nta_tele_{$type}", $status);
        wp_send_json_success();
    }
}