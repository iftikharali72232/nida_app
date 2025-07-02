@extends('layouts.app')


@section('content')
  <section class="section">
<div class="row">
<div class="col-lg-12">
  <div class="card p-5">
      <div class="pagetitle">
        <h1>{{trans('lang.service_create')}}</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
            <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
            <li class="breadcrumb-item active">{{trans('lang.elements')}}</li>
          </ol>
        </nav>
      </div>
          <!-- <h5 class="card-title"></h5> -->
            
@if (count($errors) > 0)
  <div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
       @foreach ($errors->all() as $error)
         <li>{{ $error }}</li>
       @endforeach
    </ul>
  </div>
@endif



{!! Form::open(array('route' => 'category.store','method'=>'POST', 'enctype'=>'multipart/form-data')) !!}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group mb-3">
            <strong>{{trans('lang.name')}}:</strong>
            {!! Form::text('name', null, array('placeholder' => trans('lang.name'),'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group mb-3">
            <strong>{{trans('lang.description')}}:</strong>
            {!! Form::textarea('description', null, array('placeholder' => trans('lang.description'),'class' => 'form-control')) !!}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group mb-3">
            <strong>{{trans('lang.image')}}:</strong>
           <input type="file" class="form-control" name="file" >
        </div>
    </div>

  
    <div class="col-xs-12 col-sm-12 col-md-12"><br>
        <!-- <button type="submit" class="btn btn-primary">{{trans('lang.submit')}}</button> -->
        <button type="submit" class="cssbuttons-io">
            <span>
                <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                {{trans('lang.submit')}}
            </span>
        </button>
    </div>
    
{!! Form::close() !!}


</div>
      </div>
    </div>
</div>
      </section>
@endsection