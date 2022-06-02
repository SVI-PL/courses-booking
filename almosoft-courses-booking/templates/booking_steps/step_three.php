<div class="book_step book_step_three hide_booking_step" tabindex='3'>
    <div class="booking_items">
        <div class="booking_grid_heading">
            <div class='booking_title_col_one'>Cursus</div>
            <div class='booking_title_col_two'>Locatie</div>
            <div class='booking_title_col_three'>Cursus Datum</div>
            <div class='booking_title_col_four'>Tarief</div>
            <div class='booking_title_col_five'>Examen</div>
            <!-- <div class='booking_title_col_one'>Cursus</div> -->
            <!-- <div class='booking_title_col_two'>Time</div> -->
            <!-- <div class='booking_title_col_three'>Locatie</div> -->
            <!-- <div class='booking_title_col_four'>Start Date</div> -->
            <!-- <div class='booking_title_col_five'>Tarief</div> -->
            <!-- <div class='booking_title_col_six'>Storting</div> -->
        </div>
        <div class='booking_item_body'>
            <div class="booking_item"></div>

        </div>
        <div class='booking_footer_totals'>
            <div class='total_label'>Totaal</div>
            <div class='grand_total' data-booking-value="0" id='grand_total'></div>
            <!-- <div class='partial_total' data-booking-value="0" id='partial_total'></div> -->
        </div>

    </div>
    <div class="customer_info">
        <div class="form_control_row flexcol_two">
            <div class='form_col_one'>
                <label for='first_name'>Voornaam</label>
                <input type='text' name='first_name' id='first_name' class='required' value='' placeholder=''/>
            </div>
            <div>
                <label for='middle_name'>Tussenvoegsel</label>
                <input type='text' name='middle_name' id='middle_name' value='' placeholder=''/>
            </div>
            <div class='form_col_two'>
                <label for='last_name'>Achternaam</label>
                <input type='text' name='last_name' id='last_name' class='required' value='' placeholder=''/>
            </div>
        </div>

        <div class="form_control_row flexcol_two">
            <div class='form_col_one'>
                <label for='phone'>Telefoon </label>
                <input type='text' name='phone' id='phone' class='required' value='' placeholder=''/>
            </div>
            <div class='form_col_two'>
                <label for='email'>E-mail</label>
                <input type='email' name='email' id='email' class='required' value='' placeholder=''/>
            </div>
        </div>

        <div class="form_control_row">
            <label for='address'>Straat en huisnummer</label>
            <input type='text' name='address' id='address' class='required' value='' placeholder=''/>
        </div>

        <div class="form_control_row flexcol_two">
            <div class='form_col_one'>
                <label for='postcode'>Postcode</label>
                <input type='text' name='postcode' maxlength='8' id='postcode' class='required' value=''
                       placeholder=''/>
            </div>
            <div class='form_col_two'>
                <label for='residence'>Woonplaats</label>
                <input type='text' name='residence' id='residence' class='required' value='' placeholder=''/>
            </div>
        </div>

        <div class="form_control_row flexcol_two">
            <div class='form_col_one calendar_icon'>
                <label for='date_of_birth'>Geboortedatum </label>
                <input type='text' name='date_of_birth' placeholder='dd-mm-yyyy' maxlength='10' id='date_of_birth'
                       class='required' value='' placeholder=''/>
                <span id='date_format_valid_msg' class='invalid_input'>Voer uw geboortedatum in het formaat in (dd-mm-yyyy).</span>
            </div>
            <div class='form_col_two'>
                <label for='referral'>Hoe heeft u ons gevonden?</label>
                <select name='referral' id='referral' class='select_required'>
                    <?php echo $data->booking->stringconvertto_options($data->booking->referral_options,
                    array('current_val'=>'','blank_option'=>true, 'blank_option_label'=>'Kies uw referral')); ?>
                </select>

            </div>
        </div>

    </div>
    <div class="clearfix form_control_row" style="text-align: center">
        <label id="terms_required"><input type="checkbox"  required name="terms"> Ik ga akkoord met de algemene voorwaarden</label>
    </div>
</div>