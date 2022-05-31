<h1>booking details</h1>
<div class='booking_info_wrapper'>
	<div class='item_row'>
		<span>ID</span><span><?php echo $data->booking_info->id; ?></span>
	</div>
	<div class='item_row'>
		<span>Name</span><span><?php echo $data->booking_info->first_name.' '.$data->booking_info->first_name; ?></span>
	</div>
	<div class='item_row'>
		<span>Phone</span><span><?php echo $data->booking_info->phone; ?></span>
	</div>
	<div class='item_row'>
		<span>Email</span><span><?php echo $data->booking_info->email; ?></span>
	</div>
	<div class='item_row'>
		<span>Address</span><span><?php echo $data->booking_info->address; ?></span>
	</div>
	<div class='item_row'>
		<span>Postcode</span><span><?php echo $data->booking_info->postcode; ?></span>
	</div>
	<div class='item_row'>
		<span>Residence</span><span><?php echo $data->booking_info->residence; ?></span>
	</div>
	<div class='item_row'>
		<span>Date of Birth</span><span><?php echo $data->booking_info->date_of_birth; ?></span>
	</div>
	<div class='item_row'>
		<span>Payment ID</span><span><?php echo $data->booking_info->payment_id; ?></span>
	</div>
	<div class='item_row'>
		<span>Payment Status</span><span><?php echo $data->booking_info->payment_status; ?></span>
	</div>
	<div class='item_row'>
		<span>Deposit</span><span><?php echo $data->booking_info->deposit; ?></span>
	</div>
	<div class='item_row'>
		<span>Course ids</span><span><?php echo $data->booking_info->course_ids; ?></span>
	</div>
	<div class='item_row'>
		<span>Referral</span><span><?php echo $data->booking_info->referral; ?></span>
	</div>	
</div>	
<style>
	.booking_info_wrapper{
		max-width:800px;
	}
	.booking_info_wrapper .item_row{
		line-height:26px;
		display:flex;
		align-items:center;
		padding:5px;
	}
	
		
		
	.booking_info_wrapper .item_row span:first-child{
		width:30%;
	}
</style>
