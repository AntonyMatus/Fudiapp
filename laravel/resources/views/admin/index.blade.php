<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" name="viewport">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<title>@lang('admin.title')</title>
<link rel="icon" type="image/x-icon" href="{{ Asset('assets/img/logo.png') }}"/>
<link rel="icon" href="{{ Asset('assets/img/logo.png') }}" type="image/png" sizes="16x16">
<link rel="stylesheet" href="{{ Asset('assets/vendor/pace/pace.css') }}">
<script src="{{ Asset('assets/vendor/pace/pace.min.js') }}"></script>


<link rel="stylesheet" type="text/css" href="{{ Asset('assets/fonts/materialdesignicons/materialdesignicons.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ Asset('assets/css/atmos.min.css') }}">

</head>
<body class="jumbo-page">

<main class="admin-main  ">
    <div class="container-fluid">
        <div class="row ">
            <div class="col-lg-12 bg-white">
                <div class="row align-items-center m-h-100">
                    <div class="mx-auto col-md-3">
                        <div class="p-b-20 text-center">
                            <p>
                                <img src="{{ Asset('assets/img/login.png') }}" width="20%" alt="">

                            </p>
                            
                        </div>
                        <h3 class="text-center p-b-20 fw-400">@lang('admin.title')</h3>

                        @if(Session::has('error'))

                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>ERROR : </strong> {{ Session::get('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        </div>

                        @endif

                        @if(Session::has('message'))

                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>SUCCESS : </strong> {{ Session::get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        </div>

                        @endif

                        <form class="needs-validation" action="{{ $form_url }}" method="post">

                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-row">
                                <div class="form-group floating-label col-md-12">
                                    <label>Username</label>
                                    <input type="text" required class="form-control" placeholder="Username" name="username">
                                </div>
                                <div class="form-group floating-label col-md-12">
                                    <label>Password</label>
                                    <input type="password" required class="form-control "  placeholder="Password" name="password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>

                        </form>
                       
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</main>



</body>
</html>