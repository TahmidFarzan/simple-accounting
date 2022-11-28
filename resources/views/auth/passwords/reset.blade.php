@extends('layouts.app')

@section('mainCardTitle')
    Reset Password
@endsection

@section('statusMesageSection')
    @include('utility.status messages')
@endsection

@section('content')
    <div class="card" style="min-height: inherit !important;">
        <div class="card-header">Reset Password</div>
        <div class="card-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">Email</label>

                    <div class="col-md-6">
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Enter your email.">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">Password</label>

                    <div class="col-md-6">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" minlength="8" placeholder="Enter your password.">

                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">Confirm Password</label>

                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your email.">
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-outline-success">
                            Reset Password
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
