@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">
        
        <div class="page-title">
            <div class="title_left">
                <h3>Organization</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">


                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add Organization</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('organization/'.$user_id)}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                         </div>
                        <div class="clearfix"></div>
                    </div>

                    <br>
                     <form id="org-form" enctype="multipart/form-data" method="POST" action="{{URL::to('organization/store')}}" class="form-horizontal form-label-left">
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">
                                <div class="form-group">
                                    <label for="fullname">Business Logo:</label>
                                    <input  class="form-control" name="organization_logo" type="file">
                                    <ul class="parsley-errors-list">{{ $errors->organization->first('organization_logo') }}</ul>

                                </div>
                                 <div class="form-group">
                                
                                 <label for="email">Name* :</label>
                                  
                                   <input  class="form-control" value="{{ old('organization_name') }}" name="organization_name" type="text">
                                <ul class="parsley-errors-list">{{ $errors->organization->first('organization_name') }}</ul>
                                 </div>
                                
                                <div class="form-group">
                                
                                 <label for="email">Email :</label>
                               <input value="{{ old('organization_email') }}" type="text" name="organization_email" class="form-control">
                                <ul class="parsley-errors-list">{{ $errors->organization->first('organization_email') }}</ul>
                                 
                            </div>
                                  <div class="form-group">
                                
                                 <label for="email">Phone Number :</label>
                                <input id="organization_contact" data-inputmask="'mask' : '(999) 999-9999'" type="text" value="{{ old('organization_contact') }}" name="organization_contact" class="form-control">
                                <ul class="parsley-errors-list">{{ $errors->organization->first('organization_contact') }}</ul>
                                 
                            </div>
                              
                                 <div class="form-group">
                                
                                 <label for="email">Website :</label>
                               <input type="text" value="{{ old('organization_website') }}" name="organization_website" class="form-control">
                                <ul class="parsley-errors-list">{{ $errors->organization->first('organization_website') }}</ul>
                                
                            </div>
                                
                              

                            </div>
                        </div>

                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">

                                <div class="form-group">
                                 
                               <label for="fullname">Country :</label>
                                <select class="form-control" id="country" name="country" data-placeholder="Choose a Country..." style="width:100%;" tabindex="1">
                                    <option></option>
                                    @foreach($country as $count_val)
                                    <option <?php echo (old('country') == $count_val->id) ? "selected" : ''; ?> value="{{ $count_val->id }}">{{ $count_val->name }}</option>
                                    @endforeach;
                                </select>
                                <ul class="parsley-errors-list">{{ $errors->organization->first('country') }}</ul>
                              
                            
                            </div>
                                <div class="form-group">
                                
                                 <label for="email">State :</label>
                                 
                                <select class="form-control select_input" name="state" id="state" data-placeholder="Choose a state...">

                                </select>
                                <ul class="parsley-errors-list">{{ $errors->organization->first('state') }}</ul>
                                    </div>

                                <div class="form-group">
                                 <label for="email">City :</label>
                                 
                                 <select class="form-control select_input" name="city" id="city" data-placeholder="Choose a city...">

                                </select>
                                <ul class="parsley-errors-list">{{ $errors->organization->first('city') }}</ul>
                                
                            </div>
                                
                                
                                <div class="form-group">
                                
                                <label for="email"> Street Address :</label>
                                <textarea class="form-control" name="organization_location" id="address">{{ old('organization_location') }}</textarea>
                                <ul class="parsley-errors-list">{{ $errors->organization->first('organization_location') }}</ul>
                               
                            </div>
                                
                                 
                           
                            
                          
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="user_id" value="{{ $user_id }}">
                               
                         </div>
                        </div>
                         
                         <div class="clearfix"></div> </br>
                        <div class="col-md-12 col-xs-12">
                            <label class="control-label">About the Organization* :</label></br></br>
                          
                                  
                                   <textarea class="ckeditor form-control" name="organization_description" rows="6">{{ old('organization_description') }}</textarea>
                                <ul class="parsley-errors-list">{{ $errors->organization->first('organization_description') }}</ul>
                               
                        </div>
                        
                     
                        
                        
                        <div class="clearfix"></div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-10  pull-left">
                                   <a href="{{URL::to('organization/'.$user_id)}}" class="btn btn-primary">Cancel</a>
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
    #address{
        height: 138px;
    }    
    
</style>
<script>
        $(document).ready(function () {
        
                
       
        $("#organization_contact").inputmask();        
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
        
        
        $(document).on('click','.add_map',function(){
         $('.map_div').css('display','block');   
         codeAddress();    
          
        });
        
        
        // add validation
        
          // add validation
        
         $("#org-form").validate({
                        ignore:[],
			rules: {
				organization_name: "required",
				organization_description: {
                                    required: function() 
                                    {
                                        var val = CKEDITOR.instances.organization_description.getData();
                                        console.log(val);
                                        if(val==''){
                                          return true; 
                                        }else{
                                         return false;   
                                        }
                                  
                                    }
                                }
                          },
			messages: {
				organization_name: "Please enter organization name",
				organization_description:"please enter organization description"
			},
                          errorPlacement: function(error, element) 
                {
                    if (element.attr("name") == "organization_description") 
                   {
                    error.insertAfter("#cke_organization_description");
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
                        } ,complete:function(){
                            var old_state = ('{{old('state')}}');
                            if(old_state==''){
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
                        },complete:function(){
                            var old_city = ('{{old('city')}}');
                            if(old_city != ''){
                             $(city).val(old_city);
                             $(city).trigger("chosen:updated");
                            
                           }
                            
                        }
                });
                
        }

       
     
        


    </script>


@endsection