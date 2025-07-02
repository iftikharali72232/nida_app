@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h1>Edit User</h1>
        <form action="{{ route('team_users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mobile">Mobile</label>
                        <input type="text" name="mobile" id="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="team_id">Team</label>
                <select name="team_id" id="team_id" class="form-control" required>
                    <option value="" disabled>Select a Team</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ $user->team_id == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                    <option value="" disabled>Select Status</option>
                    <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>


            <div class="form-group mb-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control">
                <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
            </div>

            <div class="mt-4">
                <button type="submit" class="cssbuttons-io">
                    <span>
                        <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                        Update User
                    </span>
                </button>
            </div>
            <!-- <button type="submit" class="btn btn-primary">Update User</button> -->
        </form>
    </div>
</div>
@endsection
