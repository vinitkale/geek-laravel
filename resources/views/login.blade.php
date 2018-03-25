<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Geek Meet! | </title>

        <!-- Bootstrap core CSS -->

        <link href="{{ asset("/bower_components/gentelella/production/css/bootstrap.min.css") }}" rel="stylesheet">

        <link href="{{ asset("/bower_components/gentelella/production/fonts/css/font-awesome.min.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/css/animate.min.css") }}" rel="stylesheet">

        <link href="{{ asset("/bower_components/gentelella/production/css/custom.css") }}" rel="stylesheet">
        <link href="{{ asset("/bower_components/gentelella/production/css/icheck/flat/green.css") }}" rel="stylesheet">


        <script src="{{ asset("/bower_components/gentelella/production/js/jquery.min.js") }}"></script>

    </head>

    <body style="background:#003A70;">

        <div class="">
            <a class="hiddenanchor" id="toregister"></a>
            <a class="hiddenanchor" id="tologin"></a>

            <div id="wrapper">
                <div id="login" class="animate form">
                    @if (session('status'))
                    <div class="alert alert-error">
                        {{ session('status') }}
                    </div>
                    @endif
                    @if (session('success_status'))
                    <div class="alert alert-success">
                        {{ session('success_status') }}
                    </div>
                    @endif
                    <section class="login_content">
                        <form action="login" method="post" id="login_form">
                            <h1>Admin Login</h1>
                            <div>
                                <input type="text" value="{{ old('username') }}" name="username" class="form-control" placeholder="Username" required="" />
                                <p class="error">{{ $errors->login->first('username') }}</p>
                            </div>
                            <div>
                                <input value="{{ old('password') }}" type="password" name="password" class="form-control" placeholder="Password" required="" />
                                <p class="error"> {{ $errors->login->first('password') }}</p>
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div>
                                <a class="btn btn-default submit" href="javascript:void(0)">Log in</a>
                            </div>
                            <div class="clearfix"></div>
                            <div class="separator">
                                <div class="clearfix"></div>
                                <br />
                                <div>
                                    <img src="{{ asset("/bower_components/gentelella/production/images/logo.png") }}" style="margin-right: 40px;">

                                </div>
                            </div>
                        </form>
                        <!-- form -->
                    </section>
                    <!-- content -->
                </div>

            </div>
        </div>

    </body>

</html>
<script>
$(document).ready(function () {

    $('.submit').click(function () {
        $('#login_form').submit();

    });

});

</script>