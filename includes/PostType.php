<?php
namespace NTA_Telegram;

use NTA_Telegram\Fields;
use NTA_Telegram\Helper;

defined('ABSPATH') || exit;

class PostType
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
        add_action('init', array($this, 'register_post_type')); 
        add_action('save_post_telegram-accounts', [$this, 'save_account'], 10, 3);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);

        add_filter('manage_telegram-accounts_posts_columns', [$this, 'manager_accounts_columns'], 10, 1);
        add_action('manage_telegram-accounts_posts_custom_column', [$this, 'manager_accounts_show_columns'], 10, 2);
        add_filter('enter_title_here', [$this, 'replace_title'], 20, 2);
        add_action('wp_print_scripts', [$this, 'disable_autosave']);

        add_action('wp_ajax_njt_tele_get_account', [$this, 'ajax_get_account']);
    }

    public function register_post_type(){
        $labels = array(
            'name' => __('Telegram Accounts'),
            'singular_name' => __('Telegram Account'),
            'add_new' => __('Add New Account', 'ninjateam-telegram'),
            'add_new_item' => __('Add New Account', 'ninjateam-telegram'),
            'edit_item' => __('Edit Account', 'ninjateam-telegram'),
            'new_item' => __('New Account', 'ninjateam-telegram'),
            'all_items' => __('All Accounts', 'ninjateam-telegram'),
            'view_item' => __('View Accounts', 'ninjateam-telegram'),
            'search_items' => __('Search Account', 'ninjateam-telegram'),
            'featured_image' => __('Avatar', 'ninjateam-telegram'),
            'set_featured_image' => __('Select an image', 'ninjateam-telegram'),
            'remove_featured_image' => __('Remove avatar', 'ninjateam-telegram'),
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Manager Accounts', 'ninjateam-telegram'),
            'public' => false,
            'show_ui' => true,
            'has_archive' => true,
            'show_in_admin_bar' => false,
            'show_in_rest' => true,
            'show_in_menu' => 'nta_telegram',
            'menu_position' => 100,
            'query_var' => 'telegram-accounts',
            'supports' => array(
                'title',
                'thumbnail',
            ),
            'capabilities' => array(
                'edit_post'          => 'manage_options',
                'read_post'          => 'manage_options',
                'delete_post'        => 'manage_options',
                'edit_posts'         => 'manage_options',
                'edit_others_posts'  => 'manage_options',
                'delete_posts'       => 'manage_options',
                'publish_posts'      => 'manage_options',
                'read_private_posts' => 'manage_options'
            ),
        );
        register_post_type('telegram-accounts', $args);
    }

    public function add_meta_boxes(){
        $current_screen = get_current_screen();

        add_meta_box('telegram-account-info', 'Telegram Account Information', [$this, 'meta_form_account'], 'telegram-accounts', 'normal');
        add_meta_box('telegram-button-style', 'Button Style', [$this, 'meta_form_button_style'], 'telegram-accounts', 'normal');
        if ($current_screen->action !== 'add') {
            add_meta_box('telegram-button-shortcode', 'Shortcode for this account', [$this, 'account_shortcode_form'], 'telegram-accounts', 'side');
        }
    }

    public function disable_autosave() {    
        if (get_post_type() === 'telegram-accounts') {
            wp_deregister_script('autosave');
        }
    }

    public function replace_title($title, $post){
        if ($post->post_type == 'telegram-accounts') {
            $my_title = "Account Name";
            return $my_title;
        }

        return $title;
    }

    public function get_posts($argsQuery = array())
    {
        $defaultArgs = array(
            'post_type' => 'telegram-accounts',
            'post_status' => 'publish',
            'numberposts' => -1,
        );

        $args = apply_filters('njt_tele_get_post_type', $defaultArgs);
        $args = wp_parse_args($argsQuery, $args);
        $account_list = get_posts($args);

        return $account_list;
    }

    public function get_active_widget_accounts()
    {
        return $this->get_posts(
            array(
                'meta_key' => 'nta_tele_widget_position',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'nta_tele_widget_show',
                        'value' => 'ON',
                        'compare' => '='
                    )
            ))

        );
    }

    public function get_active_woocommerce_accounts()
    {
        return $this->get_posts(
            array(
                'meta_key' => 'nta_tele_wc_position',
                'orderby' => 'meta_value_num',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => 'nta_tele_wc_show',
                        'value' => 'ON',
                        'compare' => '='
                    )
            ))
        );
    }

    public function ajax_get_account(){
        check_ajax_referer('njt-tele-gutenberg', 'nonce', true);
        $id = sanitize_text_field($_POST['id']);
        $metaInfo = get_post_meta($id, 'nta_tele_account_info', true);
        $metaStyle = get_post_meta($id, 'nta_tele_button_styles', true);
        wp_send_json([
            'imageUrl'   => get_the_post_thumbnail_url($id),
            'buttonTitle'  => $buttonName,
            'metaInfo'    => $metaInfo,
            'metaStyle'   => $metaStyle
        ]);
    }

    public function save_account($post_id, $post, $update)
    {
        if (isset($post->post_status) && 'auto-draft' == $post->post_status) {
            return;
        }
        
        if (!wp_verify_nonce($_POST['form_account_nonce'], 'save_form_account')) {
            return;
        }

        $new_account = array(
            'number'                => sanitize_text_field($_POST['number']),
            'title'                 => sanitize_text_field($_POST['title']),
            // 'predefinedText'        => sanitize_text_field($_POST['predefinedText']),
            'willBeBackText'        => sanitize_text_field($_POST['willBeBackText']),
            'dayOffsText'           => sanitize_text_field($_POST['dayOffsText']),
            'isAlwaysAvailable'     => isset($_POST['isAlwaysAvailable']) ? 'ON' : 'OFF'
        );

        $daysOfWeekWorking = $_POST['daysOfWeekWorking'];

        $new_account['daysOfWeekWorking'] = array_map(function($day){
            return [
                'isWorkingOnDay' => isset($day['isWorkingOnDay']) ? 'ON' : 'OFF',
                'workHours'      => $day['workHours']
            ];
        }, $daysOfWeekWorking);

        update_post_meta($post_id, 'nta_tele_account_info', $new_account);

        update_post_meta($post_id, 'nta_tele_button_styles', array(
            'type' => sanitize_text_field($_POST['btnType']),
            'backgroundColor' => sanitize_text_field($_POST['backgroundColor']),
            'textColor' => sanitize_text_field($_POST['textColor']),
            'label' => sanitize_text_field($_POST['label']),
            'width' => 300,
            'height' => 64,
        ));

        $isSaveNewPost = Helper::isSaveNewPost(sanitize_text_field($_POST['_wp_http_referer']));
        if ( $isSaveNewPost ) {
            update_post_meta($post_id, 'nta_tele_widget_show', 'OFF');
            update_post_meta($post_id, 'nta_tele_widget_position', 0);
            update_post_meta($post_id, 'nta_tele_wc_show', 'OFF');
            update_post_meta($post_id, 'nta_tele_wc_position', 0);
        } 
    }

    public function meta_form_account($post)
    {
        $screen = get_current_screen();
        $daysOfWeek = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        wp_nonce_field('save_form_account', 'form_account_nonce');
        if ( $screen->action == 'add' ) {
            $meta = Fields::getDefaultMetaAccount($daysOfWeek);
        } else {
            $meta = get_post_meta($post->ID, 'nta_tele_account_info', true);
            $meta['nta_tele_widget_show'] = get_post_meta($post->ID, 'nta_tele_widget_show', true);
            $meta['nta_tele_widget_position'] = get_post_meta($post->ID, 'nta_tele_widget_position', true);
            $meta['nta_tele_wc_show'] = get_post_meta($post->ID, 'nta_tele_wc_show', true);
            $meta['nta_tele_wc_position'] = get_post_meta($post->ID, 'nta_tele_wc_position', true);
        }
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/meta-accounts.php';
    }

    public function account_shortcode_form()
    {
    ?>
        <p>Copy the shortcode below and paste it into the editor to display the button.</p>
        <p><input type="text" id="nta-button-shortcode-copy" value="[njtele_button id=&quot;<?php echo esc_attr(get_the_ID()) ?>&quot;]" class="widefat" readonly=""></p>
        <p class="nta-shortcode-copy-status hidden" style="color: green"><strong>Copied!</strong></p>
    <?php
    }

    public function meta_form_button_style($post)
    {
        $buttonStyles = Fields::getButtonStyles($post->ID);
        $buttonStyles['avatar'] = get_the_post_thumbnail_url($post->ID);
        $buttonStyles['title'] = $post->post_title;
        require NTA_TELEGRAM_PLUGIN_DIR . 'views/meta-button-style.php';
    }

    public function manager_accounts_columns($columns)
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Account Name', 'ninjateam-telegram'),
            'avatar' => __('Avatar', 'ninjateam-telegram'),
            'number' => __('Username', 'ninjateam-telegram'),
            'nta_title' => __('Title', 'ninjateam-telegram'),
            'activedays' => __('Active Days', 'ninjateam-telegram'),
            'shortcode' => __('Shortcode', 'ninjateam-telegram'),
        );
        return $columns;
    }

    public function manager_accounts_show_columns($name, $post_id)
    {
        $data_account = get_post_meta($post_id, 'nta_tele_account_info', true);
        if (empty($data_account)) return;
        switch ($name) {
            case 'avatar':
                the_post_thumbnail('thumbnail', array('class' => 'img-size-table'));
                break;
            case 'number':
                echo esc_html($data_account['number']);
                break;
            case 'nta_title':
                echo esc_html($data_account['title']);
                break;
            case 'activedays':
                echo esc_html(Helper::printWorkingDays($data_account));
                break;
            case 'shortcode':
                echo '<input type="text" class="nta-shortcode-table" name="country" value="[njtele_button id=&quot;' . esc_attr($post_id) . '&quot;]" readonly>';
                break;
        }
    }
}
