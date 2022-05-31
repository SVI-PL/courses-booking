<?php
if (!class_exists('WP_List_Table')) {
require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class Bookings_Table extends WP_List_Table{
	public function __construct(){
		parent::__construct(array(
		'singular' => 'booking',
		'plural' => 'bookings',
		));
	}
	
	/** * Prepare the items for the table to process 
	* * @return Void 
	*/
	public function prepare_items()
	{
		$columns = $this->get_columns();
		//$hidden = $this->get_hidden_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable
		);
		
		/** Process bulk action */
		$this->process_bulk_action();
		$per_page = $this->get_items_per_page('records_per_page', 10);
		$current_page = $this->get_pagenum();
		$total_items = $this->record_count();
		$data = self::get_records($per_page, $current_page);
		$this->set_pagination_args(
		['total_items' => $total_items, //WE have to calculate the total number of items
		'per_page' => $per_page // WE have to determine how many items to show on a page
		]);
		
		$this->items = $data;
	}
	
	/** * 
	Retrieve records data from the database
	* * @param int $per_page
	* @param int $page_number
	* * @return mixed
	*/
	public static function get_records($per_page = 10, $page_number = 1){
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}courses_bookings  ";
		
		if (isset($_REQUEST['s'])) {
			$sql.= ' where first_name LIKE "%' . $_REQUEST['s'] . '%" OR last_name LIKE "%' . $_REQUEST['s'] . '%" OR phone LIKE "%' . $_REQUEST['s'] . '%"  OR email LIKE "%' . $_REQUEST['s'] . '%" ';
		}
		
		if(empty($_REQUEST['orderby'])){
			$_REQUEST['orderby'] = 'id';
		}
		
		if (!empty($_REQUEST['orderby'])) {
		$sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
		$sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' DESC';
		}
		$sql.= " LIMIT $per_page";
		$sql.= ' OFFSET ' . ($page_number - 1) * $per_page;
		
		$result = $wpdb->get_results($sql, 'ARRAY_A');
		return $result;
	}
	
	/** 
	* Override the parent columns method. Defines the columns to use in your listing table 
	* * @return Array 
	*/
	function get_columns(){
		
		$columns = array(
		'cb' => '<input type="checkbox" />', 
		'id' => __('Order ID', 'almosoft'),
		'added_at' =>__('Created At', 'almosoft'),
		'first_name' =>__('First Name', 'almosoft'),
		'last_name' =>__('Last name', 'almosoft'),
		'email' =>__('Email', 'almosoft'),
		'phone' =>__('Phone', 'almosoft'),
		'deposit' =>__('Deposit', 'almosoft'),
		'course_ids' =>__('Course code', 'almosoft'),
		'payment_status' =>__('Pay Status', 'almosoft'),
		'payment_id' =>__('Payment Id', 'almosoft'),
		'crm_status' =>__('CRM', 'almosoft'),
		'added_at' =>__('Created At', 'almosoft'),
		);
		return $columns;
	}
	
	
	public function get_sortable_columns(){
		$sortable_columns = array(
		'id' => array('id',true),
		'added_at' => array('added_at',true),
		'first_name' => array('first_name',true),
		'last_name' => array('last_name',true),
		'email' => array('email',true)
		
		);
		return $sortable_columns;
	}
	
	/**
        * [REQUIRED] this is a default column renderer
        *
        * @param $item - row (key, value array)
        * @param $column_name - string (key)
        * @return HTML
        */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
	
	/** 
	* Render the bulk edit checkbox 
	* * @param array $item 
	* * @return string 
	*/
	function column_cb($item)
	{
		return sprintf('<input type="checkbox" name="id[]" value="%s" />', $item['id']);
	}
	/** 
	* Render the bulk edit checkbox 
	* * @param array $item 
	* * @return string 
	*/
	function column_first_column_id($item)
	{
		return sprintf('<a href="%s" class="btn btn-primary"/>Edit</a>', $item['id']);
	}
	
	
	/**
        * [OPTIONAL] this is example, how to render column with actions,
        * when you hover row "Edit | Delete" links showed
        *
        * @param $item - row (key, value array)
        * @return HTML
        */
    function column_first_name($item)
    {
        // links going to /admin.php?page=[your_plugin_page][&other_params]
        // notice how we used $_REQUEST['page'], so action will be done on curren page
        // also notice how we use $this->_args['singular'] so in this example it will
        // be something like &person=2
		
		
        $actions = array(
            
            'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'almosoft')),
			'edit' => sprintf('<a href="?page=viewbooking&id=%s">%s</a>', $item['id'], __('View Details', 'almosoft')),
        );

        return sprintf('%s %s',
            $item['first_name'],
            $this->row_actions($actions)
        );
    }
	
	/**
        * [OPTIONAL] Return array of bult actions if has any
        *
        * @return array
        */
    function get_bulk_actions()
    {
        $actions = array(
            'delete' => 'Delete'
        );
        return $actions;
    }
	
	/**
        * [OPTIONAL] This method processes bulk actions
        * it can be outside of class
        * it can not use wp_redirect coz there is output already
        * in this example we are processing delete action
        * message about successful deletion will be shown on page in next part
        */
    function process_bulk_action()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'courses_bookings'; 

        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);

            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

	
	/** 
	* Returns the count of records in the database. 
	* * @return null|string 
	*/
	public static function record_count(){
		global $wpdb;
		$sql = "SELECT count(*) FROM {$wpdb->prefix}courses_bookings";
		return $wpdb->get_var($sql);
	}
}