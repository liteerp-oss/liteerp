<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ !isset($title) ? env('APP_NAME') : $title}} {{ isset($slogan) ? ' | ' . $slogan : ' | Simple And Pure' }} </title>
  <meta name="description" content="{{isset($description) ? $description : 'Simple And Pure'}}">
  <meta name="csrf-token" id="meta-csrf-token" content="{{ csrf_token() }}">
  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @else
  @endif
</head>

<body class="home-body">

  <div class="">
    @yield('content')
  </div>
@yield('scripts')
</body>

</html>