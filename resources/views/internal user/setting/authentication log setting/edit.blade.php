@extends('layouts.app')

@section('mainPageName')
    Application setting
@endsection

@section('mainCardTitle')
    Authentication log setting details
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("setting.index") }}">Setting</a></li>
            <li class="breadcrumb-item"><a href="{{ route("setting.authentication.log.setting.index") }}">Authentication log setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-3">
        <div class="card-body text-dark">
            <form method="POST" action="{{ route('setting.authentication.log.setting.update',["slug" => $authenticationLogSetting->slug]) }}" enctype="multipart/form-data">
                @csrf

                @method("PATCH")

                <div class="form-group mb-3">
                    <div class="row">
                        <div class=" col-md-6 mb-2">
                            <div class="row">
                                <label for="deleteRecordsOlderThan" class="col-md-8 col-form-label text-bold">Delete records older than <i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i> </label>
                                <div class="col-md-4">
                                    <input id="deleteRecordsOlderThan" type="number" class="form-control @error('delete_records_older_than') is-invalid @enderror" name="delete_records_older_than" value="{{ (old('delete_records_older_than') == null) ? (($authenticationLogSetting->fields_with_values["delete_records_older_than"] == null) ? 1 : $authenticationLogSetting->fields_with_values["delete_records_older_than"] ) : old('delete_records_older_than') }}" autocomplete="delete_records_older_than" placeholder="Ex: 10" autofocus required min="1" max="365">
                                    @error('delete_records_older_than')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="row mb-1">
                                <label class="col-md-8 col-form-label text-bold">Auto delete<i class="fa-solid fa-asterisk float-end mt-2" style="font-size: 10px;!important"></i></label>
                                <div class="col-md-4">
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="auto_delete" value="No" {{  (old("auto_delete") == null) ? ( ($authenticationLogSetting->fields_with_values["auto_delete"] == "Yes") ? null : "checked" ) : ( ( (old("auto_delete") == "Yes") ? null : "checked" ) ) }} >
                                            <label class="form-check-label">No</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="auto_delete" value="Yes" {{  (old("auto_delete") == null) ? ( ($authenticationLogSetting->fields_with_values["auto_delete"] == "Yes") ? "checked" : null ) : ( ( (old("auto_delete") == "Yes") ? "checked" : null ) ) }} >
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                    </div>
                                    @error('auto_delete')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
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
                <a role="button" href="{{ route("setting.authentication.log.setting.index") }}" class="btn btn-sm btn-secondary">
                    Go to authentication log
                </a>
            </div>
        </div>
    </div>
@endsection
