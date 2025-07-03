@component('mail::message')
# {{ $letter->offer_title }}

{{ $letter->offer_message }}

Branch: **{{ $letter->branch_name }}**

Thanks,  
{{ config('app.name') }}
@endcomponent
