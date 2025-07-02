@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-5">
    <h1>Create Service</h1>
    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Service Name -->
        <div class="mb-3">
            <label for="service_name" class="form-label">Service Name</label>
            <input type="text" class="form-control" id="service_name" name="service_name" required>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <div class="row">
        <!-- Thumbnail -->
            <div class="col-md-6 mb-3">
                <label for="thumbnail" class="form-label">Thumbnail</label>
                <input type="file" class="form-control" id="thumbnail" name="thumbnail">
            </div>

            <!-- Images -->
            <div class="col-md-6 mb-3">
                <label for="images" class="form-label">Images</label>
                <input type="file" class="form-control" id="images" name="images[]" multiple>
            </div>

            <!-- Category -->
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select a Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Estimated Time -->
            <div class="col-md-6 mb-3">
                <label for="estimated_time" class="form-label">Estimated Time (minutes)</label>
                <input type="text" class="form-control" id="estimated_time" name="estimated_time">
            </div>

            <!-- Start Time -->
            <div class="col-md-6 mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="text" class="form-control" id="start_time" name="start_time">
            </div>

            <!-- Service Cost -->
            <div class="col-md-6 mb-3">
                <label for="service_cost" class="form-label">Service Cost Range</label>
                <input type="text" class="form-control" id="service_cost" name="service_cost" step="0.01" required>
            </div>

            <!-- Actual Cost -->
            <div class="col-md-6 mb-3">
                <label for="actual_cost" class="form-label">Actual Cost</label>
                <input type="text" class="form-control" id="actual_cost" name="actual_cost" step="0.01" required>
            </div>
        
        </div>

        <!-- Service Variables -->
        <h5>Service Variables</h5>
        <div id="service-variables-container"></div>
        <button type="button" class="btn btn-success add-variable">Add Variable</button>
        
        <hr>
        
        <!-- Service Phases -->
        <h5 class="mt-3">Service Phases</h5>
        <div id="service-phases-container"></div>
        <button type="button" class="btn btn-success add-phase">Add Phase</button>

        <div class="mt-4">
            <button type="submit" class="cssbuttons-io">
                <span>
                    <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                    Create Service
                </span>
            </button>
        </div>
        <!-- Submit Button -->
        <!-- <button type="submit" class="btn btn-primary mt-3">Create Service</button> -->
    </form>
    </div>
</div>

<!-- JavaScript for Adding Input Fields Dynamically -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    let variableIndex = 1;
    let phaseIndex = 1;

    // Add Service Variables
    document.querySelector('.add-variable').addEventListener('click', function () {
        let container = document.getElementById('service-variables-container');
        let newVariable = `
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
                        <label class="form-label mt-1">Type</label>
                        <div>
                            <input type="radio" class="form-check-input variable-type" name="service_variables[${variableIndex}][type]" value="text"> Text
                            <input type="radio" class="form-check-input variable-type" name="service_variables[${variableIndex}][type]" value="date"> Date
                            <input type="radio" class="form-check-input variable-type dropdown-type" name="service_variables[${variableIndex}][type]" value="dropdown"> Dropdown
                            <input type="radio" class="form-check-input variable-type" name="service_variables[${variableIndex}][type]" value="checkbox"> Checkbox
                        </div>
                        <textarea class="form-control mt-2 d-none dropdown-values" name="service_variables[${variableIndex}][dropdown_values]" placeholder="Enter comma-separated values for dropdown"></textarea>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newVariable);
        variableIndex++;
    });

    // Add Service Phases
    document.querySelector('.add-phase').addEventListener('click', function () {
        let container = document.getElementById('service-phases-container');
        let newPhase = `
            <div class="d-flex align-items-end mb-3">
                <div class="w-100">
                    <label class="form-label">Phase Name</label>
                    <input type="text" class="form-control" name="service_phases[${phaseIndex}]" placeholder="Enter phase name">
                </div>

                <div class="w-auto ms-3">
                    <button type="button" class="btn btn-lg btn-danger rounded remove-phase" title="Remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newPhase);
        phaseIndex++;
    });

    // Remove Variable Section
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-variable')) {
            e.target.closest('.mb-3').remove();
        }
        if (e.target.classList.contains('remove-phase')) {
            e.target.closest('.mb-3').remove();
        }
    });

    // Ensure only one checkbox can be selected at a time within the same variable section
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('variable-type')) {
            let parentDiv = e.target.closest('.mb-3');
            let checkboxes = parentDiv.querySelectorAll('.variable-type');
            checkboxes.forEach(checkbox => {
                if (checkbox !== e.target) {
                    checkbox.checked = false;
                }
            });

            // Show/hide dropdown values textarea if "Dropdown" is selected
            let dropdownTextarea = parentDiv.querySelector('.dropdown-values');
            if (e.target.classList.contains('dropdown-type') && e.target.checked) {
                dropdownTextarea.classList.remove('d-none');
            } else if (dropdownTextarea) {
                dropdownTextarea.classList.add('d-none');
            }
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
