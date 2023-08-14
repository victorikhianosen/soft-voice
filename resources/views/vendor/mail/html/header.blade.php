<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo.png" class="logo" alt="">
@else
<img src="https://invoice.softwebdigital.com/assets/images/logo.png" class="logo" alt="" style="width: 200px; height: auto;">
{{--{{ $slot }}--}}
@endif
</a>
</td>
</tr>
