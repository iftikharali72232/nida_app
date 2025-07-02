@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-sm-start text-center">Service Listing</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table pretty-table table-striped">
            <thead  class="thead">
                <tr>
                    <th>ID</th>
                    <th>Thumbnail</th>
                    <th>Service Name</th>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Service Cost</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($services as $service)
                    <tr class="tbody">
                        <td class="align-middle">{{ $service->id }}</td>
                        <td class="align-middle">
                            @if($service->thumbnail)
                                <img src="{{asset('thumbnails').'/'.$service->thumbnail }}" alt="" width="50" height="50">
                            @else
                                No Image
                            @endif
                        </td>
                        <td class="align-middle">{{ $service->service_name }}</td>
                        <td class="align-middle">{{ $service->category->name ?? 'N/A' }}</td> <!-- Category Name -->
                        <td class="align-middle">{{ $service->description }}</td>
                        <td class="align-middle">*{{ $service->service_cost }}</td>
                        <td class="align-middle">
                            <div class="d-flex align-items-center gap-2">
                                <!-- View Button -->
                                <a href="{{ route('services.show', $service->id) }}" class="view-button">
                                    <div class="eye-filled">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path 
                                            d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            stroke-width="2" 
                                            class="blink" 
                                        />
                                        <circle cx="12" cy="12" r="3" fill="currentColor" />
                                        </svg>
                                    </div>
                                    <div class="eye-empty">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </div>
                                </a>
                                <!-- <a href="{{ route('services.show', $service->id) }}" class="btn btn-sm btn-info">View</a> -->


                                <!-- Edit Button -->
                                <a href="{{ route('services.edit', $service->id) }}" class="editBtn">
                                    <svg height="1em" viewBox="0 0 512 512">
                                        <path
                                        d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"
                                        ></path>
                                    </svg>
                                </a>
                                <!-- <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-warning">Edit</a> -->
                                
                                <!-- Delete Button -->
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bin-button" onclick="return confirm('Are you sure?')">
                                        <img src="{{asset('img/trash-open.svg')}}" class="bin-top" alt="">
                                        <img src="{{asset('img/trash-close.svg')}}" class="bin-bottom" alt="">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $services->links() }}
    </div>
</div>
@endsection
