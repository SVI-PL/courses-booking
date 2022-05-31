
<div class="header_form">
            <div class="couse_selector">
                <div class="selector_btn">
                    <div class="vrach_btn active"><a href="#vrach_tab">Vrachtwagen theorie</a></div>
                    <div class="niwo_btn"><a href="#niwo_tab">NIWO</a></div>
                </div>
            </div>
            <div class="courses_tabs">

                <div id="vrach_tab">
                <div class="vrach_tab">
                    <div class="course_select">
                        <select name='courses_code_head[]' id='courses_code_head' class='select_required'
                            multiple="multiple">
                            <?php echo $data->booking->stringconvertto_options($data->booking->courses); ?>
                        </select>
                    </div>
                    <div class="location_select">
                        <select name='courses_location_head' id='courses_location_head' class='select_required'>
                            <?php echo $data->booking->stringconvertto_options($data->booking->locations, array('current_val'=>'','blank_option'=>true, 'blank_option_label'=>'Kies uw locatie')); ?>
                        </select>
                    </div>
                    <button id="header_step_action" class="form_btn head_btn">Inschrijven</button>
                </div>    
                </div>

                <div id="niwo_tab">
                <div class="niwo_tab">
                    <div class="course_select">
                        <select name='courses_code_head[]' id='courses_code_head_niwo' class='select_required'
                            multiple="multiple">
                            <?php echo $data->booking->stringconvertto_options($data->booking->niwo_courses); ?>
                        </select>
                    </div>
                    <div class="location_select">
                        <select name='courses_location_head' id='courses_location_head_niwo' class='select_required'>
                            <?php echo $data->booking->stringconvertto_options($data->booking->locations, array('current_val'=>'','blank_option'=>true, 'blank_option_label'=>'Kies uw locatie')); ?>
                        </select>
                    </div>
                    <button id="header_step_action_niwo" class="form_btn head_btn">Inschrijven</button>
                </div>
                </div>
            </div>
        </div>