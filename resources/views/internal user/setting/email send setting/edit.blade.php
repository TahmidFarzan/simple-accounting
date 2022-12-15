@extends('layouts.app')

@section('mainPageName')
    Application setting
@endsection

@section('mainCardTitle')
    Email send setting details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item"><a href="{{ route("setting.email.send.setting.index") }}">Email send setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
@if(Session::has('errors'))

@foreach ($errors->all() as $perError)
<li>{{ $perError }}</li>
@endforeach

@endif
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <form method="POST" action="{{ route('setting.email.send.setting.update',["slug" => $emailSendSetting->slug]) }}" enctype="multipart/form-data">
                @csrf

                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class=" col-md-6 mb-2">
                            <div class="row">
                                <label for="from" class="col-md-4 col-form-label text-bold">From <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                <div class="col-md-8">
                                    <input id="from" type="email" class="form-control @error('from') is-invalid @enderror" name="from" value="{{ (old('from') == null) ? (($emailSendSetting->fields_with_values["from"] == null) ? 1 : $emailSendSetting->fields_with_values["from"] ) : old('from') }}" autocomplete="from" placeholder="Ex: xx@xx.com" required max="255">
                                    @error('from')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class=" col-md-6 mb-2">
                            <div class="row">
                                <label for="to" class="col-md-4 col-form-label text-bold">To <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                <div class="col-md-8">
                                    <input id="to" type="email" class="form-control @error('to') is-invalid @enderror" name="to" value="{{ (old('to') == null) ? (($emailSendSetting->fields_with_values["to"] == null) ? 1 : $emailSendSetting->fields_with_values["to"] ) : old('to') }}" autocomplete="to" placeholder="Ex: xx@xx.com" required max="255">
                                    @error('to')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class=" col-md-6 mb-2">
                            <div class="row">
                                <label for="cc" class="col-md-4 col-form-label text-bold">CC <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                <div class="col-md-8">
                                    <input id="cc" type="email" class="form-control @error('cc') is-invalid @enderror" name="cc" value="{{ (old('cc') == null) ? (($emailSendSetting->fields_with_values["cc"] == null) ? 1 : $emailSendSetting->fields_with_values["cc"] ) : old('cc') }}" aucccomplete="cc" placeholder="Ex: xx@xx.com" required max="255">
                                    @error('cc')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class=" col-md-6 mb-2">
                            <div class="row">
                                <label for="reply" class="col-md-4 col-form-label text-bold">Reply <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                <div class="col-md-8">
                                    <input id="reply" type="email" class="form-control @error('reply') is-invalid @enderror" name="reply" value="{{ (old('reply') == null) ? (($emailSendSetting->fields_with_values["reply"] == null) ? 1 : $emailSendSetting->fields_with_values["reply"] ) : old('reply') }}" aureplycomplete="reply" placeholder="Ex: xx@xx.com" required max="255">
                                    @error('reply')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-2">
                            <div class="row mb-1">
                                @foreach ( $emailSendSetting->fields_with_values["module"] as $perModuleIndex => $perModuleValue )
                                    <div class="col-md-6 mb-2">
                                        <div class="card border">
                                            <div class=" card-body">
                                                <div class="d-flex justify-content-center">
                                                    <p>
                                                        <b>
                                                            {{ Str::ucfirst(Str::lower(preg_replace("/([a-z])([A-Z])/", "$1 $2", $perModuleIndex))) }}
                                                        </b>
                                                    </p>
                                                </div>

                                                <div class="row">
                                                    @foreach ($perModuleValue as $perModuleFieldIndex => $perModuleFieldValue)
                                                        @if ($perModuleFieldIndex == "send")
                                                            <div class="row mb-2">
                                                                <label for="reply" class="col-md-4 col-form-label text-bold">Send <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentSendStatus = (old('send_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('send_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendSendFor{{ Str::studly($perModuleIndex) }}" name="send_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('send_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >
                                                                        <option value="1" @if($currentSendStatus == "1") selected @endif>Yes</option>
                                                                        <option value="0" @if($currentSendStatus == "0") selected @endif>No</option>
                                                                    </select>
                                                                    @error('send_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if ($perModuleFieldIndex == "event")
                                                        @if ($perModuleIndex == "Report")
                                                            <div class="row mb-2">
                                                                <label for="Reply" class="col-md-4 col-form-label text-bold">Event <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentEventStatus = (old('event_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('event_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendEventFor{{ Str::studly($perModuleIndex) }}" name="event_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('event_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >
                                                                        <option value="All" @if($currentEventStatus == "All") selected @endif>All</option>
                                                                    </select>
                                                                    @error('event_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (in_array($perModuleIndex,array("ActivityLog","AuthenticationLog")) == true)
                                                            <div class="row mb-2">
                                                                <label for="reply" class="col-md-4 col-form-label text-bold">Event <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentEventStatus = (old('event_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('event_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendEventFor{{ Str::studly($perModuleIndex) }}" name="event_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('event_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >


                                                                        @foreach (array("All" => "All","Delete" => "Delete","DeleteAll" => "Delete all") as $perEvent => $perEventValue)
                                                                            <option value="{{ $perEvent }}" @if($currentEventStatus == $perEvent) selected @endif> {{ $perEventValue }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('event_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (in_array($perModuleIndex,array("ProjectContract")) == true)
                                                            <div class="row mb-2">
                                                                <label for="reply" class="col-md-4 col-form-label text-bold">Event <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentEventStatus = (old('event_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('event_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendEventFor{{ Str::studly($perModuleIndex) }}" name="event_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('event_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >


                                                                        @foreach (array("All" => "All","Create" => "Create","Update" => "Update","Delete" => "Delete","Complete" => "Complete","ReceivingPayment" => "Receiving payment","CompleteReceivePayment" => "Complete receive payment") as $perEvent => $perEventValue)
                                                                            <option value="{{ $perEvent }}" @if($currentEventStatus == $perEvent) selected @endif> {{ $perEventValue }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('event_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (in_array($perModuleIndex,array("ProjectContractJournal","ProjectContractPayment")) == true)
                                                            <div class="row mb-2">
                                                                <label for="reply" class="col-md-4 col-form-label text-bold">Event <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentEventStatus = (old('event_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('event_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendEventFor{{ Str::studly($perModuleIndex) }}" name="event_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('event_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >


                                                                        @foreach (array("All" => "All","Create" => "Create","Update" => "Update","Delete" => "Delete") as $perEvent => $perEventValue)
                                                                            <option value="{{ $perEvent }}" @if($currentEventStatus == $perEvent) selected @endif> {{ $perEventValue }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('event_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (in_array($perModuleIndex,array("ProjectContractPaymentMethod","ProjectContractCategory","User","UserPermissionGroup")) == true)
                                                            <div class="row mb-2">
                                                                <label for="reply" class="col-md-4 col-form-label text-bold">Event <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentEventStatus = (old('event_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('event_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendEventFor{{ Str::studly($perModuleIndex) }}" name="event_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('event_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >


                                                                        @foreach (array("All" => "All","Create" => "Create","Update" => "Update","Trash" => "Trash","Restore" => "Restore") as $perEvent => $perEventValue)
                                                                            <option value="{{ $perEvent }}" @if($currentEventStatus == $perEvent) selected @endif> {{ $perEventValue }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('event_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (in_array($perModuleIndex,array("Setting")) == true)
                                                            <div class="row mb-2">
                                                                <label for="reply" class="col-md-4 col-form-label text-bold">Event <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                                                <div class="col-md-8">
                                                                    @php
                                                                        $currentEventStatus = (old('event_for_'.Str::lower( Str::snake($perModuleIndex))) == null) ? $perModuleFieldValue : old('event_for_'.Str::lower( Str::snake($perModuleIndex))) ;
                                                                    @endphp
                                                                    <select id="mailSendEventFor{{ Str::studly($perModuleIndex) }}" name="event_for_{{ Str::lower( Str::snake($perModuleIndex)) }}" class="form-control @error('event_for_'.Str::lower( Str::snake($perModuleIndex))) is-invalid @enderror" >
                                                                        @foreach (array("All" => "All","Update" => "Update") as $perEvent => $perEventValue)
                                                                            <option value="{{ $perEvent }}" @if($currentEventStatus == $perEvent) selected @endif> {{ $perEventValue }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('event_for_'.Str::lower( Str::snake($perModuleIndex)))
                                                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @endif

                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4 mb-3">
                        <button type="submit" class="btn btn-outline-success">
                            Update
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('authContentTwo')
    <div class="card border-dark mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <a role="button" href="{{ route("setting.email.send.setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to email send
                </a>
            </div>
        </div>
    </div>
@endsection
