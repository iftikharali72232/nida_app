@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-sm-start text-center">Service Orders</h2>

    <!-- Filter form -->
    <form action="{{ route('service_orders.index') }}" method="GET" class="mb-3">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label for="status" class="form-label">Filter by Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Pending</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Processing</option>
                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Complete</option>
                    <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>Cancelled</option>
                    <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table pretty-table">
            <thead class="thead">
                <tr>
                    <th>ID</th>
                    <th>Service Name</th>
                    <th>Customer Name</th>
                    <th>Service Cost</th>
                    <th>Service Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($serviceOrders as $order)
                    <tr class="tbody">
                        <td class="align-middle">{{ $order->id }}</td>
                        <td class="align-middle">{{ $order->service_name }}</td>
                        <td class="align-middle">{{ $order->customer_name }}</td>
                        <td class="align-middle">{{ $order->service_cost }}</td>
                        <td class="align-middle">{{ $order->service_date }}</td>
                        <td class="align-middle">
                            @switch($order->status)
                                @case(0)
                                    Pending
                                    @break
                                @case(1)
                                    Processing
                                    @break
                                @case(2)
                                    Complete
                                    @break
                                @case(3)
                                    Cancelled
                                    @break
                                @case(4)
                                    Deleted
                                    @break
                                @default
                                    Unknown
                            @endswitch
                        </td>
                        <td class="align-middle">
                            <a href="{{ route('service_orders.show', $order->id) }}" class="view-button">
                                <!-- SVG icons omitted for brevity -->
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $serviceOrders->appends(request()->query())->onEachSide(1)->links('vendor.pagination.default') }}
    </div>
</div>
@endsection
