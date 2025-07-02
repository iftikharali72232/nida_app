@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card p-5">
        <h2>Create Service Order</h2>

        <!-- Display Success Message -->
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('service_orders.create') }}" method="GET">
            @csrf
            <!-- user Dropdown -->
            <div class="form-group">
                <label for="customer_id">Select User</label>
                <select id="customer_id" name="customer_id" class="form-control mb-3" required>
                    <option value="">Select a User</option>
                    @foreach($users as $name => $id)
                        <option value="{{ $id }}" {{ request('customer_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Service Dropdown -->
            <div class="form-group">
                <label for="service_dropdown">Select Service</label>
                <select id="service_dropdown" name="service_id" class="form-control mb-3" onchange="this.form.submit()">
                    <option value="">Select a Service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->service_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>

        @if(request('service_id') && isset($serviceData))
            <form action="{{ route('service_orders.store') }}" method="POST">
                @csrf
            <div class="row">
                <div class="col-12">
                <!-- Dynamic Form Fields -->
                <div id="dynamic_form">
                    <div class="row">
                    @foreach($variables as $variable)
                        @if($variable['type'] === 'dropdown')
                            <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $variable['label'] }}</label>
                                <select name="variables[{{ $variable['label'] }}]" class="form-control mb-3">
                                    @foreach(explode(',', $variable['dropdown_values']) as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div>
                        @elseif($variable['type'] === 'text')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $variable['label'] }}</label>
                                <input type="text" name="variables[{{ $variable['label'] }}]" class="form-control mb-3" />
                            </div>
                            </div>
                        @elseif($variable['type'] === 'date')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $variable['label'] }}</label>
                                <input type="date" name="variables[{{ $variable['label'] }}]" class="form-control mb-3" />
                            </div>
                        </div>
                        @elseif($variable['type'] === 'checkbox')
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ $variable['label'] }}</label>
                                <input type="checkbox" name="variables[{{ $variable['label'] }}]" value="1" class="form-check-input mb-3" />
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                </div>
                
                </div>

                <!-- Service Cost, Tax, Discount -->
                 <input type="hidden" name="service_id" value="{{$serviceData->id}}">
                 <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                 <div class="col-md-6">
                <div class="form-group">
                    <label for="service_cost">Service Cost</label>
                    <input type="text" id="service_cost" name="service_cost" class="form-control mb-3" value="{{ $serviceData->actual_cost }}"  />
                </div>
                </div>

                <div class="col-md-6">
                <div class="form-group">
                    <label for="service_date">Service Date</label>
                    <input type="date" id="service_date" name="service_date" class="form-control mb-3" value="{{ date('Y-m-d'); }}"  />
                </div>
                </div>

                <div class="col-md-6">
                <div class="form-group">
                    <label for="tax">Tax</label>
                    <input type="text" id="tax" name="tax" class="form-control mb-3" value="{{ $serviceData->tax }}"  />
                </div>
                </div>

                <div class="col-md-6">
                <div class="form-group">
                    <label for="discount">Discount</label>
                    <input type="text" id="discount" name="discount" class="form-control mb-3" value="{{ $serviceData->discount }}"  />
                </div>
                </div>
            </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <!-- <button type="submit" class="btn btn-primary">Create Order</button> -->
                    <button type="submit" class="cssbuttons-io">
                        <span>
                            <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                            Create Order
                        </span>
                    </button>
                </div>
            </form>
        @endif
    </div>
    </div>
@endsection
