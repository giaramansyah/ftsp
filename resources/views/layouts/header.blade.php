<div class="container-fluid">
  <div class="row mb-2">
    <div class="col-sm-6">
      <h1>{{ $appender !== '' ? __($appender) . ' ' . __($header) : __($header) }}</h1>
    </div>
    <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
        @foreach ($breadcrumb as $value)
          <li class="breadcrumb-item{{ $value['active'] ? ' active' : '' }}">
            {{ __($value['label']) }}
          </li>
        @endforeach
      </ol>
    </div>
  </div>
</div>