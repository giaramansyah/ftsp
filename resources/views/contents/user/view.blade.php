@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4">
      <div class="card">
        <div class="card-body box-profile">
          <div class="text-center">
            <img class="profile-user-img img-fluid img-circle" src="{{ asset('img/avatar.png') }}" alt="Profile Image">
          </div>
          <h3 class="profile-username text-center">{{ $fullname }}</h3>
          <p class="text-muted text-center">{{ $username }}</p>
          <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
              <b>Email</b>
              <p class="float-right mb-0">{{ $email }}</p>
            </li>
            <li class="list-group-item">
              <b>Unit</b>
              <p class="float-right mb-0">{{ $division }}</p>
            </li>
            <li class="list-group-item">
              <b>PIC</b>
              <p class="float-right mb-0">{{ $staff }}</p>
            </li>
            <li class="list-group-item">
              <b>Akun Terhubung</b>
              <p class="float-right mb-0">
                @if ($is_login)
                <i class="fas fa-check-circle text-success"></i> {{ __('Linked') }}
                @else
                <i class="fas fa-times-circle text-danger"></i> {{ __('Unlinked') }}
                @endif
              </p>
            </li>
            <li class="list-group-item">
              <b>Tgl. Diubah</b>
              <p class="float-right mb-0">{{ $updated_by }} On {{ $updated_at }}</p>
            </li>
          </ul>
        </div>
        <div class="card-footer">
          <div class="row justify-content-center">
            <div class="col-auto">
              @include('partials.button.back', array('class' => 'btn-sm', 'action' => route('settings.user.index')))
            </div>
            @if($is_update)
            <div class="col-auto">
              @include('partials.button.edit', array('class' => 'btn-sm', 'action' => route('settings.user.edit', ['id'
              => $id])))
            </div>
            @endif
            @if($is_delete)
            <div class="col-auto">
              @include('partials.button.delete', array('class' => 'btn-sm', 'source' => 'database', 'action' => route('settings.user.post',
              ['action' => config('global.action.form.delete'), 'id' => $id])))
            </div>
            @endif
            @if($is_login && $is_update)
            <div class="col-auto">
              @include('partials.button.forcelogout', array('class' => 'btn-sm', 'action' => route('settings.user.post',
              ['action' => 'force_logout', 'id' => $id])))
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-8">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-sm-6">
              <h4 class="card-title mb-0 text-bold">{{ __('Privilege') }}</h4>
            </div>
            <div class="col-sm-6 text-right">
              <h5 class="mb-0 text-bold">{{ $privilege_name }}</h5>
              <p class="mb-0">{{ $privilege_desc }}</p>
            </div>
          </div>
        </div>
        <div class="card-body">
          <table class="table table-sm" width="100%">
            <thead>
              <tr>
                <th class="text-center">{{ __('Modules') }}</th>
                @foreach ($modulesArr as $value)
                <th class="text-center">{{ __($value) }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @foreach ($privilegeArr as $key => $value)
              <tr>
                <td>{{ __($value['label']) }}</td>
                @foreach ($value['privileges'] as $index => $val)
                <td class="text-center">
                  @if(isset($val['id']) && in_array($val['id'], $privileges))
                  <i class="fas fa-check-circle text-success"></i>
                  @endif
                </td>
                @endforeach
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0 text-bold">{{ __('Activity Log') }}</h4>
        </div>
        <div class="card-body">
          <table class="table table-sm table-striped table-data" width="100%">
            <thead>
              <tr>
                <th>{{ __('Timestamps') }}</th>
                <th>{{ __('Activity') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('IP Address') }}</th>
                <th>{{ __('Agent/Browser') }}</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@include('partials.modal.modaldelete')
@endsection
@section('push-js')
<script type="text/javascript">
  $('.table-data').DataTable(
        {
            responsive: true,
            autoWidth: true,
            processing: true,
            ajax : "{{ route('logs.activity.user', ['id' => $username_enc]) }}",
            order: [],
            columns : [
              {data: 'updated_at', name: 'updated_at', orderable: false, searchable: false},
              {data: 'privilege', name: 'privilege', orderable: true, searchable: true},
              {data: 'description', name: 'description', orderable: true, searchable: true},
              {data: 'ip_address', name: 'ip_address', orderable: true, searchable: true},
              {data: 'agent', name: 'agent', orderable: false, searchable: false},
            ],
        }
    );
</script>
@endsection