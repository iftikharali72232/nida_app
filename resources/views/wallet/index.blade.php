@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-sm-start text-center">{{trans('lang.user_list')}}</h2>
            <nav>
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
                <li class="breadcrumb-item">{{trans('lang.forms')}}</li>
                <li class="breadcrumb-item active">{{trans('lang.elements')}}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="table-responsive">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif


        <table class="table pretty-table mt-3">
            <thead class="thead">
                <tr>
                    <th>{{trans('lang.number')}}</th>
                    <th>{{trans('lang.name')}}</th>
                    <th>{{trans('lang.points')}}</th>
                    <th>{{trans('lang.status')}}</th>
                    <th width="280px">{{trans('lang.action')}}</th>
                </tr>
            </thead>

            @php
                //echo "<pre>";print_r($wallets ); exit;
                $page = $_GET['page'] ?? 1;
                $i = ($page*$perPage)-$perPage;
            @endphp

            <tbody>
                @foreach ($wallets as $wallet)
                    <tr class="tbody">
                        <td class="align-middle">{{ $wallets->firstItem() + $loop->index }}</td>
                        <td class="align-middle">{{ $wallet->user->name ?? 'N/A' }}</td>
                        <td class="align-middle">{{ $wallet->amount }}</td>
                        <td class="align-middle">
                            @if($wallet->user->status ?? 0 == 0)
                                <a class="btn btn-warning text-center">{{ trans('lang.deactive') }}</a>
                            @else
                                <a class="btn btn-success text-center">{{ trans('lang.active') }}</a>
                            @endif
                        </td>
                        <td class="align-middle" width="30%">
                            <a href="{{ route('wallet.edit', $wallet->id) }}" class="">
                                <button type="button" class="cssbuttons-io " href="{{ route('wallet.edit', $wallet->id) }}">
                                    <span>
                                        {{ trans('lang.update_wallet') }}
                                    </span>
                                </button>
                            </a>
                            <a href="{{ route('wallet.history', $wallet->id) }}" class="px-3">
                                <button type="button" class="cssbuttons-io" href="{{ route('wallet.history', $wallet->id) }}">
                                    <span>
                                        {{ trans('lang.history') }}
                                    </span>
                                </button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $wallets->onEachSide(1)->links('vendor.pagination.default') }}
    </div>
</div>
@endsection