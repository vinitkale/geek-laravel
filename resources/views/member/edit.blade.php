@extends('layout.layout')
@section('content')

<div class="right_col" role="main">
    <div class="">

        <div class="page-title">
            <div class="title_left">
                <h3>Member</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-xs-12">

                <div class="x_panel">
                    <div class="x_title">
                        <h2>Edit member</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('member')}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                         </div>
                        <div class="clearfix"></div>
                    </div>
                    <form id="edit-member-form" action="{{URL::to('member/update')}}" method="post" enctype="multipart/form-data" >
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">

                                <!-- start form for validation -->
                                <label for="fullname">Profile Image :</label>
                                <input class="form-control" name="profile_image" type="file"><ul class="parsley-errors-list">{{ $errors->member->first('profile_image') }}</ul>
                                
                                <label for="fullname">First Name* :</label>
                                <input  class="form-control" value="{{ $member->first_name }}" name="first_name" type="text"><ul class="parsley-errors-list">{{ $errors->member->first('first_name') }}</ul>

                                <label for="fullname">Last Name* :</label>
                                <input class="form-control" value="{{ $member->last_name }}" name="last_name" type="text"><ul class="parsley-errors-list">{{ $errors->member->first('last_name') }}</ul>

                                   <label for="fullname">Gender* :</label>
                                              <div class="radio">
                                                    <label>
                                                        <input name="gender" <?php echo ($member->gender=='male')? "checked":'' ?> value="male" type="radio"> Male
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input <?php ($member->gender=='female')?"checked":'' ?> value="female" name="gender" type="radio"> Female
                                                    </label>
                                                </div>
                              <ul class="parsley-errors-list">{{ $errors->member->first('gender') }}</ul>
                                <label for="fullname">Email* :</label>
                                <input  class="form-control" value="{{ $member->email }}" name="email" type="text"><ul class="parsley-errors-list">{{ $errors->member->first('email') }}</ul>

                                <label for="fullname">Birth date* :</label>
                                <input id="birth-date" class="form-control" value="{{ date('d-m-Y',strtotime($member->dob)) }}" name="dob" type="text"><ul class="parsley-errors-list">{{ $errors->member->first('dob') }}</ul>

                                <label for="fullname">Birth Place* :</label>
                                <input class="form-control" value="{{ $member->birth_place }}" name="birth_place" type="text"><ul class="parsley-errors-list">{{ $errors->member->first('birth_place') }}</ul>

                               

                               
                                
                                <label class="control-label">About Me</label>
                           <textarea class="ckeditor form-control" name="about_me" rows="6">{{ $member->about_me }}</textarea>

                        </div></div>
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">

                              
                                <label for="fullname">Country* :</label>
                                <select class="form-control" id="country" name="country" data-placeholder="Choose a Country..." style="width:100%;" tabindex="1">
                                    
                                    @foreach($country as $count_val)
                                    <option <?php echo ($member->country == $count_val->id) ? "selected" : ''; ?> value="{{ $count_val->id }}">{{ $count_val->name }}</option>
                                    @endforeach;
                                </select>
                                <ul class="parsley-errors-list">{{ $errors->member->first('country') }}</ul>

                                <label for="email">State* :</label>
                                <select class="form-control select_input" name="state" id="state">

                                </select>
                                <ul class="parsley-errors-list">{{ $errors->member->first('state') }}</ul>

                                <label for="email">City* :</label>
                                <select class="form-control select_input" name="city" id="city">

                                </select>
                                <ul class="parsley-errors-list">{{ $errors->member->first('city') }}</ul>
                               
                                  <label for="fullname">Zip Code* :</label>
                                <input  class="form-control" value="{{ $member->zip_code }}" name="zip_code" type="text"><ul class="parsley-errors-list">{{ $errors->member->first('zip_code') }}</ul>
                               <label for="email">Address* :</label>
                                <textarea class="form-control" name="address">{{ $member->address }}</textarea>
                                <ul class="parsley-errors-list">{{ $errors->member->first('address') }}</ul>
                                <label for="email">Contact Info :</label>
                                <input name="contact_info" id="contactinfo" value="{{ $member->phone }}" class="form-control textfield" type="tel">
                                <ul class="parsley-errors-list">{{ $errors->member->first('contact_info') }}</ul>

                               

                                

                                <label for="email">Interest Topic:</label>
                                <select name="interest_topic[]" id="category" class="form-control select2-multiple" multiple>
                                    <?php
                                    if ($member->favorite_category != '') {
                                        $keyword_val = explode(',', $member->favorite_category);
                                    } else {
                                        $keyword_val = array();
                                    }
                                    ?>
                                 
                                      
                                        @foreach($category as $val)
                                        <option <?php
                                        if (!empty($keyword_val)) {
                                            if (in_array($val->category_name, $keyword_val)) {
                                                echo "selected";
                                            }
                                        };
                                        ?> value="{{ $val->category_name }}">{{ $val->category_name }}</option>
                                        @endforeach;
                                   
                                </select>
                                <ul class="parsley-errors-list">{{ $errors->member->first('interest_topic') }}</ul>



                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="user_id" value="{{ $member->user_id}}">
                                <input type="hidden" name="old_profile" value="{{ $member->profile_image}}">
                                <input type="hidden" name="old_email" value="{{ $member->email}}">
                               





                                <!-- end form for validations -->

                            </div></div>





                        <div class="clearfix"></div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-10  pull-left">
                                <a type="button" id="cancel" href="{{URL::to('member')}}" class="btn btn-primary">Cancel</a>
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>
                </div>










            </div>
        </div>


        @include('layout.footer')
    </div>

    <style>
        textarea.form-control {
            height: 141px;
        }  



    </style>
    <script>
        $(document).ready(function () {
        $('#birth-date').datepicker({
        format: 'dd-mm-yyyy',
                setDate: new Date(),
                autoclose: true
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
        
        
        // edit member
        
        
        $("#edit-member-form").validate({
			rules: {
				first_name: "required",
				last_name: "required",
				username: "required",
				gender: "required",
				email: {
					required: true,
					email: true
				},
				dob: "required",
				birth_place: "required",
				country: "required",
				state: "required",
				zip_code: "required",
				address: "required",
                        },
			messages: {
				first_name: "Please enter first name",
				last_name: "Please enter last name",
				username: "Please enter username",
				gender: "Please select your gender",
				email: {
					required: "please enter email address",
					email: "please enter valid email address"
				},
				dob: "please enter valid DOB",
				birth_place: "please enter birth place",
				country: "please select country",
				state: "please select state",
				zip_code: "please enter zipcode",
				address: "please enter address",
			},
                        
                          errorPlacement: function(error, element) 
                {
                    if (element.attr("name") == "gender") 
                   {
                    error.insertAfter(".female");
                    } 
                  else if (element.attr("name") == "country") 
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
                                
                                var state_id = ('{{$member->state}}');
                                getCity(state_id);
                        } else{
                                state.empty();
                                $(state).trigger("chosen:updated");
                        }
                        }
                        ,complete:function(){
                        $(state).val('{{ $member->state }}');
                                $(state).trigger("chosen:updated");
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
                        },
                        complete:function(){
                                $(city).val('{{ $member->city }}');
                                $(city).trigger("chosen:updated");
                        }
                });
        }
        
        

       

        


    </script>

    @endsection



