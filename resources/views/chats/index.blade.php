@extends('layouts.app')

@section('content')
<h1 class="text-sm-start text-center">Customer Chats</h1>
<div class="table-responsive">
<table class="table pretty-table">
    <thead class="thead">
        <tr>
            <th>#</th>
            <th>Chat ID</th>
            <th>Customer Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chats as $key => $chat)
        <tr class="tbody">
            <td class="align-middle">{{ $key + 1 }}</td>
            <td class="align-middle">{{ $chat->id }}</td>
            <td class="align-middle">{{ $chat->customer->name }}</td>
            <td class="align-middle">
                <a href="{{ route('chats.show', $chat->customer_id) }}">View Chat</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection
