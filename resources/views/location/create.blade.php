@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">

        <div class="page-title">
            <div class="title_left">
                <h3>Venue</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">


                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add venue</h2>
                        <div class="nav navbar-right panel_toolbox">
                             <a  href="javascript:void(0)" data-href="{{URL::to('location/'.$user_id)}}" type="button" class="btn btn-default btn-sm cancel"><i class="fa fa-chevron-circle-left"></i> Back</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <br>
                   <form id="venue-form" method="POST" action="{{URL::to('location/store')}}" class="form-horizontal form-label-left">
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">
                                <div class="form-group">
                                    <label for="fullname">Venue Name* :</label>
                                    <input value="{{ old('venue_name') }}" type="text" name="venue_name" class="form-control">
                                    <ul class="parsley-errors-list">{{ $errors->location->first('venue_name') }}</ul>

                                </div>
                                <div class="form-group">
                                    <label for="fullname">Venue Description :</label>
                                     <textarea class="form-control ckeditor" name="venue_description">{{ old('venue_description') }}</textarea>
                                    <ul class="parsley-errors-list">{{ $errors->location->first('venue_description') }}</ul>
                                </div>
                               

                                 <div class="form-group">
                            <label class="control-label">Upload Media :</label>
                            <input id="image" name="image" type="file" multiple class="file-loading">

                            <input type="hidden" id="attach_ids" name="venue_image"> 
                            <input type="hidden" id="pre_ids"> 
                        </div>

                           


                               
                       


                            </div>
                        </div>

                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">
                             <div class="form-group">
                                    <label for="last-name">Country <span class="required">*</span>
                                    </label>
                                      <select class="form-control" id="country" name="country" data-placeholder="Choose a Country..." style="width:100%;" tabindex="1">
                                        <option></option>
                                        @foreach($country as $count_val)
                                        <option <?php echo (old('country') == $count_val->id) ? "selected" : ''; ?> value="{{ $count_val->id }}">{{ $count_val->name }}</option>
                                        @endforeach;
                                    </select>
                                    <ul class="parsley-errors-list">{{ $errors->location->first('country') }}</ul>
                                
                                </div>
                                
                   
                                <div class="form-group">

                                <label for="email" class="control-label">State* :</label>
                             
                                    <select class="form-control select_input" name="state" id="state">

                                    </select>
                                    <ul class="parsley-errors-list">{{ $errors->location->first('state') }}</ul>
                                </div>
                            <div class="form-group">
                                <label for="email" class="control-label">City :</label>
                               
                                    <select class="form-control select_input" name="city" id="city">

                                    </select>
                                    <ul class="parsley-errors-list">{{ $errors->location->first('city') }}</ul>
                          
                            </div>
                                
                                    <div class="form-group">

                                <label for="email" class="control-label"> Street Address* :</label>
                                 <textarea class="form-control" name="address" id="address">{{ old('address') }}</textarea>
                                    <ul class="parsley-errors-list">{{ $errors->location->first('address') }}</ul>
                           
                                <button class="btn btn-success  btn-sm btn-square active add_map" type="button"><span aria-hidden="true" class="fa fa-location-arrow">  Find Location</span>
                                </button>
                            </div>

                              <div class="form-group">

                                <label for="email" class="control-label">Latitude :</label>
                               <input type="text" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}">
                                    <ul class="parsley-errors-list">{{ $errors->location->first('latitude') }}</ul>
                               
                            </div>
                            <div class="form-group">

                                <label for="email" class="control-label">Longitude :</label>
                               <input type="text" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}">
                                    <ul class="parsley-errors-list">{{ $errors->location->first('longitude') }}</ul>
                               
                            </div>
                            <div class="form-group map_div" style="display:none">

                                <label for="email" class="control-label">Map :</label>
                               
                                    <div id="mapCanvas"></div> 
                               
                            </div>
                                
                               


                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="user_id" value="{{ $user_id }}">
                            <input type="hidden" id="zipcode" name="zipcode">
                         </div>
                        </div>
                       
                     
                        <div class="clearfix"></div> </br>
                       
                        <div class="clearfix"></div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-10 pull-left">
                                    <a  href="javascript:void(0)" data-href="{{URL::to('location/'.$user_id)}}" class="btn btn-primary cancel">Cancel</a>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            
                            
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


    @include('layout.footer')
</div>

<style>
    textarea.form-control {
        height: 141px;
    }  
    #mapCanvas {
        width: 500px;
        height: 400px;
        float: left;
    }
    #infoPanel {
        float: left;
        margin-left: 10px;
    }
    #infoPanel div {
        margin-bottom: 5px;
    }
    
    .file-preview-frame {
    padding: 0px !important;
}
 .file-upload-indicator{
        display:none;
    }



</style>
<script>
    $(document).ready(function () {

         var lang = $("#longitude").val();          
        var lat = $("#latitude").val();     
      
        if(lang!='' && lat!=''){
        initialize(lat,lang);
        $('.map_div').css('display','block');
    }


    $("#country").chosen({no_results_text: "Oops, nothing found!"});
            $("#state").chosen({no_results_text: "Oops, nothing found!"});
            $("#city").chosen({no_results_text: "Oops, nothing found!"});
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
            $(document).on('click', '.add_map', function(){

    codeAddress();
    });
    
    
    // add validation
    
      $("#venue-form").validate({
                        ignore:[],
			rules: {
				venue_name: "required",
				country:"required",
				state:"required",
				address:"required"
                          },
			messages: {
				venue_name: "Please enter venue name",
				country:"please select country",
				state:"please select state",
				address:"please select address",
			},
                          errorPlacement: function(error, element) 
                {
                    if (element.attr("name") == "country") 
                   {
                    error.insertAfter("#country_chosen");
                    } 
                 else if (element.attr("name") == "state") 
                   {
                    error.insertAfter("#state_chosen");
                    }
                   
                else {
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
                    var old_state = ('{{old('state')}}');
                            if (old_state == ''){
                    var state_id = ($("#state").val());
                            getCity(state_id);
                    }
                    else{
                    $(state).val(old_state);
                            $(state).trigger("chosen:updated");
                            getCity(old_state);
                    }
                    }
                    });
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
                            $(city).trigger("chosen:updated");
                    });
                    } else{
                    $(city).empty();
                            $(city).trigger("chosen:updated");
                    }
                    }, complete:function(){
            var old_city = ('{{old('city')}}');
                    if (old_city != ''){
            $(city).val(old_city);
                    $(city).trigger("chosen:updated");
            }

            }
            });
    }






</script>

<script>
$(document).ready(function(){
  var arr = new Array();
                // Upload Image 

                $("#attach_ids").val('');
                var base_url = $("#base_url").val();
                $("#image").fileinput({
                uploadUrl: '{{ url('upload_image')}}',
                uploadAsync: true,
                overwriteInitial: false,
                showUpload: false,
                showRemove: true,
                allowedFileExtensions:['mp4','gif', 'png','mp3','jpg','jpeg','mov']
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
                // delete Image

                $(document).on("click", ".kv-file-remove", function ()   {
        $(this).attr("disabled", "disabled");
                var del_id = $(this).parents(".file-preview-frame").attr("response_id");
                delete_image(del_id);
        });
        
        
            
                // delete Image on load

                var pre_ids = $("#pre_ids").val();
                if (pre_ids != '') {
        delete_image(pre_ids);
        }

        $('body').find(".cancel").click(function () {
        
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
                window.location.href = url;
                }
        });
        }else{
            window.location.href = url;
        }
        });    
    
    
});
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
                return 1;
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
</script>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key= AIzaSyClyMSR2AsxJvw4wJRq-yvRcy10p0In4aY&sensor=true_OR_false"></script>
<script type="text/javascript">
            var geocoder = new google.maps.Geocoder();
            var markers = [];
            function geocodePosition(pos) {
            geocoder.geocode({
            latLng: pos
            }, function(responses) {
                console.log(responses);
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
            initialize(results[0].geometry.location.lat(), results[0].geometry.location.lng());
            $('.map_div').css('display', 'block');
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