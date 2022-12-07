@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  <div class="card">
    <form class="form-lazy-control" data-action="{{ $action }}">
      <div class="card-body">
        <div class="alert hidden" role="alert"></div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Nama Grup<code>*</code></label>
          <div class="col-sm-2">
            <input type="text" class="form-control form-control-sm text-uppercase" maxlength="20" name="name" value="{{ isset($name) ? $name : '' }}" required>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Keterangan</label>
          <div class="col-sm-6">
            <input type="text" class="form-control form-control-sm" maxlength="100" name="description" value="{{ isset($description) ? $description : '' }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-sm-2 col-form-label">Hak Akses<code>*</code></label>
          <div class="col-sm-9">
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
                        @if(isset($val['id']))
                          <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                            <input type="checkbox" class="custom-control-input" id="customSwitch-{{ $key.$index }}" name="privilege_id" value="{{ $val['id'] }}" {{ isset($privileges) && in_array($val['id'], $privileges) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitch-{{ $key.$index }}">&nbsp;</label>
                          </div>
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
      <div class="card-footer">
        <div class="form-button">
          <div class="row justify-content-center">
            <div class="col-sm-1">
              @include('partials.button.back', ['class' => 'btn-sm btn-block', 'action' => route('settings.privigroup.index')])
            </div>
            <div class="col-sm-1">
              @include('partials.button.submit')
            </div>
          </div>
        </div>
        <div class="form-loading">
          <img src="{{ asset('img/loading.gif') }}" height="40">
        </div>
      </div>
    </form> 
  </div>
</div>
@endsection