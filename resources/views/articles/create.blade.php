@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h1>Create Service</h1>
        
        <form action="{{ route('articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" placeholder="Enter title" required>
            </div>

            <div class="mb-3">
                <label>Service</label>
                <select name="service_id" class="form-select" required>
                    <option value="" selected disabled>Choose Service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label>Text</label>
                <textarea name="text" class="form-control" rows="5" placeholder="Write article content" required></textarea>
            </div>

            <div class="mb-3">
                <label>Gallery Images</label>
                <input type="file" name="gallery_images[]" class="form-control" multiple>
                <small>Upload multiple images.</small>
            </div>

            <button type="submit" class="cssbuttons-io">
                <span>
                    <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                    Create Service
                </span>
            </button>
        </form>
    </div>
</div>
@endsection
