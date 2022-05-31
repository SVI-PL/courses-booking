<div class="book_step book_step_four hide_booking_step" tabindex='4'>

	<h2><?php echo $data->booking->payment_method_title; ?></h2>
	<?php if(!empty($data->booking->payment_method_description)){ ?>
	<p><?php echo $data->booking->payment_method_description ?></p>
	<?php } ?>
    <?php
    $paypal_key = get_option('almosoft_payment');
    $paypal_key = $paypal_key['paypal_key'];
    ?>
	<div class="payment_options">
		<ul>
			<li><input type='radio' name='payment_method' id='payment_method_skip' value='skip'><label for='payment_method_skip'>Ik betaal op locatie</label></li>
			<li><input type='radio' name='payment_method' id='payment_method_partial' value='partial'><label for='payment_method_partial'>Ik doe een aanbetaling van <span id='per_course_price'></span> euro - iDeal(Mollie)</label></li>
			<li><input type='radio' name='payment_method' id='payment_method_full' value='full'><label for='payment_method_full'>Ik betaal <span id='all_course_price_total'></span> euro vooraf - iDeal(Mollie)</label></li>

                <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $paypal_key;?>&components=buttons&disable-funding=credit,card,bancontact,blik,eps,giropay,ideal,mercadopago,mybank,p24,sepa,sofort,venmo&currency=EUR"></script>
                <li class="paypal_full"> <input type="radio" name="payment_method" value="paypal" id="paypal_full"><label for='paypal_full'>Ik betaal <span id='all_course_price_total_paypal'></span> euro vooraf - Paypal</label></li>
                <input type="hidden" class="click_paypal">
                <div id="paypal-buttons-container"></div>
                <style>
                    #paypal-buttons-container{
                        width: 100px!important;
                    }
                </style>
                <script>
                    paypal.Buttons({
                        style: {
                            layout:  'vertical',
                            color:   'white',
                            shape:   'rect',
                            label:   'paypal',
                            size: 'small',

                        },
                        createOrder: function(data, actions) {
                            $('.click_paypal').click();
                            // This function sets up the details of the transaction, including the amount and line item details.
                            return actions.order.create({
                                purchase_units: [{
                                    amount: {
                                        value: Number(object_almosoft.price)
                                    }
                                }]
                            });
                        },
                        onApprove: function(data, actions) {
                            // This function captures the funds from the transaction.
                            return actions.order.capture().then(function(details) {
                                let status = details.status;
                                if (status == 'COMPLETED'){
                                    var data = {
                                        action: 'payment_process_paypal_success',
                                        dataType: "json",
                                        paypal_id: window.paypal_id,
                                        post_type: 'POST',

                                    };
                                    $.post(object_almosoft.ajaxurl, data, function(response) {
                                            window.location.replace('https://nuvrachtwagen.nl/bedankt/');
                                    });
                                }
                                // This function shows a transaction success message to your buyer.

                            });
                        }
                    }).render('#paypal-buttons-container');

                    // Listen for changes to the radio buttons
                    document.querySelectorAll('input[name=payment_method]')
                        .forEach(function (el) {
                            el.addEventListener('change', function (event) {
                                if (event.target.value === 'paypal') {
                                    document.body.querySelector('#paypal-buttons-container')
                                        .style.display = 'block';
                                    document.body.querySelector('#pay_now')
                                        .style.display = 'none';
                                } else {
                                    document.body.querySelector('#paypal-buttons-container')
                                        .style.display = 'none';
                                    document.body.querySelector('#pay_now')
                                        .style.display = 'inline-block';

                                }
                            });
                        });

                    // Hide PayPal button by default
                    document.body.querySelector('#paypal-buttons-container')
                        .style.display = 'none';
                </script>
		</ul>
	</div>

</div>