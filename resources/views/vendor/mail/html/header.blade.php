<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;" class="logo-link">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="">
@else
<img src="{{ asset('assets/images/logo.png') }}" class="logo" alt="..." style="width: 120px; margin: auto">
@endif
</a>
</td>
</tr>
