@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">

        <div class="page-title">
            <div class="title_left">
                <h3>Event</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-xs-12">

                <div class="x_panel">
                    <div class="x_title">
                        <h2>edit event</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('event')}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <form id="event-edit-form" action="{{URL::to('event/'.$event->event_id)}}"  method="post" enctype="multipart/form-data" >
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">

                                <!-- start form for validation -->
                                <label for="fullname">Event Title* :</label>
                                <input class="form-control" value="{{ $event->event_title }}" name="event_title" type="text"><ul class="parsley-errors-list">{{ $errors->event->first('event_title') }}</ul>


                                <label for="fullname">Start Date* :</label>
                                <input id="start-date" class="form-control" name="start_date" type="text" value="<?php echo date("d-m-Y", strtotime($event->start_date)); ?>"><ul class="parsley-errors-list">{{ $errors->event->first('start_date') }}</ul>
                                <label for="fullname">Start Time* :</label>
                                <input class="form-control" name="start_time" id="event-start-time" type="text" value="<?php echo $event->start_time; ?>"><ul class="parsley-errors-list">{{ $errors->event->first('start_time') }}</ul>

                                <label for="fullname">End Date* :</label>
                                <input value="<?php echo date("d-m-Y", strtotime($event->end_date)); ?>" id="end-date" class="form-control" name="end_date" type="text"><ul class="parsley-errors-list">{{ $errors->event->first('end_date') }}</ul>

                                <label for="fullname">End Time* :</label>
                                <input  class="form-control" name="end_time" id="event-end-time" type="text" value="<?php echo $event->end_time; ?>"><ul class="parsley-errors-list">{{ $errors->event->first('end_time') }}</ul>

                                <label for="fullname">Website* :</label>
                                <input name="website" class="form-control" type="text" value="<?php echo $event->website; ?>"><ul class="parsley-errors-list">{{ $errors->event->first('website') }}</ul>




                                <!-- end form for validations -->

                            </div></div>
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">




                                <label for="fullname">Organization :</label>
                                <select style="width:100%;" class="form-control" name="organizers" id='organization' data-placeholder="Choose a Organization...">
                                    <option value=''>-----select-----</option>
                                    @foreach($organization as $organization_val)
                                    <option <?php echo ($event->organizers == $organization_val->organization_id) ? "selected" : ''; ?> value='{{$organization_val->organization_id}}'>{{$organization_val->organization_name}}</option>
                                    @endforeach;   

                                </select>
                                <ul class="parsley-errors-list">{{ $errors->event->first('organizers') }}</ul>
                                <label for="email">Location* :</label>

                                <select class="form-control select_input" name="location" id="event-location" style="width:90%">
                                    @foreach($location as $location_val)
                                    <option <?php echo ($event->location == $location_val->venue_id) ? "selected" : ''; ?> value='{{$location_val->venue_id}}'>{{$location_val->venue_name}}</option>
                                    @endforeach;   
                                </select>
                                <div class='pull-right'>
                                    <button data-target="#addLocation" data-toggle="modal" data-type="normal_user" class="btn btn-success  btn-sm btn-square active pull-right add_button" type="button" title="add location"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span>
                                    </button>   
                                </div>
                                <ul class="parsley-errors-list">{{ $errors->event->first('location') }}</ul>

                                <label for="email">Intended Audience: :</label>
                                <select name="audience[]" id="audience" class="form-control select2-multiple" multiple>
                                    <optgroup>
                                        <option selected="" value="Child" <?php
                                        if (strpos($event->audience, 'Child')) {
                                            echo "selected";
                                        };
                                        ?>>Child</option>
                                        <option value="Youngest" <?php
                                        if (strpos($event->audience, 'Youngest')) {
                                            echo "selected";
                                        };
                                        ?>>Youngest</option>
                                        <option value="Oldest" <?php
                                        if (strpos($event->audience, 'Oldest')) {
                                            echo "selected";
                                        };
                                        ?>>Oldest</option>
                                    </optgroup>
                                </select>
                                <ul class="parsley-errors-list">{{ $errors->event->first('audience') }}</ul>
                                <label for="email">Phone Number:</label>
                                <input id="event_contact" name="contact_info" data-inputmask="'mask' : '(999) 999-9999'"  value="{{ $event->contact_info }}" class="form-control textfield" type="tel">
                                <ul class="parsley-errors-list">{{ $errors->event->first('contact_info') }}</ul>

                                <label for="email">Event Purpose :</label>
                                <input name="purpose" id="event-purpose"  value="{{ $event->purpose }}" class="form-control textfield" type="text">
                                <ul class="parsley-errors-list"></ul>
                                <label for="email">Event Keywords :</label>
                                <select name="keyword[]" id="category" class="form-control select2-multiple" multiple>
                                    <?php
                                    if ($event->category_id != '') {
                                        $keyword_val = explode(',', $event->category_id);
                                    } else {
                                        $keyword_val = array();
                                    }
                                    ?>

                                    @foreach($category as $val)
                                    <option value="{{ $val->category_name }}" <?php
                                    if (!empty($keyword_val)) {
                                        if (in_array($val->category_name, $keyword_val)) {
                                            echo "selected";
                                        }
                                    };
                                    ?>>{{ $val->category_name }}</option>
                                    @endforeach;

                                </select>
                                <ul class="parsley-errors-list">{{ $errors->event->first('keyword') }}</ul>


                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="event_id" value="{{$event->event_id}}">
                                <input type="hidden" name="old_image_id" value="{{$event->image_id}}">
                                <input type="hidden" id="attach_ids" name="image_id" value="{{$event->image_id}}"> 
                                <input type="hidden" id="pre_ids"> 





                                <!-- end form for validations -->

                            </div></div>

                        <div class="clearfix"></div><br>
                        <div class="col-md-12 col-xs-12">
                            <label class="control-label">Event Description *</label></br></br>
                            <textarea class="ckeditor form-control" name="event_description">{{ $event->event_description }}</textarea>
                            <ul class="parsley-errors-list">{{ $errors->event->first('event_description') }}</ul>
                        </div>
                        <div class="clearfix"></div>
                        </br>
                        <div class="col-md-12 col-xs-12">
                            <label class="control-label">Select Featured Image</label></br>
                            <input id="image" name="image" type="file" multiple class="file-loading">
                              <ul>
                                <li><b>Featured image should be in either .JPG, .PNG, .JPG, .JPEG formats.</b></li>    
                                <li><b>Width of featured image file should be a 300 px.</b></li>
                                <li><b>Height of featured image file should be a 300 px.</b></li>    
                                
                                
                            </ul>
                        </div>
                        <div class="clearfix"></div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-10  pull-left">
                                <a href="{{URL::to('category')}}" class="btn btn-primary">Cancel</a>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>
                </div>










            </div>
        </div>


        @include('layout.footer')
    </div>
    <div align='center' class="wait">
        <div class="loader-center"><img height='50' width='50' src='{{ asset("/bower_components/gentelella/production/images/ajax-loader.gif") }}'></div>
    </div>

    <div class="modal modal-md" id="addLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <form data-parsley-validate  method="post" id="create_location" enctype="multipart/form-data">
            <div class="modal-dialog" role="document">

                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="exampleModalLabel">Add Location</h4>
                    </div>
                    <div class="modal-body clearfix">
                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">Name *</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <input type="text" name="venue_name" class="form-control" id='venue_name'>
                                <ul class="parsley-errors-list venue_name_err">{{ $errors->location->first('venue_name') }}</ul>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">Description</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <textarea class="form-control" name="venue_description" id='venue_description'>{{ old('address') }}</textarea>
                                <ul class="parsley-errors-list venue_description_err">{{ $errors->location->first('venue_description') }}</ul>
                            </div>

                        </div>
                        <div id="images_div">
                            <div class="form-group col-md-12">
                                <div class="col-md-2 col-md-offset-1">
                                    <label for="message-text" class="control-label">Image</label>
                                </div>
                                <div class="col-md-6 col-md-offset-1">
                                    <input type="file" name="location_image[]" class="form-control">
                                    <ul class="parsley-errors-list"></ul>
                                </div>
                                <div class="col-md-2 col-sm-1 col-xs-2">
                                    <button class="btn btn-success btn-xs btn-square" id="add_more" type="button"><span aria-hidden="true" class="fa fa-plus">Add More</span>
                                    </button></div>

                            </div>
                        </div>


                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="recipient-name" class="control-label">Country *</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <select class="form-control" id="country" name="country" data-placeholder="Choose a Country..." style="width:100% ;" tabindex="1">
                                    @foreach($country as $count_val)
                                    <option <?php echo (old('country') == $count_val->id) ? "selected" : ''; ?> value="{{ $count_val->id }}">{{ $count_val->name }}</option>
                                    @endforeach;
                                </select>
                                <ul class="parsley-errors-list country_err">{{ $errors->event->first('country') }}</ul>

                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">State *</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <select class="form-control select_input" name="state" id="state" data-placeholder="Choose a State...">

                                </select>
                                <ul class="parsley-errors-list state_err">{{ $errors->event->first('state') }}</ul>  
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">City *</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <select class="form-control select_input" name="city" id="city" data-placeholder="Choose a City...">

                                </select>
                                <ul class="parsley-errors-list city_err">{{ $errors->event->first('city') }}</ul>  
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">Address *</label>
                            </div>
                            <div class="col-md-7 col-md-offset-1">
                                <textarea class="form-control" name="address" id='address'>{{ old('address') }}</textarea>
                                <ul class="parsley-errors-list address_err">{{ $errors->location->first('address') }}</ul>
                            </div>
                            <button class="btn btn-success  btn-xs btn-square active add_map" type="button"><span aria-hidden="true" class="fa fa-location-arrow"></span>
                            </button>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">Latitude *</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <input type="text" name="latitude" class="form-control" id='latitude'>
                                <ul class="parsley-errors-list">{{ $errors->location->first('latitude') }}</ul>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">Longitude*</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <input type="text" name="longitude" class="form-control" id='longitude'>
                                <ul class="parsley-errors-list">{{ $errors->location->first('longitude') }}</ul>
                            </div>
                        </div>
                        <div class="form-group col-md-12 map_div" style="display:none">
                            <div class="col-md-2 col-md-offset-1">
                                <label for="message-text" class="control-label">Map</label>
                            </div>
                            <div class="col-md-8 col-md-offset-1">
                                <div id="mapCanvas"></div
                            </div>
                        </div>
                       <input type="hidden" name='zipcode' id='zipcode'>
                        <input type="hidden" name='request_user_id' id='request_user_id'>
                    </div>
                    <div class="clearfix"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success btn-sm" id='add_location'>Save</button>
                    </div>
                </div>

            </div></div></form></div>
<div class="form-group col-md-12"  id="add_more_div" hidden="">
    <div class="col-md-2 col-md-offset-1">
        <label for="message-text" class="control-label"></label>
    </div>
    <div class="col-md-6 col-md-offset-1">
        <input type="file" name="location_image[]" class="form-control">
        <ul class="parsley-errors-list"></ul>
    </div>
    <div class="col-md-2 col-sm-1 col-xs-2">
        <button class="btn btn-danger btn-xs btn-square" id="remove_image" type="button"><span aria-hidden="true" class="fa fa-minus"></span>
        </button></div>

</div>
<style>
    textarea.form-control {
        height: 141px;
    }   
    .file-preview-thumbnails  img {

        height: 160px !important;
        width: 160px !important;
    }
    .file-thumb-progress .progress{
        display: none
    }

    #mapCanvas {
        width:  300px;
        height: 200px;
        float: left;
    }

</style>
<script>
    $(document).ready(function () {
    $("#event_contact").inputmask();
            $(document).on('click', '.add_map', function(){

    codeAddress();
    });
            $('#event-start-time').timepicker({
    showSeconds: true,
    });
            $('#event-end-time').timepicker({
    showSeconds: true,
    });
            $('#start-date').datepicker({
    format: 'dd-mm-yyyy',
            startDate: new Date(),
            autoclose: true
    });
            $(document).on('change', '#start-date', function () {
    $('#end-date').removeAttr('disabled');
            $('#end-date').datepicker('remove')
            var endate = $(this).val();
            $('#end-date').datepicker({
    format: 'dd-mm-yyyy',
            startDate: endate,
            autoclose: true,
    });
            $('#end-date').val(endate);
    });
            $("#category").select2({
    width: '100%',
            tags: true,
            createTag: function (tag) {

            // check if the option is already there
            found = false;
                    $("#category option").each(function() {
            if ($.trim(tag.term).toUpperCase() == $.trim($(this).text()).toUpperCase()) {
            found = true;
            }
            });
                    // if it's not there, then show the suggestion
                    if (!found) {
            return {
            id: tag.term,
                    text: tag.term,
                    isNew: true
            };
            }
            }
    });
            $("#audience").select2({
    width: '100%'
    });
            $("#country").chosen({no_results_text: "Oops, nothing found!", width: '100%'});
            $("#state").chosen({no_results_text: "Oops, nothing found!", width: '100%'});
            $("#city").chosen({no_results_text: "Oops, nothing found!", width: '100%'});
            var country_id = ($("#country").val());
            getState(country_id);
            $(document).on('change', '#country', function(){
    var country_id = ($(this).val());
            getState(country_id);
    });
            $(document).on('change', '#state', function(){
    var state_id = ($(this).val());
            getCity(state_id);
    });
            // location

            // Location
            $("#organizers").chosen({no_results_text: "Oops, nothing found!"});
            $("#organization").chosen({no_results_text: "Oops, nothing found!"});
            $("#event-location").chosen({no_results_text: "Oops, nothing found!"});
//        var organizer_id = ($("#organizers").val());
//        getLocation(organizer_id);
//        getOrganization(organizer_id);
//        
//        $(document).on('change', '#organizers', function(){
//                var organizer_id = ($(this).val());
//                 getLocation(organizer_id);
//                 getOrganization(organizer_id);
//        });


            $('.add_button').click(function(){
    $("#create_location").find("#longitude").val('');
            $("#create_location").find("#address").val('');
            $("#create_location").find("#latitude").val('');
            $("#create_location").find("#venue_name").val('');
            $("#create_location").find("#venue_description").val('');
            $("#state").val('').trigger("chosen:updated");
            $("#city").val('').trigger("chosen:updated");
            $("#country").val('').trigger("chosen:updated");
            $(".map_div").css("display", "none");
            var organizer_id = ($("#organizers").val());
            $("#request_user_id").val(organizer_id);
        });
            $("#create_location").submit(function(e){
    e.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
            url: '{{ url('add_location')}}',
                    method: "POST",
                    data:formData,
                    processData: false,
                    contentType: false,
                    success:function(result){
                    var res = $.parseJSON(result);
                            if (res.status == false){
                    $.each(res.data, function(index, value) {
                    $("." + index + '_err').empty();
                            $("." + index + '_err').text(value);
                    });
                    } else if (res.status){
                    var n = noty({
                    text: "Location added successfully",
                            type: 'success',
                            dismissQueue: true,
                            layout: 'topCenter',
                            closeWith: ['click'],
                            theme: 'relax',
                            maxVisible: 10,
                            timeout: 3000,
                            animation: {
                            open: 'animated flipInX',
                                    close: 'animated flipOutX',
                                    easing: 'swing',
                                    speed: 500
                            }
                    });
                            $('#addLocation').modal('hide');
                            var location_id = res.data.location_id;
                            var location_name = res.data.location_name;
                            $("#event-location").prepend("<option value=" + location_id + " selected='selected'>" + location_name + "</option>").trigger('chosen:updated').trigger('chosen:updated');
                    }
                    }, complete :function(){
            $('#add_location').removeAttr('disabled');
                    $(".wait").css("display", "none");
            },
                    beforeSend:function(){
                    $(".wait").css("display", "block");
                    }
            });
    });
            $(document).on('click', '#add_more', function(){
    var obj = $(document).find("#add_more_div").clone();
            console.log($(document).find("#add_more_div"));
            $(obj).removeAttr('hidden');
            $(obj).removeAttr('id');
            $(obj).appendTo("#images_div");
            });
            $(document).on('click', '#remove_image', function(){

    $(this).parents('.form-group').remove();
            });
// 


// validation

            $("#event-edit-form").validate({
    ignore:[],
            rules: {
            event_title: "required",
                    start_date: "required",
                    start_time: "required",
                    end_date: "required",
                    end_time: "required",
                    location:"required",
                    website:{
                    required: true,
                            url: true

                    },
                    event_description: {
                    required: function()
                    {
                    var val = CKEDITOR.instances.event_description.getData();
                            console.log(val);
                            if (val == ''){
                    return true;
                    } else{
                    return false;
                    }

                    }
                    }
            },
            messages: {
            event_title: "Please enter event title",
                    start_date: "Please enter event start date",
                    start_time: "Please enter event start time",
                    end_date: "Please enter event end date",
                    end_time: "Please enter event end time",
                    location: "Please enter event location",
                    website: {
                    required:"Please enter event website",
                            url:"Please enter valid event website",
                    },
                    event_description:"please enter event description"
            },
            errorPlacement: function(error, element)
            {
            if (element.attr("name") == "event_description")
            {
            error.insertAfter("#cke_event_description");
            } else {
            error.insertAfter(element);
            }
            }
    });
    });
            function getState(country_id) {
            var state = $("#state");
                    $.ajax({
                    type: 'POST',
                            url: '{{ url('state')}}',
                            data: {country_id: country_id},
                            success: function (data) {

                            if (data != false) {
                            $(state).empty();
                                    var stateArray = $.parseJSON(data);
                                    $(stateArray).each(function (index, value) {
                            $(state).append('<option value="' + value.state_id + '">' + value.name + '</option>');
                                    $(state).trigger("chosen:updated");
                            });
                            } else{
                            state.empty();
                                    $(state).trigger("chosen:updated");
                            }
                            }, complete:function(){
                    $(state).val('{{ $event->state }}');
                            $(state).trigger("chosen:updated");
                    }
                    });
                    $(state).val('{{ $event->state}}').trigger("chosen:updated");
            }

    function getCity(state_id) {
    var city = $("#city");
            $.ajax({
            type: 'POST',
                    url: '{{ url('city')}}',
                    data: {state_id: state_id},
                    success: function (data) {
                    if (data != false) {
                    $(city).empty();
                            var stateArray = $.parseJSON(data);
                            $(stateArray).each(function (index, value) {
                    $(city).append('<option value="' + value.city_id + '">' + value.name + '</option>');
                    });
                    } else{
                    $(city).empty();
                            $(city).trigger("chosen:updated");
                    }
                    },
                    complete:function(){
                    $(city).val('{{ $event->city }}');
                            $(city).trigger("chosen:updated");
                    }

            });
            $(city).val('{{ $event->city}}').trigger("chosen:updated");
    }

    function delete_image(ids){

    $.ajax({
    type: 'POST',
            url: '{{ url('delete_image')}}',
            data: {id: ids},
            success: function (data) {
            var res = $.parseJSON(data)
                    if (res.status == 1) {
            var attchstr = $('body').find("#attach_ids").val();
                    var new_string = remove(attchstr, ids);
                    arr = []; arr.push(new_string);
                    $('body').find("#attach_ids").val('');
                    $('body').find("#attach_ids").val(arr);
            }


            }
    });
    }

    function remove(string, to_remove)
    {

    if (string != '' && typeof string != 'undefined') {
    var elements = string.split(",");
            var remove_index = elements.indexOf(to_remove);
            elements.splice(remove_index, 1); var result = elements.join(",");
            return result;
    }
    }


//          function getLocation(organizer_id) {
//         var location = $("#event-location");
//                $.ajax({
//                type: 'POST',
//                        url: '{{ url('venue')}}',
//                        data: {organizer_id: organizer_id},
//                        success: function (data) {
//
//                        if (data != false) {
//                        $(location).empty();
//                                var stateArray = $.parseJSON(data);
//                                $(stateArray).each(function (index, value) {
//                        $(location).append('<option value="' + value.venue_id + '">' + value.address + '</option>');
//                                $(location).trigger("chosen:updated");
//                        });
//                        } else{
//                        location.empty();
//                                $(location).trigger("chosen:updated");
//                        }
//                        },complete:function(){
//                            var old_location = ('{{$event->location}}');
//                            if(old_location != ''){
//                             $(location).val(old_location);
//                             $(location).trigger("chosen:updated");
//                           }
//                            
//                        }
//                });
//        }
//        
//           function getOrganization(organizer_id) {
//                var organization = $("#organization");
//                $.ajax({
//                type: 'POST',
//                        url: '{{ url('user_organization')}}',
//                        data: {organizer_id: organizer_id},
//                        success: function (data) {
//                       
//                        if (data != false) {
//                        $(organization).empty();
//                                var stateArray = $.parseJSON(data);
//                                $(organization).append('<option value="">-----select-----</option>');
//                                $(stateArray).each(function (index, value) {
//                        $(organization).append('<option value="' + value.organization_id + '">' + value.organization_name + '</option>');
//                                $(organization).trigger("chosen:updated");
//                        });
//                        } else{
//                        organization.empty();
//                                $(organization).trigger("chosen:updated");
//                        }
//                        }, complete:function(){
//                            
//                             var old_organizer = ('{{$event->organizers}}');
//                            
//                              $(organization).val(old_organizer);        
//                              $(organization).trigger("chosen:updated");       
//                              
//                        
//
//                }
//                });
//        }


    function featured_image(ids, status){

    $.ajax({
    type: 'POST',
            url: '{{ url('featured_image')}}',
            data: {id: ids, status:status},
            success: function (data) {
            var res = $.parseJSON(data)
                    if (res.status == 1) {
            return 1;
            } else{
            return 0;
            }


            }
    });
            return status;
    }

</script>

<script>

    $(document).ready(function () {
    var arr = new Array();
            $("#image").fileinput({
    uploadUrl: '{{ url('upload_image')}}',
            uploadAsync: true,
            showRemove: false,
            overwriteInitial: false,
            resizeImage: true,
            allowedFileExtensions:['gif', 'png', 'jpg', 'jpeg'],
            initialPreview: [
<?php
if (!empty($event->images)) {
    foreach ($event->images as $val) {
        echo "'$val'" . ',';
    }
}
?>

            ],
            initialPreviewConfig: <?php
if ($event->attachment != '') {
    echo $event->attachment;
} else {
    echo '{}';
}
?>,
    });
            $image = $('#image');
            $image.on('fileuploaded', function (event, data, previewId, index) {
            var form = data.form, files = data.files, extra = data.extra,
                    response = data.response, reader = data.reader;
                    var ids = $("#attach_ids").val();
                    arr = [];
                    if (ids != ''){
            arr.push(ids);
            }

            arr.push(response);
                    $("#attach_ids").val(arr);
                    $("#pre_ids").val(arr);
                    $("#" + previewId).attr("response_id", response);
            }).on("filebatchselected", function (event, files) {
    $image.fileinput("upload");
    });
            $(document).on("click", ".kv-file-remove", function () {
             var delete_id = $(this).attr('data-key');
            if (typeof delete_id != 'undefined') {
            del_id = delete_id;
           }
          else
       {
        del_id = $(this).parents(".file-preview-frame").attr("response_id");
        }
         delete_image(del_id);
    });
            $(document).on("click", ".file-upload-indicator", function ()   {
    if ($(this).hasClass('yellow')){
    var status = 0;
    } else{
    var status = 1;
    }

    var featured_id = $(this).parents(".file-preview-frame").attr("response_id");
            if (featured_id == undefined){
    var featured_id = $(this).parents(".file-preview-frame").find('img').attr("img_id");
    }

    var res = featured_image(featured_id, status);
            if (res){
    $(this).addClass('yellow');
    } else{
    $(this).removeClass('yellow')
    }
    });
            $('body').find("#cancel").click(function () {
    var url = $(this).attr('data-href');
            var pre_ids = $("#pre_ids").val();
            if (pre_ids != '') {
    $.ajax({
    type: 'POST',
            url: '{{ url('delete_image')}}',
            data: {id: pre_ids},
            success: function (data) {
            },
            complete:function(){
            window.location.href = '{{ url('event')}}';
            }
    });
    } else{
    window.location.href = '{{ url('event')}}';
    }
    });
            var pre_ids = $("#pre_ids").val();
            if (pre_ids != '') {
    delete_image(pre_ids);
    }




    $("body").find(".file-preview-thumbnails .file-upload-indicator").each(function () {
    var featured_image = $(this).parents('.file-preview-initial').find('img').attr('featured_image');
            $(this).attr('data-key', $(this).parent('.file-actions').find(".kv-file-remove").attr("data-key"));
            if (featured_image == 1){
    $(this).addClass('yellow');
            $(this).html('<i class="fa fa-star fa-2"></i>');
    } else{
    $(this).removeClass('yellow');
            $(this).html('<i class="fa fa-star fa-2"></i>');
    }
    })

    });</script>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key= AIzaSyClyMSR2AsxJvw4wJRq-yvRcy10p0In4aY&sensor=true_OR_false"></script>
<script type="text/javascript">
            var geocoder = new google.maps.Geocoder();
            var markers = [];
            function geocodePosition(pos) {
            geocoder.geocode({
            latLng: pos
            }, function(responses) {
            if (responses && responses.length > 0) {
            var formated_add = '';
                    var len = responses[0].address_components.length;
                    for ($i = 0; $i < (len); $i++){
            if (responses[0].address_components[$i].types[0] == 'street_number' || responses[0].address_components[$i].types[0] == 'route' || responses[0].address_components[$i].types[0] == 'intersection' || responses[0].address_components[$i].types[0] == 'premise' || responses[0].address_components[$i].types[0] == 'sublocality' || responses[0].address_components[$i].types[0] == 'sublocality_level_1' || responses[0].address_components[$i].types[0] == 'ward' || responses[0].address_components[$i].types[0] == 'neighborhood' || responses[0].address_components[$i].types[0] == 'premise' || responses[0].address_components[$i].types[0] == 'subpremise' || responses[0].address_components[$i].types[0] == 'political'){
            formated_add += responses[0].address_components[$i].long_name + ',';
            }
            
             if(responses[0].address_components[$i].types[0] == 'postal_code'){
             var zipcode = responses[0].address_components[$i].long_name;   
            }
            }

            if (formated_add == ''){
            formated_add = responses[0].formatted_address;
            } else{
            s1 = formated_add.split(',');
                    s2 = s1.pop();
                    formated_add = s1.join(',');
            }
                $("#zipcode").val(zipcode);
            updateMarkerAddress(formated_add);
            } else {
            updateMarkerAddress('Cannot determine address at this location.');
            }
            });
                    }

    function updateMarkerStatus(str) {
    document.getElementById('markerStatus').innerHTML = str;
            }

    function updateMarkerPosition(latLng) {
    $('#latitude').val('');
            $('#longitude').val('');
            $('#latitude').val(latLng.lat());
            $('#longitude').val(latLng.lng());
            }

    function updateMarkerAddress(str) {
    $('#address').val();
            $('#address').val(str);
            }

    function initialize(lat, lang) {
    var latLng = new google.maps.LatLng(lat, lang);
            var map = new google.maps.Map(document.getElementById('mapCanvas'), {
            zoom: 14,
                    center: latLng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            var marker = new google.maps.Marker({
            position: latLng,
                    title: 'Point A',
                    map: map,
                    draggable: true
            });
            // Update current position info.
            updateMarkerPosition(latLng);
            geocodePosition(latLng);
            // Add dragging event listeners.
            google.maps.event.addListener(marker, 'dragstart', function() {

            });
            google.maps.event.addListener(marker, 'drag', function() {

            updateMarkerPosition(marker.getPosition());
            });
            google.maps.event.addListener(marker, 'dragend', function() {

            geocodePosition(marker.getPosition());
            });
            }

// Onload handler to fire off the app.
//google.maps.event.addDomListener(window, 'load', initialize);

    function codeAddress()
    {


    main_address = '';
            var main_address = $('#address').val();
            var country = $('#country').find(':selected').text();
            var state = $('#state').find(':selected').text();
            var city = $('#city').find(':selected').text();
            setAllMap(null); //Clears the existing marker
            if (city == '' && main_address == ''){
    var address = [state, country].join();
    } else if (city == ''){
    var address = [main_address, state, country].join();
    } else if (main_address == ''){
    var address = [city, state, country].join();
    } else{
    var address = [main_address, city, state, country].join();
    }


    if (address != '' && main_address != '')
    {


    //Clears the existing marker

    geocoder.geocode({address:address}, function(results, status)
    {
    if (status == google.maps.GeocoderStatus.OK)
    {
    $('#latitude').val(results[0].geometry.location.lat());
            $('#longitude').val(results[0].geometry.location.lng());
            $('.map_div').css('display', 'block');
            initialize(results[0].geometry.location.lat(), results[0].geometry.location.lng());
    } else {
    var n = noty({
    text: 'Geocode was not successful for the following reason: ' + status,
            type: 'warning',
            dismissQueue: true,
            layout: 'topCenter',
            closeWith: ['click'],
            theme: 'relax',
            maxVisible: 10,
            timeout: 3000,
            animation: {
            open: 'animated flipInX',
                    close: 'animated flipOutX',
                    easing: 'swing',
                    speed: 500
            }
    });
    }
    });
    }
    else{
    var n = noty({
    text: "Please Enter Your Address",
            type: 'warning',
            dismissQueue: true,
            layout: 'topCenter',
            closeWith: ['click'],
            theme: 'relax',
            maxVisible: 10,
            timeout: 3000,
            animation: {
            open: 'animated flipInX',
                    close: 'animated flipOutX',
                    easing: 'swing',
                    speed: 500
            }
    });
    }

    }
    function setAllMap(map) {
    for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
    }
    }


</script>
@endsection
