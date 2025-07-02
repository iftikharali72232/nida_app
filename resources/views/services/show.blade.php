@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Thumbnail Image -->
    <div class="text-center mb-4">
        @if($service->thumbnail)
            <img src="{{ asset('thumbnails/' . $service->thumbnail) }}" alt="Thumbnail" class="img-fluid rounded" style="max-height: 300px;">
        @else
            <p>No Thumbnail Available</p>
        @endif
    </div>

    <!-- Other Images -->
    <div class="d-flex justify-content-center mb-4">
        @if($service->images && count(json_decode($service->images, true)) > 0)
            @foreach(json_decode($service->images, true) as $image)
                <img src="{{ asset('images/' . $image) }}" alt="Service Image" class="img-thumbnail mx-1" style="width: 100px; height: 100px;">
            @endforeach
        @else
            <p>No Additional Images</p>
        @endif
    </div>

    <!-- Service Details -->
    <div class="card p-3">
        <h3>{{ $service->service_name }}</h3>
        <p><strong>Description:</strong> {{ $service->description }}</p>
        <p><strong>Category:</strong> {{ $service->category->name ?? 'N/A' }}</p>
        <p><strong>Estimated Time:</strong> {{ $service->estimated_time }} minutes</p>
        <p><strong>Start Time:</strong> {{ $service->start_time }}</p>
        <p><strong>Service Cost:</strong> {{ $service->service_cost }}</p>
        <p><strong>Actual Cost:</strong> {{ $service->actual_cost }}</p>
    </div>

    <!-- Service Variables -->
    @if($service->variables_json && count(json_decode($service->variables_json, true)) > 0)
        <div class="card mt-4 p-3">
            <h4>Service Variables</h4>
            @foreach(json_decode($service->variables_json, true) as $variable)
                <div class="mb-3">
                    <label class="form-label">{{ $variable['label'] }}</label>
                    @if($variable['type'] === 'text')
                        <input type="text" class="form-control" value="" placeholder="Enter {{ $variable['label'] }}">
                    @elseif($variable['type'] === 'date')
                        <input type="date" class="form-control" value="" placeholder="Enter {{ $variable['label'] }}">
                    @elseif($variable['type'] === 'checkbox')
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checkbox_{{ $loop->index }}">
                            <label class="form-check-label" for="checkbox_{{ $loop->index }}">{{ $variable['label'] }}</label>
                        </div>
                    @elseif($variable['type'] === 'dropdown')
                        <select class="form-select">
                            @foreach(explode(',', $variable['dropdown_values']) as $option)
                                <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="card mt-4 p-3">
            <h4>Service Variables</h4>
            <p>No Variables Defined</p>
        </div>
    @endif

    <!-- Service Phases -->
    @if($service->servicePhases && $service->servicePhases->isNotEmpty())
        <div class="card mt-4 p-3">
            <h4>Service Phases</h4>
            <ul class="list-group">
                @foreach($service->servicePhases as $phase)
                    <li class="list-group-item">{{ $phase->phase }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="card mt-4 p-3">
            <h4>Service Phases</h4>
            <p>No Phases Defined</p>
        </div>
    @endif
</div>
@endsection
