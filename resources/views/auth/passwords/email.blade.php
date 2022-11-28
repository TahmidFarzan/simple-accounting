@extends('layouts.app')


@section('mainCardTitle')
    Send Reset Password Request
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@auth()
    @section('authContentOne')
@endauth

@guest
    @section('content')
@endguest()

@section('content')
    <div class="card" style="min-height: inherit !important;">
        <div class="card-header">Send Reset Password Request</div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email.">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-primary">
                            Send Password Reset Link
                        </button>
                    </div>

                    <div class="col-md-6 offset-md-4">
                        <p>

                            @if (Route::has('login'))
                                <a class="btn btn-link" href="{{ route('login') }}">
                                    Have a account.
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a class="btn btn-link" href="{{ route('register') }}">
                                    Become a register user.
                               </a>
                            @endif
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
