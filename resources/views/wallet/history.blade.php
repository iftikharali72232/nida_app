@extends('layouts.app')

@section('content')
<div class="pagetitle">
  <h1>{{ trans('lang.wallet_history') }}</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ trans('lang.home') }}</a></li>
      <li class="breadcrumb-item active">{{ trans('lang.wallet_history') }}</li>
    </ol>
  </nav>
</div>

<section class="section">
<div class="row mb-3">
  <div class="col-md-12">
    <form method="GET" action="{{ route('wallet.history', $wallet->id) }}" class="form-inline">
      <div class="row">
        <div class="col-md-3">
          <label for="start_date">{{ trans('lang.start_date') }}</label>
          <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
          <label for="end_date">{{ trans('lang.end_date') }}</label>
          <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-3 align-self-end">
          <button type="submit" class="btn btn-primary mt-2">{{ trans('lang.filter') }}</button>
          <a href="{{ route('wallet.history', $wallet->id) }}" class="btn btn-secondary mt-2">{{ trans('lang.reset') }}</a>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="row">
<div class="col-lg-12">
  <div class="card">
      <div class="card-body">
          <h5 class="card-title">{{ trans('lang.wallet_history_of') }} {{ $wallet->user->name ?? 'N/A' }}</h5>

          <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>{{ trans('lang.service_name') }}</th>
                <th>{{ trans('lang.description') }}</th>
                <th>{{ trans('lang.credit') }}</th>
                <th>{{ trans('lang.debit') }}</th>
                <th>{{ trans('lang.date') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($history as $index => $entry)
                <tr>
                  <td>{{ $history->firstItem() + $index }}</td>
                  <td>{{ $entry->service->service_name ?? 'N/A' }}</td>
                  <td>{{ $entry->description }}</td>
                  <td>
                    @if($entry->is_deposite)
                      {{ number_format($entry->amount, 2) }}
                    @endif
                  </td>
                  <td>
                    @if($entry->is_expanse)
                      {{ number_format($entry->amount, 2) }}
                    @endif
                  </td>
                  <td>{{ $entry->created_at->format('d M Y, h:i A') }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="text-right">{{ trans('lang.total') }}</th>
                <th>{{ number_format($totalCredit, 2) }}</th>
                <th>{{ number_format($totalDebit, 2) }}</th>
                <th></th>
              </tr>
            </tfoot>
          </table>

          {{ $history->appends(request()->query())->links() }}

      </div>
    </div>
  </div>
</div>
</section>
@endsection
