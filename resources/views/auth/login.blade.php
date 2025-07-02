@extends('layouts.app')
@section('content')
<style>
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background: linear-gradient(320deg, #FF6666, #FFA07A);
    height: 100vh;
    overflow: hidden;
}

.login-form-area {
    width: 100%;
}

.login-form-area input {
    padding: 20px 25px;
    border-radius: 3px;
    margin-bottom: 30px;
}

.login-form-area input:focus {
    box-shadow: none;
}

.btn-muted-blue {
    background: #4D6F95;
    padding: 20px 25px;
    border-radius: 3px;
    color: white;
}

.btn-muted-blue:hover {
    background: #3a5c82;
    color: white;
}

.login-illustration-img {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 45%;
}

</style>
<div class="container w-100 vh-100 d-flex justify-content-center flex-column">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center mb-5">
            <img src="{{asset('img/fix_it_logo.png')}}" alt="Fix it Logo" class="img-fluid" style="width: 250px;">
        </div>
    </div>
    
    <div class="row">
        <!-- Left Section with Illustration -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center">
            <div class="login-illustration-img">
                <img src="{{asset('img/construction_illusion.png')}}" alt="Construction Illustration" class="img-fluid">
            </div>
        </div>

        <!-- Right Section with Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="login-form-area px-md-5">

                <!-- Profile Icon -->
                <div class="text-center mb-3">
                    <img src="{{asset('img/user-circle-img.png')}}" alt="User Icon" style="width: 120px; margin-bottom: 20px;">
                </div>

                <!-- Title -->
                <!-- <h5 class="text-center">{{ trans('lang.login_to_account') }}</h5>
                <p class="text-center text-muted small">{{ trans('lang.enter_username_password') }}</p> -->

                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Username Input -->
                    <div class="mb-3">
                        <!-- <label for="email" class="form-label">{{ trans('lang.username') }}</label> -->
                        <input id="email" type="email" placeholder="{{ trans('lang.username') }}" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <!-- <label for="password" class="form-label">{{ trans('lang.password') }}</label> -->
                        <input id="password" placeholder="{{ trans('lang.password') }}" type="password" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <!-- <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            {{ trans('lang.remember_me') }}
                        </label>
                    </div> -->

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-muted-blue">{{ trans('lang.login') }}</button>
                    </div>

                    <!-- Forgot Password -->
                    <!-- @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}" class="small">
                                {{ trans('lang.forgot_password') }}
                            </a>
                        </div>
                    @endif -->
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
