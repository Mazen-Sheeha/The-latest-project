@if (session()->get('success'))
    {{ toastify()->success(session()->get('success')) }}
@endif
@if ($errors->any())
    @foreach ($errors->all() as $error)
        {{ toastify()->error($error) }}
    @endforeach
@endif
