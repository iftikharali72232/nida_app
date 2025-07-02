@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Service Order Details</h2>
    
    <!-- Service Order Details -->
    <div class="card mb-4 shadow-sm">
    <div class="card-header p-3 bg-dark-main text-white">
        <h5 class="mb-0">Order Details</h5>
    </div>
    <div class="card-body px-3 py-4">
        <p><strong>Customer Name:</strong> {{ $serviceOrder->customer->name ?? 'N/A' }}</p>
        <p><strong>Customer Mobile:</strong> {{ $serviceOrder->customer->mobile ?? 'N/A' }}</p>
        <p><strong>Customer Email:</strong> {{ $serviceOrder->customer->email ?? 'N/A' }}</p>
        <p><strong>Service Name:</strong> {{ $serviceOrder->service->service_name ?? 'N/A' }}</p>
        <p><strong>Service Cost:</strong> {{ $serviceOrder->service_cost }}</p>
        <p><strong>Service Date:</strong> {{ $serviceOrder->service_date }}</p>

        <p><strong>Status:</strong></p>
        <form method="POST" action="{{ route('service-order.update-status', $serviceOrder->id) }}">
            @csrf
            @method('PATCH')
            <div class="input-group mb-3">
                <select class="form-select" name="status" id="status">
                    @php
                        $statuses = [
                            0 => 'Pending',
                            1 => 'Processing',
                            2 => 'Completed',
                            3 => 'Cancelled',
                            4 => 'Deleted',
                        ];
                    @endphp
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ $serviceOrder->status == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </div>
        </form>

        @if ($activeOffer)
            <p><strong>Active Offer:</strong> {{ $activeOffer->discount }} Off</p>
        @else
            <p><strong>Active Offer:</strong> No active offers</p>
        @endif

        <div class="mb-3">
            <label class="form-label">Teams</label>
            <select class="form-select" id="team" onchange="team_users();" {{ $serviceOrder->team_id > 0 && $serviceOrder->team_user_id > 0 ? '' : 'disabled' }}>
                <option value="0" {{ $serviceOrder->team_id == 0 ? 'selected' : '' }}>SELECT</option>
                @foreach($teams as $team)
                    <option value="{{ $team->id }}" {{ $serviceOrder->team_id == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3" id="team_users">
            @if ($serviceOrder->team_id > 0 && $serviceOrder->team_user_id > 0)
                <label class="form-label">Select a User</label>
                <select class="form-select" name="user_id" id="user_id">
                    @foreach($users as $user) {{-- Pass users associated with the selected team --}}
                        <option value="{{ $user->id }}" {{ $serviceOrder->team_user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        @if (!($serviceOrder->team_id > 0 && $serviceOrder->team_user_id > 0))
            <script>
                document.getElementById('team').disabled = false;
                document.getElementById('team_users').innerHTML = '<p class="text-muted">Please select a team to load users.</p>';
            </script>
        @endif

        <button onclick="update();" class="cssbuttons-io">
            <span>Confirm</span>
        </button>
    </div>
</div>


    <!-- Variables Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header p-3 bg-dark-main text-white">
            <h5 class="mb-0">Dynamic Form Variables</h5>
        </div>
        <div class="card-body px-3 py-4">
            @if (!empty($variables))
                <div id="dynamic_form">
                    @foreach ($variables as $variable)
                        @if (!empty($variable['value']))
                            @if($variable['type'] === 'dropdown')
                                <div class="mb-3">
                                    <label class="form-label">{{ $variable['label'] }}</label>
                                    <select class="form-select" disabled>
                                        @foreach(explode(',', $variable['dropdown_values']) as $option)
                                            <option value="{{ $option }}" {{ $option == $variable['value'] ? 'selected' : '' }}>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif($variable['type'] === 'text')
                                <div class="mb-3">
                                    <label class="form-label">{{ $variable['label'] }}</label>
                                    <input type="text" value="{{ $variable['value'] }}" class="form-control" disabled />
                                </div>
                            @elseif($variable['type'] === 'date')
                                <div class="mb-3">
                                    <label class="form-label">{{ $variable['label'] }}</label>
                                    <input type="date" value="{{ $variable['value'] }}" class="form-control" disabled />
                                </div>
                            @elseif($variable['type'] === 'checkbox')
                                <div class="mb-3">
                                    <label class="form-label">{{ $variable['label'] }}</label>
                                    <input type="checkbox" {{ $variable['value'] ? 'checked' : '' }} disabled />
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            @else
                <p class="text-muted">No variables available.</p>
            @endif
        </div>
    </div>

    <!-- Service Phases -->
<!-- Service Phases -->
<div class="card shadow-sm">
    <div class="card-header p-3 bg-dark-main text-white">
        <h5 class="mb-0">Service Phases</h5>
    </div>
    <div class="card-body px-3 py-4">
        @if (!empty($phases) && $phases->isNotEmpty())
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Phase</th>
                        <th>Response Description</th>
                        <th>Images</th>
                        <th>Audios</th>
                        <th>Videos</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($phases as $phase)
                        <tr>
                            <td>{{ $phase->phase }}</td>
                            <td>
                                @if ($phase->response)
                                    {{ $phase->response->description }}
                                @else
                                    <span class="text-muted">No Response</span>
                                @endif
                            </td>
                            <td>
                                @if ($phase->response && !empty($phase->response->images))
                                    <ul>
                                        @foreach ($phase->response->images as $image)
                                            <li><a href="{{ asset($image) }}" target="_blank">View Image</a></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No Images</span>
                                @endif
                            </td>
                            <td>
                                @if ($phase->response && !empty($phase->response->audios))
                                    <ul>
                                        @foreach ($phase->response->audios as $audio)
                                            <li><a href="{{ asset($audio) }}" target="_blank">Play Audio</a></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No Audios</span>
                                @endif
                            </td>
                            <td>
                                @if ($phase->response && !empty($phase->response->videos))
                                    <ul>
                                        @foreach ($phase->response->videos as $video)
                                            <li><a href="{{ asset($video) }}" target="_blank">Watch Video</a></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No Videos</span>
                                @endif
                            </td>
                            <td>
                                @if ($phase->response)
                                    <span class="text-muted">{{ $phase->response->status == 1 ? "Approved" : "Unapproved" }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($phase->response)
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $phase->response->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $phase->response->id }}">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatus({{ $phase->response->id }}, 1)">Approve</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item" onclick="updateStatus({{ $phase->response->id }}, 0)">Unapprove</a>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-muted">No Action</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No phases available for this service.</p>
        @endif
    </div>
</div>

<!-- JavaScript for Updating Status -->
<script>
    function updateStatus(orderPhaseId, status) {
        const BASE_URL = "{{ url('/') }}";
        fetch(BASE_URL+`/order-phase/${orderPhaseId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // alert('Status updated successfully!');
                location.reload(); // Reload the page to reflect the change
            } else {
                alert('Failed to update status.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred.');
        });
    }
</script>

</div>
<script>
    function team_users() {
        const teamId = document.getElementById('team').value;

        // Clear previous users
        const usersContainer = document.getElementById('team_users');
        usersContainer.innerHTML = '<p class="text-muted">Loading users...</p>';

        const BASE_URL = "{{ url('/') }}";
        console.log(BASE_URL); // Outputs the base URL

        // Make AJAX request to fetch team users
        fetch(BASE_URL+`/team/${teamId}/users`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(users => {
                if (users.length > 0) {
                    let userHtml = `
                        <label class="form-label">Select a User</label>
                        <select class="form-select" name="user_id" id="user_id">
                            <option selected disabled>SELECT</option>`;
                    users.forEach(user => {
                        userHtml += `<option value="${user.id}">${user.name} (${user.email})</option>`;
                    });
                    userHtml += '</select>';
                    usersContainer.innerHTML = userHtml;
                } else {
                    usersContainer.innerHTML = '<p class="text-muted">No users found for this team.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching team users:', error);
                usersContainer.innerHTML = '<p class="text-danger">Failed to load users. Please try again.</p>';
            });
    }
function update() {
    const teamId = document.getElementById('team').value;
    const userId = document.getElementById('user_id')?.value || "0"; // Default to "0" if user_id is not selected
    const orderId = "{{ $serviceOrder->id }}"; // Get the order ID

    if (teamId === "0" || userId === "0") {
        alert("Please select both a valid team and a user.");
        return;
    }

    const BASE_URL = "{{ url('/') }}";

    // Send the POST request
    fetch(BASE_URL + '/service-order/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}" // Include CSRF token
        },
        body: JSON.stringify({
            order_id: orderId,
            team_id: teamId,
            user_id: userId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to update the order.');
        }
        return response.json();
    })
    .then(data => {
        alert(data.success || 'Order updated successfully!');
        location.reload(); // Reload to reflect changes
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update the order. Please try again.');
    });
}


</script>

@endsection
