<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class MAP_WPCF7_Post_List_Table extends WP_List_Table
{
	private $form_id;

	public function __construct( $form_id ) {
		$this->form_id = $form_id;
		parent::__construct(
			array(
				'singular' => 'map_contact_form',
				'plural'   => 'map_contact_forms',
				'ajax'     => false
			)
		);
	}
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$search = empty( $_REQUEST['s'] ) ? false :  esc_sql( $_REQUEST['s'] );
		$this->process_bulk_action();

		$columns  = $this->get_columns();
		$hidden	= $this->get_hidden_columns();
	        $sortable = $this->get_sortable_columns();
		$items	= $this->table_items();

		$perPage     = 100;
        	$currentPage = $this->get_pagenum();
		
		if ( ! empty($search) ) {
			$totalItems = count( $items );
		}else{
			$totalItems = count( $items );
		}

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page' => $perPage
		) );

		$this->_column_headers = array($columns, $hidden ,$sortable);
		$this->items = $items;
	}
	
	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns()
	{
		$columns = array(
			'cb' => '<input type="checkbox" />',
                        'place_name' => __( 'Name', 'map-contact-form-7' ),
                        'post_content'=> __( 'Post Content', 'map-contact-form-7' )
                );

                return $columns;
	}
	/**
	 * Define check box for bulk action (each row)
	 * @param  $item
	 * @return checkbox
	 */
	public function column_cb($item){
		return sprintf(
			 '<input type="checkbox" name="%1$s[]" value="%2$s" />',
			 $this->_args['singular'],
			 $item['post_id']
		);
	}
	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return array('post_id');
	}
	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns() {
		return array('form-date' => array('form-date', true));
	}

	public function get_bulk_actions() {
		return array(
			'delete' => __( 'Delete', 'map-contact-form-7' ),
		);
	}
	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_items()
	{
		$menu_slug = MAP_WPCF7_Menu_Page::menu_slug;
		$form_id = $this->form_id;
		$posts = MAP_WPCF7_Post::find( array(
			'meta_key' => MAP_WPCF7_Post::meta_key_form_id,
			'meta_value' => $form_id,
		) );
		foreach ( $posts as $post ) {
			$post_id = $post->id();
			$post_content = $post->post_content();
			$posted_data = json_decode( $post_content );
			$form_id = get_post_meta( $post_id, MAP_WPCF7_Post::meta_key_form_id, true );
			$contact_forms = WPCF7_ContactForm::find( array(
				'p' => $form_id
			) );
			$contact_form = $contact_forms[ 0 ];
			$tags = $contact_form->scan_form_tags();
			$place_name = '';
			foreach( $posted_data as $name => $values ) {
				$tag = MAP_WPCF7_ContactForm::get_tag( $tags, $name );
				switch ( $tag[ 'type' ] ) {
				case 'place':
				case 'place*':
					$place = explode( ',', $values[ 0 ] );
					$place_name = urldecode( $place[ 1 ] );
					break;
				default:
					break;
				}
			}
			$link  = "<a class='row-title' href=admin.php?page=$menu_slug&post-id=$post_id>%s</a>";

			$data[] = array(
				'place_name' => sprintf( $link, $place_name ),
				'post_content' => sprintf( $link, urldecode( $post_content ) ),
				'post_id' => $post_id,
			);
		}
	   	return $data;
	}
	/**
	 * Define bulk action
     	 *
	 */
	public function process_bulk_action(){
		$action = $this->current_action();

		$post_ids = isset( $_POST['map_contact_form'] ) ? $_POST['map_contact_form'] : array();
		if ( 'delete' === $action ) {
			foreach ($post_ids as $post_id) {
				wp_delete_post( $post_id, true );
			}
		}
	}
	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item		Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name )
	{
		return $item[ $column_name ];

	}
}

