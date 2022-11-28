@auth
    @if (Auth::user()->default_password==1)
        <div class="alert-messages alert alert-warning" role="alert">
            <div class="row">
                <div class="col-11 col-lg-11 col-md-11 col-sm-11">
                    <b>You are using system default password. Please update or change your password.</b>
                </div>
                <div class="col-1 col-lg-1 col-md-1 col-sm-1">
                    <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    @if(Session::has('errors'))
        <div class="alert-messages alert alert-danger" role="alert">
            <div class="row">
                <div class="col-11 col-lg-11 col-md-11 col-sm-11">
                    @if (is_string(Session::get('errors'))==true)
                        <p class="text-center">
                            <b>{{ Session::get('errors') }}</b>
                        </p>
                    @endif

                    @if ((is_string(Session::get('errors'))==false))
                        <ul>
                            @foreach ($errors->all() as $perError)
                                <li>{{ $perError }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div class="p-1 col-1 col-lg-1 col-md-1 col-sm-1">
                    <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif
@endauth

@if(Session::has('status'))
    <div class="alert-messages alert alert-success" role="alert">
        <div class="row">
            <div class="col-11 col-lg-11 col-md-11 col-sm-11">
                @if (is_string(Session::get('status'))==true)
                    <p class="text-center">
                        <b>{{ Session::get('status') }}</b>
                    </p>
                @endif
                @if ((is_string(Session::get('status'))==false))
                    <ul>
                        @foreach (Session::get('status') as $perStatus)
                            <li>{{ $perStatus }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-1 col-lg-1 col-md-1 col-sm-1">
                <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

@if(Session::has('warning'))
    <div class="alert-messages alert alert-warning" role="alert">
        <div class="row">
            <div class="col-11 col-lg-11 col-md-11 col-sm-11">
                @if (is_string(Session::get('warning'))==true)
                    <p class="text-center">
                        <b>{{ Session::get('warning') }}</b>
                    </p>
                @endif
                @if ((is_string(Session::get('warning'))==false))
                    <ul>
                        @foreach (Session::get('warning') as $perWarning)
                            <li>{{ $perWarning }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="col-1 col-lg-1 col-md-1 col-sm-1">
                <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

@if (Session::has('resent'))
    <div class="alert-messages alert alert-success" role="alert">
        <div class="row">
            <div class="col-11 col-lg-11 col-md-11 col-sm-11">
                <b>A fresh verification link has been sent to your email address</b>
            </div>
            <div class="col-1 col-lg-1 col-md-1 col-sm-1">
                <button type="button" class="btn-sm btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endif

@push('onPageExtraScript')
    <script>
        $(document).ready(function(){
            if ($('.alert-messages').length){
                setTimeout(function(){
                    $('.alert-messages').remove();
                }, 15000);
            }
        });
    </script>
@endpush
