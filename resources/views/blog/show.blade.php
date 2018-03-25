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
                        <h2 style="font-size:24px !important;">{{ $blog->blog_title}}</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('blog')}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <h5>Published by 
                            <span>
                                <b>{{ ucfirst($blog->username)}}</b>
                            </span> On 
                            <abbr class="published" title=" <?php echo date('l, F dS, Y, h:i A', strtotime($blog->create_date)) ?>">
                                <?php
                                if ($blog->create_date != '') {
                                    $date_obj = date_create($blog->create_date);
                                    echo date_format($date_obj, 'M j, Y');
                                }
                                ?>   </abbr>
                        </h5>	
                    </div> 

                    <div class="clearfix"></div>
                    </br>
                    <div class="col-md-12 col-xs-12 featured-image">
                        <img  class="thumbnails" title="{{ $blog->title}}" src="{{asset('geekmeet/uploads/blog/' . $blog->thumb)}}">           
                    </div>

                    <div class="clearfix"></div>
                    </br>
                    <div class="col-md-12 col-xs-12 content_div">
                        <p><?php echo html_entity_decode($blog->blog_content) ?></p>
                    </div> 

                    <div class="clearfix"></div>
                    </br>
                    <?php if($blog->blog_category!=''){
                         $category = explode(',',$blog->blog_category);
                        ?>
                    <div class="col-md-12 col-xs-12 category_div">
                       
                            <div  class='col-md-2 row'><b> Categories :</b></div>
                            <div class='col-md-10'>
                                <ul class="interest-topic">
                                    <?php foreach ($category as $val) { ?>
                                        <li style="float: left; font-size: 15px;">{{ $val }}<b class="dash">&nbsp;&nbsp;-&nbsp;&nbsp;   </b></li>
                                    <?php } ?>
                                </ul></div> </div>
                 
                    <?php } ?>




                    <div class="clearfix"></div>
                    </br> </br>
                    <div class="col-md-12 col-xs-12">
                        <label class="control-label">Upload Media</label>
                        <input id="image" name="image" type="file" multiple class="file-loading">


                        <input type="hidden" id="attach_ids" name="blog_media" value="{{$blog->blog_media}}"> 
                        <input type="hidden" id="pre_ids"> 
                    </div>

                   
                   


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
     .btn.btn-primary.btn-file,.fileinput-remove,.kv-file-remove {
        display: none;
    }       
    .file-preview-thumbnails img {
        height: 200px !important;
        width: 200px !important;
    }
    .file-preview-thumbnails video {
        height: 200px !important;
        width: 200px !important;
    }
    .file-thumb-progress .progress{
        display: none
    }
    .content_div{
        font-size:14px;
    }
    
    .category_div ul>li{
font-size: 12px;
padding:1px;
}
.category_div ul {
    list-style:  none;
    padding: 0px;
}
.interest-topic li:last-child b {
    display: none;
}

.featured-image{
 overflow: hidden;
 height: 450px;
    
}
.featured-image img{
 width: 100%;
}


@media screen and (max-width: 1200px) {
  .featured-image {
    height: 200px;

  }
}

@media screen and (max-width: 500px) {
      .featured-image {
        height: 100px;

  }
}




</style>
<script>
    $(document).ready(function(){

    $("#allow_comment").bootstrapSwitch();
            $('#published-date').datepicker({
    format: 'dd-mm-yyyy',
            startDate: new Date(),
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
          
        
            });</script>
<script>

            $(document).ready(function () {
    var arr = new Array();
            $("#image").fileinput({
    uploadUrl: '{{ url('upload_media')}}',
            showUpload: false,
            overwriteInitial: false,
            showCaption: false,
            initialPreview: [
<?php
if (!empty($blog->images)) {
    foreach ($blog->images as $val) {
        echo "'$val'" . ',';
    }
}
?>

            ],
            initialPreviewConfig: <?php
if ($blog->attachment != '') {
    echo $blog->attachment;
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
    } else{
    window.location.href = '{{ url('blog')}}';
    }
    });
            var pre_ids = $("#pre_ids").val();
            if (pre_ids != '') {
    delete_image(pre_ids);
    }


      
       $("body").find(".file-preview-thumbnails .file-footer-buttons").each(function () {
            
            var url =  '{{ url('blog/download')}}'
          
            $(this).append('<a target="_self" href="'+url+'/'+$(this).find(".kv-file-remove").attr("data-key")+'" title="Download file" class="kv-file-download btn btn-xs btn-default" type="button"><i class="fa fa-download"></i></a>');
       })


    });
            

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