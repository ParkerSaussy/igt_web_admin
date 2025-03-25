<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>ItsGoTime Admin</title>
    <?php  $imageUrl  = config('global.local_image_url'); ?>
    
    <link rel="icon" type="image/x-icon" href="<?php echo $imageUrl?>favicon.png">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" href="apple-icon.png">
    <link rel="shortcut icon" href="favicon.ico">


    <link rel="stylesheet" href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/themify-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/selectFX/css/cs-skin-elastic.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>



</head>

<body class="bg-dark">


    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-form">
                    <div class="login-logo">
                        <a href="#">
                            <?php  $imageUrl  = config('global.local_image_url'); ?>
                            <img class="align-content" src="<?php echo $imageUrl?>logo.png" alt="" style="max-width: 160px">
                           
                        </a>
                    </div>
                         @if ($message = Session::get('failed'))
                                    <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show">
                                         {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    @endif
                                    @if ($message = Session::get('message'))
                                   <div class="sufee-alert alert with-close alert-success alert-dismissible fade show">
                                         {{ $message }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    @endif
                    <form method="POST" action="{{URL('/resetpostPassword')}}">
                        @csrf
                         <input type="hidden" name="token" value="{{ $token }}">
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="newpassword" class="form-control @error('newpassword') is-invalid @enderror" value="{{old('newpassword')}}" autocomplete="off" autofocus>
                            @error('newpassword')<span style="color:red;">{{$message}}</span>
                            @enderror<br>
                        </div>
                          <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirmpassword" class="form-control @error('confirmpassword') is-invalid @enderror" value="{{old('confirmpassword')}}" autocomplete="off" autofocus>
                            @error('confirmpassword')<span style="color:red;">{{$message}}</span>
                            @enderror<br>
                        </div>
                            <button type="submit" class="btn btn-primary btn-flat m-b-15">Submit</button>

                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('vendors/popper.js/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>



</body>

</html>