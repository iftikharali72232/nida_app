@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h2>Create User</h2>
        <form action="{{ route('team_users.store') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="mobile">Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group mb-3">
                <label for="team_id">Team</label>
                <select class="form-control" id="team_id" name="team_id" required>
                    <option value="">Select a Team</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="cssbuttons-io">
                <span>
                    <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                    Save
                </span>
            </button>

            <!-- <button type="submit" class="btn btn-primary">Save</button> -->
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
