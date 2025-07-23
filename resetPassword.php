<!DOCTYPE html>
<html lang="en">


<!-- login23:11-->
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <title>KK PATEL ADMIN LOGIN</title>
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <!--[if lt IE 9]>
		<script src="assets/js/html5shiv.min.js"></script>
		<script src="assets/js/respond.min.js"></script>
	<![endif]-->
</head>

<body>
    <div class="main-wrapper account-wrapper">
        <div class="account-page">
			<div class="account-center">
				<div class="account-box">
                    @if($errors->any())
                    @foreach ($errors->all() as $error )
                    <p style="color:red">{{$error}}</p>
                        @endforeach
                        @endif
                
                      @if(Session::has('error'))
                      <p style="color:red">{{ Session::get('error')}}</p>
                      @endif
                      @if(Session::has('success'))
                      <p style="color:red">{{ Session::get('success')}}</p>
                      @endif
                      <form action="{{ route('resetPassword') }}" method="POST">
                        @csrf
						<div class="account-logo">
                            <a href=""><img src="/img/experts/logo.png" alt=""></a>
                        </div>
                        @if($user)
                        <input type="hidden" name="id" value="{{ $user->id }}">
                    @endif
                        <div class="form-group">
                            <label>Enter New password</label>
                            <input type="password" name="password" placeholder="enter new pass" class="form-control">
                            <span style="color:red">
                                @error('password')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password_confirmation" placeholder="enter confirm pass" class="form-control">
                            <span style="color:red">
                                @error('password_confirmation')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary account-btn">Submit</button>
                        </div>
                        <p class="mt-3">
                            <a href="/kklogin123">Login</a>
                        </p>
                      
                    </form>
                </div>
			</div>
        </div>
    </div>
    <script src="{{URL::to('/')}}/assets/js/jquery-3.2.1.min.js"></script>
	<script src="{{URL::to('/')}}/assets/js/popper.min.js"></script>
    <script src="{{URL::to('/')}}/assets/js/bootstrap.min.js"></script>
    <script src="{{URL::to('/')}}/assets/js/app.js"></script>
</body>


<!-- login23:12-->
</html>



