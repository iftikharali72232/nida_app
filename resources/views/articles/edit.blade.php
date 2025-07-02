@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h1>Edit Article</h1>
        <form action="{{ route('articles.update', $article->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="{{ $article->title }}" required>
            </div>
            <div class="form-group mb-3">
                <label>Service</label>
                <select name="service_id" class="form-select" required>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ $article->service_id == $service->id ? 'selected' : '' }}>
                            {{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3">
                <label>Text</label>
                <textarea name="text" class="form-control" rows="5" required>{{ $article->text }}</textarea>
            </div>
            <div class="form-group mb-3">
                <label>Gallery Images</label>
                <div class="row mb-3">
                    @foreach((is_array($article->gallery_images) ? $article->gallery_images : json_decode($article->gallery_images, true) ?? []) as $image)
                    <div class="col-4 position-relative mb-2">
                        <img src="{{ asset('images/'.$image) }}" class="img-thumbnail" alt="Image">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                            onclick="deleteImage('{{ $image }}', this)">×</button>
                    </div>
                    @endforeach
                </div>
                <input type="file" name="gallery_images[]" class="form-control" multiple>
                <small>Upload additional images (current images remain unless deleted).</small>
            </div>
            <button type="submit" class="cssbuttons-io">
                <span>
                    <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                    Update Article
                </span>
            </button>
        </form>
    </div>

</div>

<script>
    function deleteImage(imagePath, button) {
        if (confirm('Are you sure you want to delete this image?')) {
            fetch('{{ route("articles.deleteImage") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image: imagePath, id: <?= $article->id ?> })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.closest('.col-4').remove();
                } else {
                    alert('Could not delete the image. Please try again.');
                }
            })
            .catch(error => console.error('Error:', error));
        }
    }
</script>
@endsection
