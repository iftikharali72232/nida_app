@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-5">
            <h1>Add Team</h1>
            <form action="{{ route('teams.store') }}" method="POST">
                @csrf
                <div class="form-group mb-3">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"></textarea>
                </div>
                <div class="form-group mb-3">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="cssbuttons-io">
                    <span>
                        <i class="fa-solid fa-plus {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                        Add
                    </span>
                </button>
                <!-- <button type="submit" class="btn btn-success">Add</button> -->
            </form>
        </div>
    </div>
@endsection
