(function ( $ ) {
	$('.progresswrapper').contents().filter(function() { return this.nodeType === 3; }).remove();


	$('#courses_code').select2(
		{
		  placeholder: 'Selecteer een cursus'
		}
	);

	$('#courses_code').on('select2:select', function(e) {
    	$('input.select2-search__field').prop('placeholder', 'Klik voor nog een cursus');
		$('input.select2-search__field').attr('style', 'width: 100%')
	});


	$('#courses_code_head').on('select2:select', function(e) {
    	$('input.select2-search__field').prop('placeholder', 'Klik voor nog een cursus');
		$('input.select2-search__field').attr('style', 'width: 100%')
	});
    
    $('#courses_code_head_niwo').on('select2:select', function(e) {
    	$('input.select2-search__field').prop('placeholder', 'Klik voor nog een cursus');
		$('input.select2-search__field').attr('style', 'width: 100%')
	});
	

	$('#referral').select2({
		minimumResultsForSearch: -1,
		placeholder: ""
	});
	
	$('#courses_code_head').select2({
		minimumResultsForSearch: -1,
		placeholder: "Selecteer een cursus",
	});
    
    $('#courses_code_head_niwo').select2({
		minimumResultsForSearch: -1,
		placeholder: "Selecteer een cursus",
	});
	
	$('#courses_location_head').select2({
		minimumResultsForSearch: -1
	});
	
    $('#courses_location_head_niwo').select2({
		minimumResultsForSearch: -1
	});
    
	 $.fn.inputFilter = function(inputFilter) {
		return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
		  if (inputFilter(this.value)) {
			this.oldValue = this.value;
			this.oldSelectionStart = this.selectionStart;
			this.oldSelectionEnd = this.selectionEnd;
		  } else if (this.hasOwnProperty("oldValue")) {
			this.value = this.oldValue;
			this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
		  } else {
			this.value = "";
		  }
		});
	  };
	
	const booking_course_data = [];
	const booking_course_selected = [];
	const customer_booking_info = {
		first_name:'',
		last_name:'',
		phone:'',
		email:'',
		address:'',
		postcode:'',
		residence:'',
		date_of_birth:'',
		referral:'',
		grand_total:'',
		partial_total:''
	}
	
	$("#stepone_action, #next_step").click(function(e){
		e.preventDefault();
		
		var clickedBtn = $( event.target );
		if (typeof clickedBtn === "undefined") {
			var clickedBtn = $( this );
		}
		var current_booking_step = $('.active_step');
		if(current_booking_step.hasClass('book_step_four')){
				return;
		}
		
		var current_step_index = parseInt($('.active_step').attr('tabindex')) ;
		/** validate data */
		if(current_step_index==1){
			console.log('validate step one');
			if(!validate_select2_dropdown($('#courses_code'))){
				return false;
			}
		}
		
		if(current_step_index==2){
			var error_step2 = '';
			if(!validate_select2_dropdown_by_class($('.location_row'))){
				error_step2='yes';
			}
			
			if(!validate_select2_dropdown_by_class($('.course_date_available'))){
				error_step2='yes';
			}
			
			if(error_step2=='yes'){
				return false;
			}
		}
		
		if(current_step_index==3){

			if(!validate_inputs_data($('.required'))){
				return false;
			}

			
		}
		
		var next_booking_step = $('.active_step').next();
		
		current_booking_step.removeClass('active_step');
		current_booking_step.addClass('hide_booking_step');
		
		next_booking_step.addClass('active_step');
		
		next_booking_step.removeClass('hide_booking_step');
		
		$('.booking_action').css({'display' : 'flex'});
		var step_index = parseInt(next_booking_step.attr('tabindex')) ;

		var completed_steps_filler = 25*step_index;
		$('.completed_steps_fill').css('width',completed_steps_filler+'%');
        
		
		/** create dynamic rows for selected course */
		if(step_index==2){
			
			selectedValues = $('#courses_code').val();
			
			render_step_two_form(selectedValues);
			
		}
		
		if(step_index==3){
			
			var booking_item_body = $(".booking_item_body");
			booking_item_body.html('');
			var booking_grand_total = 0;
			var booking_partial_total = 0;
			$(".date_form_row").each(function(){
				
				var selected_course = $(this).find(".course_code").val();
				var selected_location = $(this).find(".location_row").val();
				var selected_date = $(this).find(".course_date_available").val();
				var selected_row_data = get_course_data_by_id(selected_date);
				
				var booking_item_row = $('<div></div>');
				booking_item_row.addClass('booking_item');
				
				var booking_item_col_one = $('<div></div>');
				booking_item_col_one.addClass('booking_item_col_one');
				booking_item_col_one.html("<span class='label'>Cursus</span><span class='col_info'>"+selected_row_data.course_type+"</span>");
				
				//var booking_item_col_two = $('<div></div>');
				//booking_item_col_two.addClass('booking_item_col_two');
				//booking_item_col_two.html("<span class='label'>Time</span><span class='col_info'>"+selected_row_data.time+"</span>");
				
				var booking_item_col_two = $('<div></div>');
				booking_item_col_two.addClass('booking_item_col_two');
				booking_item_col_two.html("<span class='label'>Locatie</span><span class='col_info'>"+selected_row_data.city+"</span>");
				
				var booking_item_col_three = $('<div></div>');
				booking_item_col_three.addClass('booking_item_col_three');
				booking_item_col_three.html("<span class='label'>Cursus Datum</span><span class='col_info'>"+selected_row_data.date+"</span>");
				
				var booking_item_col_four = $('<div></div>');
				booking_item_col_four.addClass('booking_item_col_four');
				booking_item_col_four.html("<span class='label'>Tarief</span><span class='col_info'>"+object_almosoft.price+' '+object_almosoft.currency+"</span>");
				booking_grand_total = Number(booking_grand_total) + Number(object_almosoft.price);
				booking_partial_total = Number(booking_partial_total) + Number(object_almosoft.part_price);
				
				//var booking_item_col_six = $('<div></div>');
				//booking_item_col_six.addClass('booking_item_col_six');
				//booking_item_col_six.html("<span class='label'>Storting</span><span class='col_info'>"+object_almosoft.part_price+'' +object_almosoft.currency+"</span>" );
				
				booking_item_row.append(booking_item_col_one);
				booking_item_row.append(booking_item_col_two);
				booking_item_row.append(booking_item_col_three);
				booking_item_row.append(booking_item_col_four);
				//booking_item_row.append(booking_item_col_five);
				//booking_item_row.append(booking_item_col_six);
				booking_item_body.append(booking_item_row);
				
			});
			$("#grand_total").attr("data-booking-value",booking_grand_total);
			$("#grand_total").html(booking_grand_total+' '+object_almosoft.currency);
			customer_booking_info.grand_total = booking_grand_total; 
			
			$("#partial_total").attr("data-booking-value",booking_partial_total);
			$("#partial_total").html(booking_partial_total+' '+object_almosoft.currency);
			customer_booking_info.partial_total = booking_partial_total; 
				
		}
		
		if(step_index==4){
			$('html, body').animate({
				scrollTop: $("#booking_wrapper").offset().top
			}, 1000);
			
			customer_booking_info.first_name = $("#first_name").val();
			customer_booking_info.last_name = $("#last_name").val();
			customer_booking_info.phone = $("#phone").val();
			customer_booking_info.email = $("#email").val();
			customer_booking_info.address = $("#address").val();
			customer_booking_info.postcode = $("#postcode").val();
			customer_booking_info.residence = $("#residence").val();
			customer_booking_info.date_of_birth = $("#date_of_birth").val();
			customer_booking_info.referral = $("#referral").val();
			
			/** next button change to call ajax function */
			$("#next_step").hide();
			$("#pay_now").show().css('display','inline-block');

			jQuery("#all_course_price_total").text(customer_booking_info.grand_total);
			jQuery("#all_course_price_total_paypal").text(customer_booking_info.grand_total);
			jQuery("#per_course_price").text(customer_booking_info.partial_total);
			
			
		}	
		
	});
	
	function render_step_two_form(selectedValues, header_redirect=''){
		
		var cousrse_len = selectedValues.length;
		var date_finder_wrapper = $('.date_finder_wrapper');
		date_finder_wrapper.empty();
		if(header_redirect==1){ cousrse_len=1; }
			
		for(i=0;i<cousrse_len; i++){
			var selected_code = (header_redirect==1) ? selectedValues : selectedValues[i];
			var course_row = $("<div></div>"); 
			course_row.addClass('date_form_row');
			
			var course_col_code = $("<div></div>");
			course_col_code.addClass('course_code');
			
			var course_col_code_inner = $('<span></span>').text(selected_code);
			course_col_code.append(course_col_code_inner);
			
			/** location column */
			var location_input = $('<select class="location_row select_required"></select>');
			location_input.attr('name','location_'+selected_code);
			location_input.attr('id','location_'+selected_code);
			location_input.append(object_almosoft.location_options);

			var course_col_location = $("<div class='location_code'></div>");
			course_col_location.append(location_input);
			
			
			/** date column */
			var course_col_date = $("<div></div>");
			course_col_date.addClass('course_date');
			var course_date_input = $('<select></select>');
			course_date_input.addClass('course_date_available select_required');
			course_date_input.attr('name','date_'+selected_code);
			course_date_input.attr('id','date_'+selected_code);
			course_col_date.append('<i class="fa fa-cal" aria-hidden="true"></i>');
			course_col_date.append(course_date_input);
			
			
			/** action column */
			var course_col_action = $("<div></div>");
			course_col_action.addClass('course_action desktoponly');
			course_col_action.html("<span class='remove'>Verwijder <i class='fa fa-trashs' aria-hidden='true'></i></span>");
			
			var course_col_mobile_action = $("<div></div>");
			course_col_mobile_action.addClass('course_action mobileonly');
			course_col_mobile_action.html("<span class='remove'><i class='fa fa-trash' aria-hidden='true'></i></span>");
			
			course_row.append(course_col_code);
			course_row.append(course_col_location);
			course_row.append(course_col_mobile_action);
			course_row.append(course_col_date);
			course_row.append(course_col_action);
			
			date_finder_wrapper.append(course_row);	
			//$("#date_"+selected_code).datepicker();
			$('#location_'+selected_code).select2({
				minimumResultsForSearch: -1
			});
			
			/** date dropdown */
			$('#date_'+selected_code).select2({
				minimumResultsForSearch: -1
			});
			
			$('#location_'+selected_code).val(object_almosoft.location_city);
			
			$('#location_'+selected_code).trigger('change');
			
		}
		
	}
	
	function validate_select2_dropdown(elemObj){
		if(!elemObj.val() || elemObj.val()==null || elemObj.val()==''){
			elemObj.next().addClass('booking_invalid');
			return false;
		}
		return true;
	}
	
	function validate_select2_dropdown_by_class(elemObj){
		var error = '';
		elemObj.each(function(){
			if(!$(this).val()){
				$(this).next().addClass('booking_invalid');
				error = 'yes';
			}else{
				$(this).next().removeClass('booking_invalid');
			}
		});
		
		if(error == 'yes'){
			return false;
		}
		
		return true;
	}
	
	$("#booking_steps").on('change','.select_required', function(){
		
		$(this).next().removeClass('booking_invalid');
		
	});
	
	function validate_inputs_data(elemObj){
		var error='';
		elemObj.each(function(){
			if(!$(this).val()){
				$(this).addClass('booking_invalid');
				error = 'yes';
			}else{
				if($(this).attr('id')=='date_of_birth'){
					
					var dob_input = $(this).val();
					 var isDateOkay = isDatevalid(dob_input);
					 if(!isDateOkay || isDateOkay=='exceed'){
						error = 'yes';
					 }
					 
				}else{
					$(this).removeClass('booking_invalid');
				}
			}
		});

		if (!$('#terms_required input').prop('checked')){
			$('#terms_required').css('border', '1px solid #ff6a00');
			error = 'yes';
		} else {
			$('#terms_required').css('border', 'unset');
		}

		if(!validate_select2_dropdown($('#referral'))){
				return false;
			}
			else {
				$(this).removeClass('booking_invalid');
			}
		
		if(error == 'yes'){
			return false;
		}
		
		return true;
	}
	
	$("#booking_steps").on('change','.required', function(){
		
		$(this).removeClass('booking_invalid');
		
	});
	
	$("#booking_steps").on('change','#email', function(){
		$(this).parent().find('.invalid_input').remove();
		if(!validateEmail()){
			$(this).parent().append('<span class="invalid_input">Email ID is not valid.</span>');
			return false;
		}
		
		$(this).removeClass('booking_invalid');
		
	});
	
	 // $("#postcode").inputFilter(function(value) {
		// return /^\d*$/.test(value);
	 //  });
	
	 $("#phone").inputFilter(function(value) {
		return /^\+?\d*$/.test(value); 
	  });
	  
	  $("#referral").change(function(){
	  	if(!validate_select2_dropdown($('#referral'))){
				return false;
			}
	  	});


	 $("#date_of_birth").change(function(){
		 
		 var dob_input = $(this).val();
		 var isDateOkay = isDatevalid(dob_input);
		 
		 $("#date_format_valid_msg").html('Please enter valid date(dd-mm-yyyy).');
		 if(isDateOkay && isDateOkay !='exceed'){
			 $(this).removeClass('booking_invalid');
			 $("#date_format_valid_msg").hide();
		 }else if(isDateOkay =='exceed'){
			 $("#date_format_valid_msg").show();
			 $("#date_format_valid_msg").html('Birth date must be lower than current date.');
			 $(this).addClass('booking_invalid');
			 return false;
		 }else{
			  $(this).addClass('booking_invalid');
			  $("#date_format_valid_msg").show();
			  return false;
		 }
		 
		 
	 });
	 
	 $("#date_of_birth").keyup(function(event){
		 var track_key_pressed = event.keyCode;
		 if(track_key_pressed==8 || track_key_pressed==46){ return true;}
		 var current_entered_val = $(this).val();
		 
		 if(current_entered_val.length==2 || current_entered_val.length==5){
			 $(this).val(current_entered_val+'-');
		 }
		 
		 var month_first_digit = $(this).val().charAt(3);
		 var month_last_digit = $(this).val().charAt(4);
		 
		 if(month_first_digit==1 && month_last_digit>2){
			 console.log('testing-'+month_last_digit);
			 var prev_part_date = current_entered_val.substr(0,(current_entered_val.length-1));
			 $(this).val(prev_part_date);
		 }
		 
		 if(current_entered_val.length==4){
			 var first_part_date = current_entered_val.substr(0,(current_entered_val.length-1));
			 var last_part_date = current_entered_val.substr(current_entered_val.length - 1);
			 if(Number(last_part_date)>1){
				$(this).val(first_part_date+'0'+last_part_date+'-'); 
			 }
		 }
		 
		
		 
	 });
	 
	 $("#date_of_birth").inputFilter(function(value) {
		 if(value==''){
			 return true;
		 }
		return /^0?(\d|\-)*$/.test(value); 
	 });
	 
	 
	 function isDatevalid(txtDate){
		  var currVal = txtDate;
		  if(currVal == '')

			return false;
		   
		  //Declare Regex 

		  var rxDatePattern = /^(\d{1,2})(\-)(\d{1,2})(\-)(\d{4})$/;

		  var dtArray = currVal.match(rxDatePattern); // is format OK?
		 
		  if (dtArray == null)

			 return false;

		  //Checks for mm/dd/yyyy format.

		  dtDay = dtArray[1];

		  dtMonth= dtArray[3];

		  dtYear = dtArray[5];
		 
		 var dexpb = new Date();
		 var ebcurr_year = dexpb.getFullYear();
		
		  if (dtMonth < 1 || dtMonth > 12)

			  return false;

		  else if (dtDay < 1 || dtDay> 31)

			  return false;

		  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)

			  return false;

		  else if (dtMonth == 2)

		  {

			 var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));

			 if (dtDay> 29 || (dtDay ==29 && !isleap))

				  return false;

		  }
		  
		  if(Number(ebcurr_year) <= Number(dtYear)){
			  return 'exceed';
		  }

		  return true;
	}
	  
	function validateEmail() {
		var emailText = $("#email").val();
		var pattern = /^[a-zA-Z0-9\-_]+(\.[a-zA-Z0-9\-_]+)*@[a-z0-9]+(\-[a-z0-9]+)*(\.[a-z0-9]+(\-[a-z0-9]+)*)*\.[a-z]{2,4}$/;
		if (pattern.test(emailText)) {
			return true;
		} else {
			
			return false;
		}
	}
	
	
	
	function get_course_data_by_id(course_id){
		
		for (let i = 0; i < booking_course_data.length; i++) {
			if (Number(booking_course_data[i].id)==Number(course_id)) {
				console.log("Matched-"+course_id);
				booking_course_selected.push(booking_course_data[i]);
				return booking_course_data[i];
			}
		}
		
	}
	
	
	$("#previous_step").click(function(e){
		e.preventDefault();
		
		var clickedBtn = $( event.target );
		if (typeof clickedBtn === "undefined") {
			var clickedBtn = $( this );
		}
		
		$("#next_step").show();
		$("#pay_now").hide();
		var current_booking_step = $('.active_step');
		var prev_booking_step = $('.active_step').prev();
		
		var step_index = parseInt(prev_booking_step.attr('tabindex')) ;
		if(step_index==1){
			clear_header_form_city_course();
		}
		
		current_booking_step.removeClass('active_step');
		current_booking_step.addClass('hide_booking_step');
		
		prev_booking_step.addClass('active_step');
		prev_booking_step.removeClass('hide_booking_step');
		
		
		var completed_steps_filler = 25*step_index;
		$('.completed_steps_fill').css('width',completed_steps_filler+'%');
		
		if(prev_booking_step.hasClass('book_step_one')){
			$('.booking_action').hide();
			
			return;
		}
		$('.booking_action').css({'display' : 'flex'});
		$('html, body').animate({
			scrollTop: $("#booking_wrapper").offset().top
		}, 1000);
		
	});
	
	function clear_header_form_city_course(){
		$("#courses_code").change(function(){
			window.history.pushState("object or string", "Title", object_almosoft.bookingurl);
			object_almosoft.course_code='';
			object_almosoft.location_city='';
		});
	}
	
	$("#booking_steps").on('change','.location_row', function(){
		
		var current_row_obj = $(this);
		var location_city = current_row_obj.val();
		var course_type = current_row_obj.parent().prev().text();
		if(location_city){
			$(".bookingoverlay").css('display','flex');
			var data = {
				action: 'course_available_dates',
				city:location_city,
				course_type:course_type,
				post_type: 'POST',
				
			};
			
			$.post(object_almosoft.ajaxurl, data, function(response) {
				
				if(response.success === false) {
					console.log('error encounter');
				}
				
				if(response.success == true) {
					
					//$("#date_"+course_type).html(response.data);
					var number_of_response = response.data.length;
					
					var available_dates_msg = "Kies uw datum";
					if(!number_of_response){
						available_dates_msg = "Geen vrije datum";
					}
					var date_dropdown_options_html = "<option value=''>"+available_dates_msg+"</option>";
					$.each(response.data, function (index, value) {
						date_dropdown_options_html += "<option value='"+this.id+"'>"+this.date+"</option>";
						// Get the items
						var items = this.id;
						
						booking_course_data.push({id:this.id, course_type:this.course_type, date:this.date, time:this.time,city:this.city});	
						
					});
					
					$("#date_"+course_type).html(date_dropdown_options_html);
					$(".bookingoverlay").hide();
					
				}
				
			});
		}else{
			var available_dates_msg = "";
			var date_dropdown_options_html = "<option value=''>"+available_dates_msg+"</option>";
			$("#date_"+course_type).html(date_dropdown_options_html);
		}
		
	});
	
	var click_paypal = false;
	$(".booking_wrapper").on('click','.click_paypal', function(){
		if (!click_paypal){
			var selected_payment_option = $("input[name='payment_method']:checked").val();
			var customer_data = JSON.stringify(customer_booking_info);
			var booking_order_details = JSON.stringify(booking_course_selected);
			$(".bookingoverlay").css('display','flex');
			var data = {
				action: 'booking_payment_process_paypal',
				dataType: "json",
				payment_option: selected_payment_option,
				customer_data:customer_data,
				booking_order_details: booking_order_details,
				post_type: 'POST',

			};

			$.post(object_almosoft.ajaxurl, data, function(response) {
				$(".bookingoverlay").hide();

				if(response.success == true) {
					window.paypal_id = response.data;
				}

			});
			click_paypal = true;
		}

	});
	$(".booking_wrapper").on('click','#pay_now', function(){

		console.log('payment function called');
		var selected_payment_option = $("input[name='payment_method']:checked").val();
		var customer_data = JSON.stringify(customer_booking_info);
		var booking_order_details = JSON.stringify(booking_course_selected); 
		$(".bookingoverlay").css('display','flex');
		$( "#pay_now" ).prop( "disabled", true );
		var data = {
			action: 'booking_payment_process',
			dataType: "json",
			payment_option: selected_payment_option,
			customer_data:customer_data,
			booking_order_details: booking_order_details,
			post_type: 'POST',
			
		};
		
		$.post(object_almosoft.ajaxurl, data, function(response) {
			$(".bookingoverlay").hide();
			//$( "#pay_now" ).prop( "disabled", false );
			if(response.success === false) {
				console.log('error encounter');
			}
			
			if(response.success == true) {
				if(response.data.pay_method!='skip'){
					window.location.replace(response.data.payment_url);
				}else{
					console.log(response.data.response.status);
					if(response.data.response.status=='ok'){
						
						$("#booking_completed").css('display','block');
						$(".booking_action").hide();
						$(".book_step_four").hide();
						$('html, body').animate({
							scrollTop: $("#booking_wrapper").offset().top
						}, 1000);
						
					}else{
						$("#booking_completed").html(object_almosoft.registration_failed);
					}
					
				}
			}
			
		});
		
	});
	
	$("#header_step_action").click(function(){
		console.log('action button clicked');
		var selected_course = $("#courses_code_head").val();
		var location_city = $("#courses_location_head").val();
		var error = '';
		if(!location_city){
			$("#courses_location_head").next().addClass('booking_invalid');
			error = 'yes';
		}
		
		if(!selected_course){
			$("#courses_code_head").next().addClass('booking_invalid');
			error = 'yes';
		}
		
		if(error=='yes'){
			return;
		}
		
		var pajaxUrl = object_almosoft.bookingurl+"?code="+selected_course+"&city="+location_city;
		window.location.replace(pajaxUrl);
    });
    
    $("#header_step_action_niwo").click(function(){
		console.log('action button clicked');
		var selected_course = $("#courses_code_head_niwo").val();
		var location_city = $("#courses_location_head_niwo").val();
		var error = '';
		if(!location_city){
			$("#courses_location_head_niwo").next().addClass('booking_invalid');
			error = 'yes';
		}
		
		if(!selected_course){
			$("#courses_code_head_niwo").next().addClass('booking_invalid');
			error = 'yes';
		}
		
		if(error=='yes'){
			return;
		}
		
		var pajaxUrl = object_almosoft.bookingurl_niwo+"?code="+selected_course+"&city="+location_city;
		window.location.replace(pajaxUrl);
    });
	
	var course_code_from_url = object_almosoft.course_code;
	var course_location_from_url = object_almosoft.location_city;
	
	if(course_code_from_url){
		var course_arr = course_code_from_url.split(',');
		render_step_two_form(course_arr);
		$("#location_"+course_arr[0]).val(course_location_from_url);
		$("#location_"+course_arr[0]).trigger('change');
		
		$("#courses_code").val(course_arr);
		$("#courses_code").trigger('change');
		
		$('.booking_action').css({'display' : 'flex'});
		var completed_steps_filler = 25*2;
		$('.completed_steps_fill').css('width',completed_steps_filler+'%');
		
	}
	
	$("#booking_steps").on("click", ".remove", function () {  
		var selected_course_length = $(".date_form_row").length;
		$(this).closest(".date_form_row").hide('slow', function(){ 
			$(this).remove(); 
			
		});
		
        if(Number(selected_course_length)<2){
			$("#previous_step").trigger("click");
		}
		
    }); 
	
}( jQuery ));




jQuery( document ).ready(function($) {
	$('.paypal-button-container').hide();
	$("body").on("click", ".payment_method_full_full_li", function (){
		$('.paypal-button-container').show();
	});

	$(new Option('Alle 3 €750', 'alle_3')).appendTo('#courses_code_head');

	$('#courses_code_head').on('select2:select', function (e) {
		var data = e.params.data;
		if (data.id == 'alle_3'){
			var Values = new Array();
			Values.push("RV1");
			Values.push("V2C");
			Values.push("V3C");

			$("#courses_code_head").val(Values).trigger('change');
		}  
	});
    
    $(new Option('Alle cursussen', 'alle_curs')).prependTo('#courses_code_head_niwo');
    
    $('#courses_code_head_niwo').on('select2:select', function (e) {
		var data = e.params.data;
		if (data.id == 'alle_curs'){
			var Values = new Array();
			Values.push("ONWG-1");
			Values.push("ONWG-2");
            Values.push("ONPG");
            Values.push("ONBG");
            Values.push("ONCG");
            Values.push("ONFM");

			$("#courses_code_head_niwo").val(Values).trigger('change');
		}  
	});

$('.selector_btn a').on('click', function (e) {

            e.preventDefault();

            $(this).parent().addClass('active');
            $(this).parent().siblings().removeClass('active');

            target = $(this).attr('href');

            $('.courses_tabs > div').not(target).hide();

            $(target).fadeIn(600);

        });
        
	});
    
jQuery( document ).ready(function($) {
    $("option").each(function() {
    var text = $(this).text();
    text = text.replace("ONWG-1", "Wegvervoer goederen 1");
    $(this).text(text);
	});  
    $("option").each(function() {
    var text = $(this).text();
    text = text.replace("ONWG-2", "Wegvervoer goederen 2");
    $(this).text(text);
	});  
    $("option").each(function() {
    var text = $(this).text();
    text = text.replace("ONPG", "Personeelsmanagement");
    $(this).text(text);
	});  
    $("option").each(function() {
    var text = $(this).text();
    text = text.replace("ONBG", "Bedrijfsmanagement");
    $(this).text(text);
	});  
    $("option").each(function() {
    var text = $(this).text();
    text = text.replace("ONCG", "Calculatie");
    $(this).text(text);
	});  
    $("option").each(function() {
    var text = $(this).text();
    text = text.replace("ONFM", "Financieel management");
    $(this).text(text);
	});  
});