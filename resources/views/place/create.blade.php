@extends('layout.layout_without_call_to_action')

@section('content')

  @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  <div class="forms">
    <form method="POST" action="{{ route('place.store', ['language' => App::getLocale()]) }}">
      {!! csrf_field() !!}
      @include('place.partials.form_place')
      <div>
        <button class="button" type="submit">{{ ucfirst(trans('pokemonbuddy.place.create_place')) }}</button>
      </div>
    </form>
  </div>
@endsection

@section('javascript')
  <script src="{{ asset('/js/pokemonbuddy_place_field.js') }}"></script>
@endsection