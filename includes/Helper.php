<?php
namespace NTA_Telegram;

defined('ABSPATH') || exit;

class Helper
{
    protected static $instance = null;
    private static $manifest = [];
    static $timezone;
    static $time_symbols;
    public static function getInstance()
    {
        if (null == self::$instance) {
            self::$instance = new self;
        }
    return self::$instance;
    }

    public function __construct()
    {
    }
    
    public static function printWorkingDays($array_data)
    {
        if ($array_data['isAlwaysAvailable'] === 'ON') {
            return __('Always online','ninjateam-telegram');
        }

        $date_string = "";
        $daysOfWeek = array(
            'sunday' => __('Sunday', "ninjateam-telegram"),
            'monday' => __('Monday', "ninjateam-telegram"),
            'tuesday' => __('Tuesday', "ninjateam-telegram"),
            'wednesday' => __('Wednesday', "ninjateam-telegram"),
            'thursday' => __('Thursday', "ninjateam-telegram"),
            'friday' => __('Friday', "ninjateam-telegram"),
            'saturday' => __('Saturday', "ninjateam-telegram"),
        );

        foreach ($array_data['daysOfWeekWorking'] as $dayKey => $dayVal) {
            if ($dayVal["isWorkingOnDay"] === 'ON') {
                $date_string .= $daysOfWeek[$dayKey] . ', ';
            }
        }

        $date_string = trim($date_string, ', ');
        return $date_string;
    }

    public static function getValueOrDefault($object, $objectKey, $defaultValue = '')
    {
        return (isset($object[$objectKey]) ? $object[$objectKey] : $defaultValue);
    }

    public static function buildTimeSelector($default = '08:00', $interval = '+30 minutes')
    {
        $output = '';

        $current = strtotime('00:00');
        $end = strtotime('23:59');
        
        while ($current <= $end) {
            $time = date('H:i', $current);
            $sel = ($time == $default) ? ' selected' : '';

            $output .= "<option value=\"{$time}\"{$sel}>" . date('H:i', $current) . '</option>';
            $current = strtotime($interval, $current);
        }
        $sel = ($default === '23:59') ? ' selected' : '';
        $output .= "<option value=\"23:59\"{$sel}>" . '23:59' . '</option>';
        return $output;
    }

    public static function sanitize_array($var)
    {
        if (is_array($var)) {
            return array_map('self::sanitize_array', $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }

    public static function wp_timezone_string(){
        $timezone_string = get_option( 'timezone_string' );

        if ( $timezone_string ) {
            return $timezone_string;
        }

        $offset  = (float) get_option( 'gmt_offset' );
        $hours   = (int) $offset;
        $minutes = ( $offset - $hours );

        $sign      = ( $offset < 0 ) ? '-' : '+';
        $abs_hour  = abs( $hours );
        $abs_mins  = abs( $minutes * 60 );
        $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

        return $tz_offset;
    }

    public static function checkGDPR($option){
        if ($option['isShowGDPR'] === 'OFF') return false;
        if (isset($_COOKIE["nta-tele-gdpr"]) && $_COOKIE["nta-tele-gdpr"] == 'accept') return false;
        return true;
    }

    public static function isSaveNewPost($refererUrl){
        $add_new_action = strpos($refererUrl, 'post-new.php');
        if ($add_new_action !== false) return true;
        return false;
    }

    public static function print_icon(){
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="48px"><path d="M15.5,45.4C37,36,51.3,29.9,58.5,26.9c20.5-8.5,24.7-10,27.5-10c0.6,0,2,0.1,2.9,0.9c0.7,0.6,1,1.4,1,2c0.1,0.6,0.2,1.9,0.1,2.9c-1.1,11.6-5.9,39.9-8.3,53c-1,5.5-3.1,7.4-5,7.6c-4.3,0.4-7.5-2.8-11.7-5.5c-6.5-4.2-10.1-6.9-16.4-11c-7.3-4.8-2.6-7.4,1.6-11.7C51.1,53.7,70,36.5,70.4,35c0-0.2,0.1-0.9-0.3-1.3c-0.4-0.4-1.1-0.3-1.5-0.1c-0.7,0.1-11,7-31.1,20.5c-2.9,2-5.6,3-8,3c-2.6-0.1-7.7-1.5-11.5-2.7c-4.6-1.5-8.3-2.3-8-4.8C10.2,48.1,12,46.8,15.5,45.4z" fill="#2aa7e8"/></svg>';
    }

    private static function get_manifest() {
		if ( ! self::$manifest ) {
			$manifest = NTA_TELEGRAM_PLUGIN_DIR . 'assets/dist/mix-manifest.json';

			if (
				$map = file_get_contents( $manifest ) and
				is_array( $map = json_decode( $map, true ) )
			) {
				self::$manifest = $map;
			}
		}

		return self::$manifest;
    }
    
    public static function AssetResolve( $path ) {
		if ( $map = self::get_manifest() ) {

			$path = self::leading_slash_it( $path );

			if ( isset( $map[ $path ] ) ) {
				return NTA_TELEGRAM_PLUGIN_URL . 'assets/dist' . self::leading_slash_it( $map[ $path ] );
			}
		}

		return '';
    }
    
    private static function leading_slash_it( $string ) {
		return '/' . ltrim( $string, '/\\' );
	}
}
