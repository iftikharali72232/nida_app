@extends('layouts.app')


@section('content')
<div class="pagetitle">
  <h1>{{trans('lang.user_list')}}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
      <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
      <li class="breadcrumb-item active">{{trans('lang.elements')}}</li>
    </ol>
  </nav>
</div>
<section class="section">
  <div class="row">
    <div class="col-lg-12">
      <!-- <div class="card">
        <div class="card-body">
          <h5 class="card-title"></h5> -->
          <div class="d-flex align-items-center justify-content-end">
            <a href="{{ route('users.create') }}" class="cssbuttons-io">
              <span>
                <i class="fa-solid fa-plus me-2"></i>
                {{trans('lang.create_new_user')}}
              </span>
            </a>

            <!-- <a class="btn btn-success" href="{{ route('users.create') }}"> {{trans('lang.create_new_user')}}</a> -->
          </div>



          @if ($message = Session::get('success'))
        <div class="alert alert-success">
        <p>{{ $message }}</p>
        </div>
      @endif


          <table class="table pretty-table">
            <tr class="thead">
              <th>{{trans('lang.number')}}</th>
              <th>{{trans('lang.name')}}</th>
              <th>{{trans('lang.email')}}</th>
              <th>{{trans('lang.user_type')}}</th>
              <th>{{trans('lang.status')}}</th>
              <th width="280px">{{trans('lang.action')}}</th>
            </tr>
            @php
        //echo "<pre>";print_r($perPage); exit;
        $page = $_GET['page'] ?? 1;
        $i = ($page * $perPage) - $perPage;
       @endphp
            @foreach ($buyers as $key => $user)
        <tr class="tbody">
          <td class="align-middle">{{ ++$i }}</td>
          <td class="align-middle">{{ $user->name }}</td>
          <td class="align-middle">{{ $user->email }}</td>
          <td class="align-middle">{{$user->user_type == 0 ? "ADMIN" : ($user->user_type == 1 ? "SELLER" : "BUYER")}}</td>
          <td class="align-middle">
          @if($user->status == 0)
        <a class="btn btn-warning text-center"
        href="{{ route('sellers_active', $user->id) }}">{{trans('lang.deactive')}}</a>
      @else
      <a class="btn btn-success text-center"
      href="{{ route('sellers_inactive', $user->id) }}">{{trans('lang.active')}}</a>
    @endif
          </td>
          <td class="align-middle">
          <!-- <a class="btn btn-info" href="{{ route('users.show',$user->id) }}">Show</a> -->
           <div class="d-flex align-items-center gap-2">
            <a href="{{ route('users.edit', $user->id) }}" class="editBtn">
              <svg height="1em" viewBox="0 0 512 512">
                <path
                  d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"
                ></path>
              </svg>
            </a>

            {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'style' => '']) !!}
              <button type="submit" class="bin-button">
              <img src="{{asset('img/trash-open.svg')}}" class="bin-top" alt="">
              <img src="{{asset('img/trash-close.svg')}}" class="bin-bottom" alt="">
              </button>
            {!! Form::close() !!}
           </div>
          

          <!-- <a class="btn btn-primary" href="{{ route('users.edit', $user->id) }}">{{trans('lang.edit')}}</a> -->
          <!-- {!! Form::open(['method' => 'DELETE', 'route' => ['users.destroy', $user->id], 'style' => 'display:inline']) !!}
          {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
          {!! Form::close() !!} -->
          </td>
        </tr>
      @endforeach
          </table>


          {{ $buyers->onEachSide(1)->links('vendor.pagination.default') }}


        <!-- </div>
      </div> -->
    </div>
  </div>
</section>
@endsection