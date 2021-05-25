<?php

class MAP_WPCF7_Menu_Page {
	const menu_slug = 'map-contact-form-7-list.php';

	private static $instance;

        private function __construct() {
        }
        public static function get_instance() {
                if ( empty( self::$instance ) ) {
                        self::$instance = new self;
                }

                return self::$instance;
        }
	public function add_menu_page() {
		add_menu_page(
			__( 'Contact Forms With Place', 'map-contact-form-7' ),
			__( 'Contact Forms With Place', 'map-contact-form-7' ),
			'map_wpcf7_delete',
			self::menu_slug,
			array( $this, 'list_page' ),
			'dashicons-list-view' );
	}
	public function list_page() {
		$form_id  = empty($_GET['form-id']) ? 0 : (int) $_GET['form-id'];
		if ( !empty($form_id) ) {
			$this->list_posts_page( $form_id );
            		return;
        	}
		$post_id  = empty($_GET['post-id']) ? 0 : (int) $_GET['post-id'];
		if( !empty($post_id) ){
			$this->details_page( $post_id );
			return;
		}

        	$list_forms = new MAP_WPCF7_Form_List_Table();
        	$list_forms->prepare_items();
        	?>
            		<div class="wrap">
                		<div id="icon-users" class="icon32"></div>
                		<h2><?php _e( 'Contact Forms List With Places', 'map-contact-form-7' ); ?></h2>
                		<?php $list_forms->display(); ?>
            		</div>
        	<?php
	}
	public function list_posts_page( $form_id ) {
        	$list_posts = new MAP_WPCF7_Post_List_Table( $form_id );
        	$list_posts->prepare_items();
        	?>
            		<div class="wrap">
                		<div id="icon-users" class="icon32"></div>
                		<h2><?php echo get_the_title( $form_id ); ?></h2>
                		<form method="post" action="">
                    			<?php $list_posts->search_box( __( 'Search', 'map-contact-form-7' ), 'search'); ?>
                    			<?php $list_posts->display(); ?>
                		</form>
            		</div>
        	<?php
	}
	public function details_page( $post_id ) {
		$posts = MAP_WPCF7_Post::find( array(
			'p' => $post_id,
		) );
		$post = $posts[ 0 ];
		$ID = $post->ID();
		$post_content = $post->post_content();
		$posted_data = json_decode( $post_content );
		$form_id = get_post_meta( $ID, MAP_WPCF7_Post::meta_key_form_id );
		$contact_forms = WPCF7_ContactForm::find( array(
			'p' => $form_id
		) );
		$contact_form = $contact_forms[ 0 ];
		$tags = $contact_form->scan_form_tags();
		?>
		<div class="wrap">
		<h3><?php echo urldecode( $contact_form->name() ); ?><h3>
		<?php
		foreach( $posted_data as $name => $values ) {
			$tag = MAP_WPCF7_ContactForm::get_tag( $tags, $name );
			switch ( $tag[ 'type' ] ) {
			case 'place':
			case 'place*':
				$place = explode( ',', $values[ 0 ] );
				$value = urldecode( $place[ 1 ] )
					. '(' . urldecode( $values[ 0 ] ) . ')';
				break;
			default:
				if ( is_array( $values ) ) {
					$value = implode( ',', $values );
				} else {
					$value = $values;
				}
				break;
			}
			$value = esc_html( $value );
			?>
			<p>
			<b><?php echo $name; ?></b>: <?php echo $value; ?>
			</p>
			<?php
		}
		?>
		</div>
		<?php
	}
}
