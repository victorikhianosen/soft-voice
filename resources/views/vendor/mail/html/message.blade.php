@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
<p style="text-align: left; margin-left: -30px;">Best Invoice Solution - Send invoice fast and get paid faster
<!-- © {{ date('Y') }} Invoice Solution. @lang('All rights reserved.') -->
<br>
Click here to <a href="#">create a free account</a>
<br>
<br>
Powered by <a href="https://linktr.ee/softwebdigital">Soft-Web Digital</a>
</p>
@endcomponent
@endslot
@endcomponent
