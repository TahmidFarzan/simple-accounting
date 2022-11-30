@extends('layouts.app')

@section('mainPageName')
    User permission group
@endsection

@section('mainCardTitle')
    Create
@endsection

@section('navBreadcrumbSection')
    <nav aria-label="breadcrumb" class="ms-3">
        <ol class="breadcrumb m-1 mb-2">
            <li class="breadcrumb-item"><a href="{{ route("user.permission.group.index") }}">User permission group</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create</li>
        </ol>
    </nav>
@endsection

@section('authContentOne')
    <div class="card border-dark mb-2">
        <div class="card-body text-dark">
            <form action="{{ route("user.permission.group.save") }}" method="POST" id="createForm">
                @csrf

                <div class="form-group mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Name <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input type="text" id="nameInput" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Ex: Hello" maxlength="100" required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">Code <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <input type="text" id="codeInput" name="code" class="form-control form-control-sm @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Ex: hello" maxlength="100" required>
                                    @error('code')
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <div class="row">
                                <label class="col-lg-4 col-form-label col-form-label-sm text-bold">User permission <i class="fa-solid fa-asterisk" style="font-size: 10px;!important"></i></label>
                                <div class="col-lg-8">
                                    <select class="form-control form-control-sm js-example-basic-multiple @error('user_permission') is-invalid @enderror" id="userPermissionInput" name="user_permission[]" multiple required>
                                        <option value="">Select</option>
                                        @foreach ($userPermissions as $perUserPermissionGroup => $userPermissionGroupPermissions)
                                            <optgroup label="{{ Str::ucfirst(Str::lower(preg_replace("/([a-z])([A-Z])/", "$1 $2", $perUserPermissionGroup))) }}">
                                                @foreach ($userPermissionGroupPermissions as $perUserPermission)
                                                    <option value="{{ $perUserPermission->slug }}" {{ (old('user_permission') == $perUserPermission->slug) ? 'selected' : null }}>{{ $perUserPermission->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                    @error('user_permission')
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
                            Save
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
                <a role="button" href="{{ route("user.permission.group.index") }}" class="btn btn-sm btn-secondary">
                    Go to user permission group
                </a>
            </div>
        </div>
    </div>
@endsection

@push('onPageExtraScript')
    <script src="{{ asset("select2/select2.min.js") }}"></script>
    <script>
        $(document).ready(function(){
            $("#userPermissionInput").select2({
                width: '100%',
                allowClear: true,
                closeOnSelect: false,
                placeholder: "Select",
            });
        });
    </script>
@endpush

@push('onPageExtraCss')
    <link href="{{ asset("select2/select2.min.css") }}" rel="stylesheet">
@endpush
