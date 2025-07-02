@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-5">
            <h1>Edit Team</h1>
            <form action="{{ route('teams.update', $team->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group mb-3">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $team->name }}" required>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control">{{ $team->description }}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $team->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="">
                    <button type="submit" class="cssbuttons-io">
                        <span>
                            <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                            Update
                        </span>
                    </button>
                </div>
                <!-- <button type="submit" class="btn btn-success">Update</button> -->
            </form>
        </div>
    </div>
@endsection
