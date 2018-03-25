<html lang="en">

    <head>

        <title>Geek Meet | </title>

        <!-- Bootstrap core CSS -->

        <link href="{{ asset("/bower_components/gentelella/production/css/bootstrap.min.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/fonts/css/font-awesome.min.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/css/animate.min.css") }}" rel="stylesheet">
     
        <link href="{{ asset("/bower_components/gentelella/production/css/icheck/flat/green.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/css/datatablenew/jquery.dataTables.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/css/datatablenew/dataTables.responsive.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/js/bootstrap-timepicker/css/bootstrap-timepicker.min.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/js/bootstrap-datepicker/css/bootstrap-datepicker.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/js/bootstrap-select2/css/select2.min.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/css/chosen.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/js/fileinput/fileinput.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/js/bootstrap-switch/bootstrap-switch.css") }}" rel="stylesheet">
           <link href="{{ asset("/bower_components/gentelella/production/css/custom.css") }}" rel="stylesheet">

        
       
       





    </head>

    <body class="nav-md">
        <div class="container body">
            @include('layout.header')
            @yield('content')

        </div>
    </body>
</html>




