@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">

        <div class="page-title">
            <div class="title_left">
                <h3>Blog</h3>
            </div>
        </div>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">


                <div class="x_panel">
                    <div class="x_title">
                        <h2>Add blog</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('blog')}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <br>
                    <form id="blog-form" method="POST" action="{{URL::to('blog')}}" class="form-horizontal form-label-left" enctype="multipart/form-data">   
                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">
                                <div class="form-group">
                                    <label for="fullname">Blog Title* :</label>
                                    <input id="blog_title" class="form-control" name="blog_title" type="text" value="<?php echo old('blog_title'); ?>"><ul class="parsley-errors-list">{{ $errors->blog->first('blog_title') }}</ul>

                                </div>
                                <div class="form-group">
                                    <label for="fullname">Blog Published Date* :</label>
                                    <input id="published-date" class="form-control" name="published_date" type="text" value="<?php echo (old('published_date') != "") ? old('published_date') : date("d-m-Y",time()); ?>"><ul class="parsley-errors-list">{{ $errors->blog->first('published_date') }}</ul>
                                </div>
                               

                                <div class="form-group">
                                    <label for="last-name">Status <span class="required">*</span>
                                    </label>
                                    <select class="form-control col-md-7 col-xs-12"  name="status">
                                        <option value="active" <?php echo (old('status') == 'active') ? "selected" : "" ?>>Active</option>   
                                        <option value="deactive" <?php echo (old('status') == 'deactive') ? "selected" : "" ?>>Deactive</option>   
                                    </select></div>







                                <input type="hidden" name="_token" value="{{ csrf_token() }}">





                            </div>
                        </div>

                        <div class="col-md-6 col-xs-12">
                            <div class="x_content">

                                
                                </br>
                                
                                
                                 <div class="form-group">
                                    <label for="last-name">Allow Comment :
                                    </label>
                                      <input id="allow_comment" type="checkbox"  checked data-size="mini" name="allow_comment">
                                 </div>
                                </br>


                                <div class="form-group">
                                    <label class="">Blog Category :
                                    </label>
                                    <select name="blog_category[]" id="category" class="form-control select2-multiple" multiple>
                                 
                                        @foreach($category as $val)
                                        <option <?php
                                        if (!empty(old('blog_category'))) {
                                            if (in_array($val->blog_category_name, old('blog_category'))) {
                                                echo "selected";
                                            }
                                        };
                                        ?> value="{{ $val->blog_category_name }}">{{ $val->blog_category_name }}</option>
                                        @endforeach;
                                   
                                    </select>
                                     <ul class="parsley-errors-list">{{ $errors->blog->first('blog_category') }}</ul>
                                </div>
                                <div class="form-group">
                                    <label for="last-name">Featured image<span class="required"> :</span>
                                    </label>

                                    <input id="fullname" class="form-control" name="blog_image" type="file"><ul class="parsley-errors-list">{{ $errors->blog->first('blog_image') }}</ul>
                                </div>
                         </div>
                        </div>
                        
                        <div class="clearfix"></div> </br>
                        <div class="col-md-12 col-xs-12">
                            <label class="control-label">Blog Content* :</label>
                          
                                  
                                    <textarea class="ckeditor form-control" name="blog_content" rows="6">{{ old('blog_content')}}</textarea>
                                    <ul class="parsley-errors-list">{{ $errors->blog->first('blog_content') }}</ul>
                               
                        </div>
                        <div class="clearfix"></div> </br>
                        <div class="col-md-12 col-xs-12">
                            <label class="control-label">Upload Media :</label>
                            <input id="image" name="image" type="file" multiple class="file-loading">

                            <input type="hidden" id="attach_ids" name="blog_media"> 
                            <input type="hidden" id="pre_ids"> 
                        </div>
                        <div class="clearfix"></div>
                        <div class="ln_solid"></div>
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-10  pull-left">
                                <button type="button" id="cancel" data-href="{{URL::to('blog')}}" class="btn btn-primary">Cancel</button>
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
    .file-upload-indicator{
        display:none;
    }   
    .file-preview-thumbnails img {
     height: 160px !important;
     width: 160px !important;
}
    .file-preview-thumbnails video {
     height: 160px !important;
     width: 160px !important;
}
    
</style>
<script>
$(document).ready(function(){
       
        $("#allow_comment").bootstrapSwitch();
        
        $('#published-date').datepicker({
        format: 'dd-mm-yyyy',
              
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
        
        
     // add validation
     
     
      $("#blog-form").validate({
                        ignore:[],
			rules: {
				blog_title: "required",
				blog_content: {
                                    required: function() 
                                    {
                                        var val = CKEDITOR.instances.blog_content.getData();
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
				blog_title: "Please enter blog title",
				blog_content:"please enter blog content"
			},
                          errorPlacement: function(error, element) 
                {
                    if (element.attr("name") == "blog_content") 
                   {
                    error.insertAfter("#cke_blog_content");
                    } else {
                    error.insertAfter(element);
                    }
                }
		});
    
});
</script>
<script>
$(document).ready(function(){
  var arr = new Array();
                // Upload Image 

                $("#attach_ids").val('');
                var base_url = $("#base_url").val();
                $("#image").fileinput({
                uploadUrl: '{{ url('upload_media')}}',
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

        $('body').find("#cancel").click(function () {
        
        var url = $(this).attr('data-href');
        
                var pre_ids = $("#pre_ids").val();
                if (pre_ids != '') {
        $.ajax({
        type: 'POST',
                url: '{{ url('delete_media')}}',
                data: {id: pre_ids},
                success: function (data) {
                },
                complete:function(){
                window.location.href = '{{ url('blog')}}';
                }
        });
        }else{
            window.location.href = '{{ url('blog')}}';
        }
        });    
    
    
});
 function delete_image(ids){

        $.ajax({
        type: 'POST',
                url: '{{ url('delete_media')}}',
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


@endsection