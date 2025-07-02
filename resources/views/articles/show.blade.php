@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4>View Article</h4>
                </div>
                <div class="card-body">
                    <h5>{{ $article->title }}</h5>
                    <p><strong>Service:</strong> {{ $article->service->service_name }}</p>
                    <p>{{ $article->text }}</p>
                    <h6>Gallery Images:</h6>
                    <div class="row">
                        @foreach((is_array($article->gallery_images) ? $article->gallery_images : json_decode($article->gallery_images, true) ?? []) as $image)
                            <div class="col-md-4">
                                <img src="{{ asset('images/' . $image) }}" class="img-fluid rounded">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
