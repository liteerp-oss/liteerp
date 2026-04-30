@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{env('APP_URL') . env('LOGO_PATH') . '/logo-full.png'}}" width="250" class="logo" alt="Laravel Logo">
</a>
</td>
</tr>
