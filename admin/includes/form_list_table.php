<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class MAP_WPCF7_Form_List_Table extends WP_List_Table
{
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$options = MAP_WPCF7_Options::get_instance();

		$setting = $options->get_option();
		$form_ids = $setting[ MAP_WPCF7_Options::form_ids ];

		$columns	 = $this->get_columns();
		$hidden	  = $this->get_hidden_columns();
		$items		= $this->table_items();
		$perPage	 = 10;

		$this->set_pagination_args( array(
			'total_items' => count( $form_ids ),
			'per_page'	=> 10,
		) );

		$this->_column_headers = array($columns, $hidden );
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
			'name' => __( 'Name', 'map-contact-form-7' ),
			'count'=> __( 'Count', 'map-contact-form-7' )
		);

		return $columns;
	}
	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return array();
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function table_items()
	{
		$options = MAP_WPCF7_Options::get_instance();
		$setting = $options->get_option();
		$form_ids = $setting[ MAP_WPCF7_Options::form_ids ];

		$items = array();
		$contact_forms = WPCF7_ContactForm::find();
		foreach ( $contact_forms as $contact_form ) {
			if ( !MAP_WPCF7_ContactForm::in_form_ids( $form_ids, $contact_form ) ) {
				continue;
			}
			$form_id = $contact_form->id();
			$posts = MAP_WPCF7_Post::find( array(
				'meta_key' => MAP_WPCF7_Post::meta_key_form_id,
				'meta_value' => $form_id,
				) );
			$menu_slug = MAP_WPCF7_Menu_Page::menu_slug;
			$link  = "<a class='row-title' href=admin.php?page=$menu_slug&form-id=$form_id>%s</a>";

			$items[] = array(
				'name' => sprintf( $link, urldecode( $contact_form->name() ) ),
				'count' => sprintf( $link, count( $posts ) ),
			);
		}
	   	return $items;
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
