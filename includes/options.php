<?php

class MAP_WPCF7_Options {
	const option_group = 'map-conatct-form-7';
	const option_name = 'map-conatct-form-7';
	const section_id = 'map-conatct-form-7-section';
	const api_key_field_id = 'map-conatct-form-7-api-key-field';
	const api_key = 'API_KEY';
	const language_field_id = 'map-conatct-form-7-language-field';
	const language = 'language';
	const region_field_id = 'map-conatct-form-7-region-field';
	const region = 'region';
	const form_ids_field_id = 'map-conatct-form-7-form-ids-field';
	const form_ids = 'form_ids';
	const num_ranks_field_id = 'map-conatct-form-7-num_ranks-field';
	const num_ranks = 'num_ranks';

	private static $languages = array( 'ja' );
	private	static $regions = array( 'jp' );

	private static $instance;

	private function __construct() {
	}
	public static function get_instance() {
                if ( empty( self::$instance ) ) {
                        self::$instance = new self;
                }

                return self::$instance;
        }
	public function delete_option() {
		delete_option( self::option_name );
	}
	public function get_option() {
		return get_option(
			self::option_name,
			array( 
				self::form_ids => array(),
				self::num_ranks => 3,
				self::language => 'ja',
				self::region => 'jp',
			) );
	}
	public function register_setting() {
		register_setting(
			self::option_group,
			self::option_name );
		add_settings_section(
                	self::section_id,
                	'',
                	'',
                	self::option_group );

		/* API_KEY */
		add_settings_field(
			self::api_key_field_id,
			'API KEY',
			array( $this, 'output_api_key_field' ),
			self::option_group,
			self::section_id );
		add_settings_field(
			self::language_field_id,
			'language',
			array( $this, 'output_language_field' ),
			self::option_group,
			self::section_id );
		add_settings_field(
			self::region_field_id,
			'region',
			array( $this, 'output_region_field' ),
			self::option_group,
			self::section_id );

		/* POSTS */
		add_settings_field(
			self::form_ids_field_id,
			'Posts',
			array( $this, 'output_form_ids_field' ),
			self::option_group,
			self::section_id );
		/* NUM RANKS */
		add_settings_field(
			self::num_ranks_field_id,
			'NumRanks',
			array( $this, 'output_num_ranks_field' ),
			self::option_group,
			self::section_id );
	}
	public function output_api_key_field() {
		$settings = $this->get_option();
		?>
		<input type="text" id="<?php echo self::api_key_field_id; ?>" name="<?php echo self::option_name; ?>[<?php echo self::api_key; ?>]" value="<?php esc_attr_e( $settings[ self::api_key ] ) ?>" />
		<?php
	}
	public function output_language_field() {
		$settings = $this->get_option();
		?>
		<select type="text" id="<?php echo self::language_field_id; ?>" name="<?php echo self::option_name; ?>[<?php echo self::language; ?>]" value="<?php esc_attr_e( $settings[ self::language ] ) ?>" />
		<?php
		foreach ( self::$languages as $language ) {
			?>
			<option value="<?php echo $language; ?>"><?php echo $language; ?></option>
			<?php
		}
		$language = $settings[ self::language ];
		if ( !in_array( $language, self::$languages ) ) {
			?>
			<option value="<?php echo $language; ?>"><?php echo $language; ?></option>
			<?php
		}
		?>
		</select>
		<input type="text" class="map-contact-form-7-add-option" >
		<button id="map-conatct-form-7-form-ids-add">
        		<?php _e( 'add', 'map-contact-form-7' ); ?>
        	</button>
		<?php
		require_once MAP_WPCF7_PLUGIN_DIR . '/includes/add-option.php';
	}
	public function output_region_field() {
		$settings = $this->get_option();
		?>
		<select type="text" id="<?php echo self::region_field_id; ?>" name="<?php echo self::option_name; ?>[<?php echo self::region; ?>]" value="<?php esc_attr_e( $settings[ self::region ] ) ?>" />
		<?php
		foreach ( self::$regions as $region ) {
			?>
			<option value="<?php echo $region; ?>"><?php echo $region; ?></option>
			<?php
		}
		$region = $settings[ self::region ];
		if ( !in_array( $region, self::$regions ) ) {
			?>
			<option value="<?php echo $region; ?>"><?php echo $region; ?></option>
			<?php
		}
		?>
		</select>
		<input type="text" class="map-contact-form-7-add-option" >
		<button id="map-conatct-form-7-form-ids-add">
        		<?php _e( 'add', 'map-contact-form-7' ); ?>
        	</button>
		<?php
		require_once MAP_WPCF7_PLUGIN_DIR . '/includes/add-option.php';
	}
	public function output_form_ids_field() {
		$contact_forms = WPCF7_ContactForm::find();
		$setting = $this->get_option();
		$form_ids = $setting[ self::form_ids ];
		if ( empty( $form_ids ) ) $form_ids = array();

		$forms = array();
		foreach ( $contact_forms as $contact_form ) {
			$tags = $contact_form->scan_form_tags();
			if ( !MAP_WPCF7_ContactForm::has_place( $tags ) ) {
				continue;
			}
			$forms[] = array( 
				'id' => $contact_form->id(),
				'name' => $contact_form->name()
			);
		}
		require_once MAP_WPCF7_PLUGIN_DIR . '/includes/form_ids_field.php';
	}
	public function output_num_ranks_field() {
		$settings = $this->get_option();
		?>
		<input type="number" id="<?php echo self::num_ranks_field_id; ?>" name="<?php echo self::option_name; ?>[<?php echo self::num_ranks; ?>]" value="<?php esc_attr_e( $settings[ self::num_ranks ] ) ?>" />
		<?php
	}
	public function add_options_page() {
		add_options_page(
		 	//ページタイトル
                	'設定',
                	//設定メニューに表示されるメニュータイトル
                	'Mapプラグインの設定',
                	//権限
                	'administrator',
                	//設定ページのURL。options-general.php?page=sample_setup_page
                	'map-contact-form-7-settings',
                	//設定ページのHTMLをはき出す関数の定義
                	array( $this, 'output' )
        	);
	}
	public function output() {
		?>
		<div class="wrap">
			<?php
			require_once(ABSPATH . 'wp-admin/options-head.php');
			?>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::option_group );
				// 入力項目を出力します(設定ページのslugを指定)>。
				do_settings_sections( self::option_group );
				// 送信ボタンを出力します。
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

