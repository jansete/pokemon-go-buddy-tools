@if(Auth::check())
    <div class="disclaimer">
        <p>{{ Auth::user()->username }} {{ Lang::get('shitguide.welcome') }}</p>
        @if (session('status'))
                <p>{!! session('status') !!}</p>
        @endif
    </div>
@endif