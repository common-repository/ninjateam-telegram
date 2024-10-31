<?php
namespace NTA_Telegram;

use NTA_Telegram\Fields;

defined('ABSPATH') || exit;
class Shortcode
{
    protected static $instance = null;
    protected $accountID;

    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
            self::$instance->doHooks();
        }
        return self::$instance;
    }

    private function doHooks(){
        add_shortcode('njtele_button', [$this, 'button_shortcode']);
    }

    public function button_shortcode($id)
    {
        extract($id);
        $displayOption = Fields::getWidgetDisplay();
        $stylesOption = Fields::getWidgetStyles();
        $analyticsOption = Fields::getAnalyticsSetting();

        $script = array(
            'name' => get_the_title($id),
            'info' => get_post_meta($id, 'nta_tele_account_info', true),
            'styles' => Fields::getButtonStyles($id),
            'avatar' => get_the_post_thumbnail_url($id),
            'options' => [
                'display' => $displayOption,
                'styles' => $stylesOption,
                'analytics' => $analyticsOption
            ],
            'gdprStatus' => Helper::checkGDPR($stylesOption),
            'defaultAvatar' => NTA_TELEGRAM_PLUGIN_URL . 'assets/img/telegram_logo.svg'
        );
        wp_add_inline_script('nta-tele-libs', 'var njt_tele_button_' . $id . '=' . json_encode($script));

        $content = '';  
        
        $content .= '<div class="nta_tele_button" data-id="' . $id . '"></div>';

        return $content;
    }
}
