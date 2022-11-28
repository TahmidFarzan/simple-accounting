@extends('layouts.app')

@section('mainCardTitle')
    Verify Your Email Address
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
    <div class="card @auth() border-dark @endauth">
        <div class="card-header">Verify Your Email Address</div>
        <div class="card-body">
            <p>
                Before proceeding, please check your email for a verification link.If you did not receive the email
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">click here to request another.</button>.
                </form>
            </p>
        </div>
    </div>
@endsection
