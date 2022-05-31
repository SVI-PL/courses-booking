<div class="book_step book_step_one <?php echo $data->active_tab ?>" tabindex='1'>
							
	<div class='book_step_one_inner'>
		<div class='book_col_select'>
			<select name='courses_code[]' id='courses_code' class='select_required' multiple="multiple">  
<?php 
if(strpos($_SERVER['REQUEST_URI'], 'niwo') !== false){
echo $data->booking->stringconvertto_options($data->booking->niwo_courses);
} else {
echo $data->booking->stringconvertto_options($data->booking->courses);
}
?>
</select>
		</div>
		<div class='book_col_btn'>
			<button id='stepone_action' class='button main_form_btn'>Volgende</button>
		</div>
	</div>
</div>