@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h2>Edit Service Offer</h2>
        <form action="{{ route('service_offers.update', $serviceOffer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
            <div class="col-md-6 mb-3">
                <label for="service_id" class="form-label">Service</label>
                <select class="form-control" id="service_id" name="service_id" required>
                    <option value="" disabled>Select a service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ $serviceOffer->service_id == $service->id ? 'selected' : '' }}>
                            {{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" class="form-control mt-2" id="image" name="image">
                @if ($serviceOffer->image)
                    <div class="position-relative">
                        <img src="{{ asset('images/' . $serviceOffer->image) }}" alt="Offer Image" width="100" class="mt-2">
                        <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 0; right: 0;"
                            onclick="deleteImage()">X</button>
                    </div>
                @endif
            </div>
            <div class="col-md-6 mb-3">
                <label for="discount" class="form-label">Discount</label>
                <input type="text" class="form-control" id="discount" name="discount" value="{{ $serviceOffer->discount }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="1" {{ $serviceOffer->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$serviceOffer->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-12">
                <button type="submit" class="cssbuttons-io">
                    <span>
                        <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                        Update
                    </span>
                </button>
            </div>
            <!-- <button type="submit" class="btn btn-primary">Update</button> -->
            </div>
        </form>
    </div>
</div>

<script>
    function deleteImage() {
        if (confirm('Are you sure you want to delete the current image?')) {
            fetch('{{ route('service_offers.delete_image', $serviceOffer->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (response.ok) {
                    alert('Image deleted successfully.');
                    location.reload(); // Reload the page to update the UI
                } else {
                    alert('Failed to delete the image.');
                }
            });
        }
    }
</script>
@endsection
