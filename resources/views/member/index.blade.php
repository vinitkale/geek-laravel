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
                    Member

                </h3>
            </div>

        </div>
        <div class="clearfix"></div>


        <div class="row">







            <div class="clearfix"></div>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="x_title">
                        <h2>Member list</h2>
                        <div class="nav navbar-right panel_toolbox">
                            <a class='btn btn-primary btn-sm' href="{{URL::to('member/create')}}">Add Member</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <div class="x_content">



                        <table class="table table-striped responsive-utilities jambo_table bulk_action" id='member_list'>
                            <thead>
                                <tr class="headings">
                                    <th class="column-title">Username </th>
                                    <th class="column-title">First Name</th>
                                    <th class="column-title">Last Name</th>
                                    <th class="column-title">Email</th>
                                     <th class="column-title" style="width:15%">Address</th>
                                      <th class="column-title">Action</th>
                                </tr>
                            </thead>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


    @include('layout.footer')
</div>



@endsection

