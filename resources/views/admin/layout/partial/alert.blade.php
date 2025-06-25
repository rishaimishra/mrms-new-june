@if(session()->has('alert'))

    @php $alert = session('alert') @endphp


    @isset($alert['type'])
        @switch($alert['type'])
            @case(\App\Http\Controllers\Admin\AdminController::MESSAGE_SUCCESS)
            <div class="alert alert-success">
                {{ $alert['msg'] }}
            </div>
            @break

            @case(\App\Http\Controllers\Admin\AdminController::MESSAGE_ERROR)
            <div class="alert alert-danger">
                {{ $alert['msg'] }}
            </div>
            @break
        @endswitch
    @endisset

    @isset($alert['status'])
        <div class="alert alert-{{ $alert['status'] }}">
            {{ $alert['message'] }}
        </div>
    @endisset
@endif
