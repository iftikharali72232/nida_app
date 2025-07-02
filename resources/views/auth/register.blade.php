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
    position: fixed;
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

        <!-- Right Section with Registration Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="login-form-area px-md-5">

                <!-- Title -->
                <!-- <h5 class="text-center">{{ trans('lang.register') }}</h5> -->
                <!-- <p class="text-center text-muted small">{{ trans('lang.create_account') }}</p> -->

                <!-- Registration Form -->
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <!-- Name Input -->
                    <div class="mb-3">
                        <!-- <label for="name" class="form-label">{{ trans('lang.name') }}</label> -->
                        <input id="name" type="text" placeholder="{{ trans('lang.name') }}" class="form-control @error('name') is-invalid @enderror" 
                               name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                        <!-- <label for="email" class="form-label">{{ trans('lang.email_address') }}</label> -->
                        <input id="email" type="email" placeholder="{{ trans('lang.email_address') }}" class="form-control @error('email') is-invalid @enderror" 
                               name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="mb-3">
                        <!-- <label for="password" class="form-label">{{ trans('lang.password') }}</label> -->
                        <input id="password" type="password" placeholder="{{ trans('lang.password') }}" class="form-control @error('password') is-invalid @enderror" 
                               name="password" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="mb-3">
                        <!-- <label for="password-confirm" class="form-label">{{ trans('lang.confirm_password') }}</label> -->
                        <input id="password-confirm" placeholder="{{ trans('lang.confirm_password') }}" type="password" class="form-control" 
                               name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <!-- Submit Button -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-muted-blue">{{ trans('lang.register') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
