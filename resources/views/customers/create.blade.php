@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h2>Create Customer</h2>
        <form action="{{ route('team_users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control mb-3" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="text" class="form-control mb-3" id="mobile" name="mobile" value="{{ old('mobile') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control mb-3" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control mb-3" id="password" name="password" required>
            </div>


            <!-- <button type="submit" class="btn btn-primary">Save</button> -->

            <button type="submit" class="cssbuttons-io">
                <span>
                    <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                    Save
                </span>
            </button>

            <a href="{{ route('team_users.index') }}">
                <button type="button" class="cssbuttons-io">
                    <span>
                        <i class="fa-solid fa-xmark {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                        Cancel
                    </span>
                </button>
            </a>
        </form>
    </div>
</div>
@endsection
