<?php
namespace NTA_Telegram;

defined('ABSPATH') || exit;
/**
 * I18n Logic
 */
class I18n {
  public static function loadPluginTextdomain() {
    if (function_exists('determine_locale')) {
      $locale = determine_locale();
    } else {
      $locale = is_admin() ? get_user_locale() : get_locale();
    }
    unload_textdomain('ninjateam-telegram');
    load_textdomain('ninjateam-telegram', NTA_TELEGRAM_PLUGIN_DIR . '/languages/' . $locale . '.mo');
    load_plugin_textdomain('ninjateam-telegram', false, NTA_TELEGRAM_PLUGIN_DIR . '/languages/');
  }

  public static function getTranslation(){
    $translation = array(
      'online' => __('Online', 'ninjateam-telegram'),
      'offline' => __('Offline', 'ninjateam-telegram')
    );

    return $translation;
  }
}
