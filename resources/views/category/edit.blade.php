@extends('layout.layout')
@section('content')
<div class="right_col" role="main">
    <div class="">

        <div class="page-title">
            <div class="title_left">
                <h3>Category</h3>
            </div>
        </div>
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
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">


                <div class="x_panel">
                    <div class="x_title">
                        <h2>edit category</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a href="{{URL::to('category')}}" type="button" class="btn btn-default btn-sm"><i class="fa fa-chevron-circle-left"></i> Back</a>
                         </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <br>
                        <form method="POST" action="{{URL::to('category/'.$category->category_id)}}" class="form-horizontal form-label-left">

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="first-name">Category Name <span class="required">*</span>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <input id="category-name" name="category_name" value="{{ $category->category_name }}" class="form-control col-md-7 col-xs-12" type="text"><ul class="parsley-errors-list">{{ $errors->category->first('category_name') }}</ul>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="last-name">Catgeory Status <span class="required">*</span>
                                </label>
                                <div class="col-md-2 col-sm-2 col-xs-4">
                                    <select class="form-control col-md-7 col-xs-12" id="category-status" name="category_status">
                                        <option value="1" <?php echo ($category->category_status == 1) ? "selected" : "" ?>>Active</option>   
                                        <option value="0" <?php echo ($category->category_status == 0) ? "selected" : "" ?>>Deactive</option>   
                                    </select>
                                    <ul class="parsley-errors-list">{{ $errors->category->first('category_status') }}</ul>
                                </div>
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="category_id" value="{{ $category->category_id }}">
                            <input type="hidden" name="old_category_name" value="{{ $category->category_name }}">




                            <div class="ln_solid"></div>
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                    <a href="{{URL::to('category')}}" class="btn btn-primary">Cancel</a>
                                    <button type="submit" class="btn btn-success">Update</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('layout.footer')
</div>



@endsection