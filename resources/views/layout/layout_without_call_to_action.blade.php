<!DOCTYPE html>
<html lang="es">
<head>
    @include('scripts.head')
</head>
<body>

@include('sections.header')

@include('sections.navigation')

@include('sections.disclaimer')

@yield('content')

@include('sections.footer')

@include('scripts.javascript')

@yield('javascript')

@include('js.nearest')
@include('js.analytics')

</body>
</html>
