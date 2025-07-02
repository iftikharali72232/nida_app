@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
        <h1>Edit Service</h1>
        <form action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Service Name -->
            <div class="mb-3">
                <label for="service_name" class="form-label">Service Name</label>
                <input type="text" class="form-control" id="service_name" name="service_name" value="{{ $service->service_name }}" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $service->description }}</textarea>
            </div>

            <div class="row">
                <!-- Thumbnail -->
                <div class="col-md-6 mb-3">
                    <label for="thumbnail" class="form-label">Thumbnail</label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                    @if ($service->thumbnail)
                        <div class="mt-2 position-relative d-inline-block">
                            <img src="{{ asset('thumbnails/' . $service->thumbnail) }}" alt="Thumbnail" width="100">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-thumbnail" data-url="{{ route('services.deleteThumbnail', $service->id) }}">×</button>
                        </div>
                    @endif
                </div>

                <!-- Images -->
                <div class="col-md-6 mb-3">
                    <label for="images" class="form-label">Images</label>
                    <input type="file" class="form-control" id="images" name="images[]" multiple>
                    @if ($service->images)
                        @foreach (json_decode($service->images) as $image)
                            <div class="mt-2 position-relative d-inline-block">
                                <img src="{{ asset('images/' . $image) }}" alt="Image" width="100" class="me-2">
                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-image" data-url="{{ route('services.deleteImage', [$service->id, $image]) }}">×</button>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Category -->
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <option value="">Select a Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $category->id == $service->category_id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Estimated Time -->
                <div class="col-md-6 mb-3">
                    <label for="estimated_time" class="form-label">Estimated Time (minutes)</label>
                    <input type="text" class="form-control" id="estimated_time" name="estimated_time" value="{{ $service->estimated_time }}">
                </div>

                <!-- Start Time -->
                <div class="col-md-6 mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="text" class="form-control" id="start_time" name="start_time" value="{{ $service->start_time }}">
                </div>

                <!-- Service Cost -->
                <div class="col-md-6 mb-3">
                    <label for="service_cost" class="form-label">Service Cost Range</label>
                    <input type="text" class="form-control" id="service_cost" name="service_cost" value="{{ $service->service_cost }}" required>
                </div>

                <!-- Actual Cost -->
                <div class="col-md-6 mb-3">
                    <label for="actual_cost" class="form-label">Actual Cost</label>
                    <input type="text" class="form-control" id="actual_cost" name="actual_cost" value="{{ $service->actual_cost }}" required>
                </div>
            </div>

            <!-- Service Variables -->
            <h5>Service Variables</h5>
            <div id="service-variables-container">
                <?php if($service->variables_json != "") { ?>
                @foreach (json_decode($service->variables_json, true) as $index => $variable)
                    <div class="mb-3">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" name="service_variables[{{ $index }}][label]" value="{{ $variable['label'] }}" required>

                        <label class="form-label">Type</label>
                        <div>
                            @foreach (['text', 'date', 'dropdown', 'checkbox'] as $type)
                                <input type="radio" class="form-check-input variable-type {{ $type === 'dropdown' ? 'dropdown-type' : '' }}" 
                                    name="service_variables[{{ $index }}][type]" 
                                    value="{{ $type }}" 
                                    {{ $variable['type'] === $type ? 'checked' : '' }}> {{ ucfirst($type) }}
                            @endforeach
                        </div>

                        <textarea class="form-control mt-2 {{ $variable['type'] === 'dropdown' ? '' : 'd-none' }} dropdown-values" 
                            name="service_variables[{{ $index }}][dropdown_values]" 
                            placeholder="Enter comma-separated values for dropdown">{{ implode(',', (array)$variable['dropdown_values']) }}</textarea>

                        <button type="button" class="btn btn-danger mt-2 remove-variable">Remove</button>
                    </div>
                @endforeach
                <?php } ?>
            </div>
            <button type="button" class="btn btn-success add-variable">Add Variable</button>

            <hr>

            <!-- Service Phases -->
            <h5 class="mt-3">Service Phases</h5>
            <div id="service-phases-container">
                @foreach ($service->servicePhases as $phase)
                    <div class="mb-3 d-flex">
                        <input type="text" class="form-control" name="service_phases[]" value="{{ $phase->phase }}" placeholder="Enter phase">
                        <button type="button" class="btn btn-danger ms-2 remove-phase">-</button>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-success add-phase">+ Add Phase</button>

            <!-- Submit Button -->
            <div class="mt-4">
                <button type="submit" class="cssbuttons-io">
                    <span>
                        <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                        Update Service
                    </span>
                </button>
            </div>
            <!-- <button type="submit" class="btn btn-primary mt-3">Update Service</button> -->
        </form>
    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let variableIndex = {{ count(($service->variables_json != "" ? json_decode($service->variables_json, true) : [])) }};
    let phaseIndex = {{ count($service->servicePhases ?? []) }};

    // Add Service Variable
    document.querySelector('.add-variable').addEventListener('click', function () {
        const container = document.getElementById('service-variables-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="row mb-3">
                <div class="col-md-12 d-flex align-items-center">
                    <div class="w-100">
                        <label class="form-label">Label</label>
                        <input type="text" class="form-control" name="service_variables[${variableIndex}][label]" placeholder="Enter label" required>
                    </div>

                    <div class="w-auto d-flex align-items-end h-100 ms-3">
                        <button type="button" class="btn btn-lg btn-danger rounded remove-variable" title="Remove">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-12 d-flex align-items-center">
                    <div class="mt-3 w-100">
                        <label class="form-label">Type</label>
                        <div>
                            <input type="radio" name="service_variables[${variableIndex}][type]" value="text"> Text
                            <input type="radio" name="service_variables[${variableIndex}][type]" value="date"> Date
                            <input type="radio" name="service_variables[${variableIndex}][type]" value="dropdown"> Dropdown
                            <input type="radio" name="service_variables[${variableIndex}][type]" value="checkbox"> Checkbox
                        </div>
                        <textarea class="form-control mt-2 d-none dropdown-values" name="service_variables[${variableIndex}][dropdown_values]" placeholder="Enter dropdown values"></textarea>
                    </div>
                </div>
            </div>
        `);
        variableIndex++;
    });

    // Add Service Phase
    document.querySelector('.add-phase').addEventListener('click', function () {
        const container = document.getElementById('service-phases-container');
        container.insertAdjacentHTML('beforeend', `
            <div class="d-flex align-items-end mb-3">
                <div class="w-100">
                    <input type="text" class="form-control" name="service_phases[]" placeholder="Enter phase">
                </div>
                
                <div class="w-auto ms-3">
                    <button type="button" class="btn btn-lg btn-danger rounded remove-phase" title="Remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        `);
        phaseIndex++;
    });

    // Remove Variable or Phase
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variable') || e.target.classList.contains('remove-phase')) {
            e.target.closest('.mb-3').remove();
        }
    });

    // Handle Thumbnail and Image Deletion (AJAX)
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-thumbnail') || e.target.classList.contains('delete-image')) {
            const url = e.target.dataset.url;
            fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        e.target.closest('.position-relative').remove();
                    } else {
                        alert('Failed to delete');
                    }
                });
        }
    });
});
document.getElementById('images').addEventListener('change', function (event) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const file = event.target.files[0];
        
        if (file && !allowedTypes.includes(file.type)) {
            alert('Please upload an image file (JPEG, PNG, GIF).');
            event.target.value = ''; // Clear the input
        }
    });
    document.getElementById('thumbnail').addEventListener('change', function (event) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        const file = event.target.files[0];
        
        if (file && !allowedTypes.includes(file.type)) {
            alert('Please upload an image file (JPEG, PNG, GIF).');
            event.target.value = ''; // Clear the input
        }
    });
</script>
@endsection
