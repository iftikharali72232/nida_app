@extends('layouts.app')


@section('content')
<div class="card p-5">
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>{{trans('lang.category_edit')}}</h2>
        </div>
        <div class="pull-right d-none">
            <a class="btn btn-primary" href="{{ route('category.index') }}"> {{trans('lang.back')}}</a>
        </div>
    </div>
</div>


@if ($message = Session::get('error'))
          <div class="alert alert-danger">
            <p>{{ $message }}</p>
          </div>
          @endif


{!! Form::model($category, ['enctype'=>'multipart/form-data','method' => 'PATCH','route' => ['category.update', $category->id]]) !!}
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
    <div class="col-xs-12 col-sm-12 col-md-12">
        <!-- <button type="submit" class="btn btn-primary">{{trans('lang.submit')}}</button> -->
        <div class="mt-2">
            <button type="submit" class="cssbuttons-io">
                <span>
                    <i class="fa-regular fa-floppy-disk {{ app()->getLocale() == 'en' ? 'me-2' : 'ms-2' }}"></i>
                    {{trans('lang.submit')}}
                </span>
            </button>
        </div>
    </div>
</div>
{!! Form::close() !!}

        </div></section></div>
</div>
@endsection