<?php
if (!defined('ABSPATH')) {
	exit;
}

use NTA_Telegram\Helper;

function njt_tele_block_cgb_block_assets()
{ // phpcs:ignore
	// Register block styles for both frontend + backend.

	// wp_register_style(
	// 	'block-cgb-style-css', // Handle.
	// 	plugins_url('dist/blocks.style.build.css', dirname(__FILE__)), // Block style CSS.
	// 	is_admin() ? array('wp-editor') : null, // Dependency to include the CSS after it.
	// 	null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	// );

	// Register block editor script for backend.
	wp_register_script(
		'block-cgb-block-js', // Handle.
		plugins_url('/dist/blocks.build.js', dirname(__FILE__)), // Block.build.js: We register the block here. Built with Webpack.
		array('wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor'), // Dependencies, defined above.
		null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);

	// Register block editor styles for backend.
	// wp_register_style(
	// 	'block-cgb-block-editor-css', // Handle.
	// 	plugins_url('dist/blocks.editor.build.css', dirname(__FILE__)), // Block editor CSS.
	// 	array('wp-edit-blocks'), // Dependency to include the CSS after it.
	// 	null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	// );

	// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
	wp_localize_script(
		'block-cgb-block-js',
		'njttele', // Array containing dynamic data for a JS Global.
		[
			'pluginDirPath' 	=> plugin_dir_path(__DIR__),
			'pluginDirUrl'  	=> plugin_dir_url(__DIR__),
			'avatarDefaultUrl' 	=> NTA_TELEGRAM_PLUGIN_URL . 'assets/img/telegram_logo.svg',
			'gutenbergPreview'  => NTA_TELEGRAM_PLUGIN_URL . 'assets/img/telegram-button-preview.png',
			'nonce'				=> wp_create_nonce('njt-tele-gutenberg')
			// Add more data here that you want to access from `cgbGlobal` object.
		]
	);

	register_block_type(
		'ninjateam/nta-telegram',
		array(
			'style'         => 'block-cgb-style-css',
			'editor_script' => 'block-cgb-block-js',
			'editor_style'  => 'block-cgb-block-editor-css',
			'render_callback' => 'njt_tele_button_render',
			'attributes' => array(
				'isSelectedAccount' => array(
					'type' => 'string',
					'default' => -1,
				),
				'username' => array(
					'type' => 'string',
					'default' => "",
				),
				'imageID' => array(
					'type' => 'number',
					'default' => 0,
				),
				'imageAlt' => array(
					'type' => 'string',
					'default' => "img",
				),
				'imageUrl' => array(
					'type' => 'string',
					'default' => "",
				),
				'buttonStyle' => array(
					'type' => 'string',
					'default' => "round",
				),
				'buttonColor' => array(
					'type' => 'string',
					'default' => "#2aa7e8",
				),
				'buttonTitle' => array(
					'type' => 'string',
					'default' => "John Doe",
				),
				'buttonInfo' => array(
					'type' => 'string',
					'default' => "Need help? Chat with us",
				),
				'textColor' => array(
					'type' => 'string',
					'default' => "#fff",
				),
				'waUrl' => array(
					'type' => 'string',
					'default' => "",
				),
				'className' => array(
					'type' => 'string',
				),
				'preview' => array(
                    'type' => 'boolean',
                    'default' => false
                )
			),
		)
	);
}

function njt_tele_button_render($attributes)
{
	if ($attributes['isSelectedAccount'] != -1) {
        return do_shortcode("[njtele_button id={$attributes['isSelectedAccount']}]");
    } else {
        $avatarClass = $attributes['imageUrl'] ? "tele__btn_w_img" : "tele__btn_w_icon";
        $btnStyleClass = $attributes['buttonStyle'] == "round" ? "tele__r_button" : "tele__sq_button";
		$btnStyleClass .= empty($attributes['buttonTitle']) ? ' tele__button_text_only' : '';
        $btn_icon_or_image = '';
        if (empty($attributes['imageUrl'])) {
            $btn_icon_or_image = '<div class="tele__btn_icon"><img src="' . NTA_TELEGRAM_PLUGIN_URL . 'assets/img/telegram_logo.svg' . '" alt=' . $attributes['imageAlt'] . '/></div>';
        } else {
            $btn_icon_or_image = '<div class="tele__cs_img"><div class="tele__cs_img_wrap" style="background: url(' . $attributes['imageUrl'] . ') center center no-repeat; background-size: cover;"></div></div>';
        }
        $html = '';
        $html .= '<div style="margin: 30px 0 30px;">';
        $html .= '<a target="_blank" href="https://t.me/' . $attributes['username'] . '" class="tele__button ' . $btnStyleClass . ' tele__stt_online ' . $avatarClass . '" style="background-color: ' . $attributes['buttonColor'] . '; color: ' . $attributes['textColor'] . '">';
        $html .= $btn_icon_or_image;
        $html .= '<div class="tele__btn_txt">';
		if (!empty($attributes['buttonTitle'])) {
			$html .= '<div class="tele__cs_info">';
			$html .= '<div class="tele__cs_name" style="color: ' . $attributes['textColor'] . '">' . $attributes['buttonTitle'] . '</div>';
			$html .= '<div class="tele__cs_status">Online</div></div>';
		}
		$html .= '<div class="tele__btn_title">' . $attributes['buttonInfo'] . '</div></div></a></div>';
        return $html;
    }
}

function njt_tele_me_assets()
{
	wp_enqueue_style('nta-css-tele-popup', NTA_TELEGRAM_PLUGIN_URL . 'assets/css/style.css');
}

// Hook: Block assets.
add_action('init', 'njt_tele_block_cgb_block_assets');
add_action('enqueue_block_assets', 'njt_tele_me_assets');
