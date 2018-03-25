@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">
        <div class="clearfix"></div>
        <div class="row">
            @if (session('error'))
            <div class="alert alert-warning alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                </button>
                {{  session('error') }}
            </div>

            @endif
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade in" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
                </button>
                {{ session('success') }}
            </div>

            @endif
        </div>
        <div class="page-title">
            <div class="title_left">
                <h3>
                    Blog

                </h3>
            </div>
            <div class="title_right">
                <form action="{{ url('blog')}}" method="">
                            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                                <div class="input-group">
                                    <input class="form-control" name="search" placeholder="Search for..." type="text">
                                    <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">Go!</button>
                          </span>
                                </div>
                            </div>
                </form>
           </div>

        </div>
        <div class="clearfix"></div>


        <div class="row">







            <div class="clearfix"></div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Blog list</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a class='btn btn-primary btn-sm' href="{{URL::to('blog/create')}}">Add Blog</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">
                        
                        @foreach($blog as $val)
                        <div class="col-md-12 blog_div">
                            <div class="col-md-3 col-sm-3 col-xs-12">
                              
                                <div class="avatar-view" title="" data-original-title="Change the avatar">    
                                <?php if($val->thumb!=''){?>
                                <img  class="thumbnails" title="{{ $val->title}}" src="{{asset('geekmeet/uploads/blog/' . $val->thumb)}}">
                                <?php }else{ ?>
                                 <img  class="thumbnails" src="{{asset('geekmeet/uploads/noimage.jpg')}}">    
                               <?php  } ?>
                                </div>
                            </div>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                                <div class="block">
                                    <div class="block_content">
                                      
                                        <div class="row">    
                                        <div class="col-md-11">    
                                        <h2 class="title">
                                            <a href="blog/{{$val->blog_id}}" title="{{ $val->blog_title}}">{{ $val->blog_title}}</a>                                   
                                        </h2>
                                        </div>
                                        <div class="col-md-1">
                                        <?php if($val->status=='active'){?>    
                                       <span class="label label-success">Active</span>
                                        <?php }else{ ?>
                                       <span class="label label-default">Deactive</span>
                                            
                                       <?php  } ?>
                                        </div>
                                        </div>
                                        <div class="byline">Published by 
                                            <span>
                                                {{ ucfirst($val->username)}}
                                            </span> On 
                                            <abbr class="published" title=" <?php echo date('l, F dS, Y, h:i A',strtotime($val->create_date))?>">
                                                <?php
                                                if ($val->create_date != '') {
                                                    $date_obj = date_create($val->create_date);
                                                    echo date_format($date_obj, 'M j, Y');
                                                }
                                                ?>   </abbr>
                                        </div>	


                                        <p class="excerpt">
                                            <?php
                                            $string = strip_tags($val->blog_content);

                                            if (strlen($string)>600) {

                                                // truncate string
                                                $stringCut = substr($string, 0, 600);

                                                // make sure it ends in a word so assassinate doesn't become ass...
                                                $string = substr($stringCut, 0, strrpos($stringCut, ' ')) . '....<a class="moretag" href="blog/'.$val->blog_id.'">Read More</a>';
                                            }
                                            echo $string;
                                            ?>
                                        </p>
                                        <div class="pull-right blog_button" style="display:none">
                                             <a title="view"  href="blog/{{$val->blog_id}}" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-zoom-in"></i> View</a>&nbsp;<a title="edit"  href="blog/{{$val->blog_id}}/edit" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;<a title="delete" href="javascript:void(0)" data-href="' . url('blog/delete/' . {{$val->blog_id}}) . '" class="btn btn-xs btn-danger delete" data-msg="blog"><i class="glyphicon glyphicon-trash"></i> Delete</a>    

                                        </div>
                                    </div>
                                </div>

                            </div>		
                        </div>
                        <div class="clearfix"></div>
                        
                        <div class="ln_solid"></div>
                            
                    @endforeach
                    </div>
                    <div class="pagination_div">
                    {{ $blog->appends(['search' => $search])->links() }}
                    </div>
                </div>
            </div>
        </div>





        @include('layout.footer')
    </div>
</div>
<style>
  
    .moretag{
     font-weight: bold;
    }
    .pagination_div{
     text-align: center;   
    }
   



</style>
<script>
    $(document).ready(function () {
        $(document).on('mouseenter', '.blog_div', function () {
            $(this).find(".blog_button").show();
        }).on('mouseleave', '.blog_div', function () {
            $(this).find(".blog_button").hide();
        });
    });

</script>


@endsection

