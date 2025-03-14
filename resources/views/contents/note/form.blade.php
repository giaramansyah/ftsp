@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
	<div class="card">
		<form class="form-lazy-control" data-action="{{ $action }}" data-validate="max_amount_request,max_amount_approve">
			<div class="card-body">
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Tahun Akademik') }}<code>*</code></label>
					<div class="col-sm-2">
						<select class="form-control form-control-sm select2" name="year" {{ isset($mandatory) && $mandatory
							? 'required' : '' }} onchange="getData();">
							<option value="">-- Silakan Pilih --</option>
							@foreach ($yearArr as $key => $value)
							<option value="{{ $value['id'] }}" {{ isset($year) && $year==$value['id'] ? 'selected' : '' }}>{{
								$value['name'] }}
							</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Unit') }}<code>*</code></label>
					<div class="col-sm-2">
						<select class="form-control form-control-sm select2" name="division_id" {{ isset($mandatory) && $mandatory
							? 'required' : '' }} onchange="getData();">
							<option value="">-- Silakan Pilih --</option>
							@foreach ($divisionArr as $key => $value)
							<option value="{{ $value['id'] }}" {{ isset($division_id) && $division_id==$value['id'] ? 'selected' : ''
								}}>
								{{ $value['name'] }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group row form-staff-select">
					<label class="col-sm-2 col-form-label">{{ __('PIC') }}<code>*</code></label>
					<div class="col-sm-2">
						<table class="table table-sm" width="100%">
							<tbody>
								@foreach ($staffArr as $key => $value)
								@if (!in_array($value['id'], [config('global.staff.code.kaprodis1'),
								config('global.staff.code.kaprodis2')]))
								<tr>
									<td>
										<div class="form-check">
											<input class="form-check-input" type="checkbox" name="staff_id" value="{{ $value['id'] }}" {{
												isset($staff_id) && in_array($value['id'], $staff_id) ? 'checked' : '' }} {{ isset($mandatory)
												&& $mandatory && $key==1 ? 'required' : '' }} onchange="getData();">
											<label class="form-check-label">{{ $value['name'] }}</label>
										</div>
									</td>
								</tr>
								@endif
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
				<div class="form-group row form-staff-input-1">
					<label class="col-sm-2 col-form-label">{{ __('PIC') }}<code>*</code></label>
					<div class="col-sm-2">
						<input type="hidden" name="staff_id" value="{{ config('global.staff.code.kaprodis1') }}">
						<input type="text" class="form-control form-control-sm" name="staff"
							value="{{ config('global.staff.desc.kaprodis1') }}" readonly {{ isset($mandatory) && $mandatory && $key==0
							? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row form-staff-input-2">
					<label class="col-sm-2 col-form-label">{{ __('PIC') }}<code>*</code></label>
					<div class="col-sm-2">
						<input type="hidden" name="staff_id" value="{{ config('global.staff.code.kaprodis2') }}">
						<input type="text" class="form-control form-control-sm" name="staff"
							value="{{ config('global.staff.desc.kaprodis2') }}" readonly {{ isset($mandatory) && $mandatory && $key==0
							? 'required' : '' }}>
					</div>
				</div>
				@if (!isset($id))
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Mata Anggaran<code>*</code></label>
					<div class="col-sm-10">
						<table class="table table-sm table-bordered table-striped table-ma" width="100%">
							<thead>
								<tr>
									<th class="text-center">#</th>
									<th class="text-center">No. M.A.</th>
									<th class="text-center">Program</th>
									<th class="text-center">PIC</th>
									<th class="text-center">Dana</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				@endif
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('No. M.A.') }}<code>*</code></label>
					<div class="col-sm-2">
						<input type="text" class="form-control form-control-sm" maxlength="20" name="ma_id"
							value="{{ isset($ma_id) ? $ma_id : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Program') }}<code>*</code></label>
					<div class="col-sm-6">
						<input type="text" class="form-control form-control-sm" name="program"
							value="{{ isset($program) ? $program : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Dana RAB') }}<code>*</code></label>
					<div class="col-sm-2">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Rp</span>
							</div>
							<input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount"
								id="validate_max_request" onkeypress="preventAlpha(event)" onkeyup="numberFormat(this, true)"
								onblur="numberFormat(this, true);amountText(this.value, '#text_amount')"
								value="{{ isset($amount) ? $amount : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
					<div class="col-sm-7">
						<input type="text" class="form-control form-control-sm" id="text_amount" readonly>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">No. Surat<code>*</code></label>
					<div class="col-sm-2">
						<input type="text" class="form-control form-control-sm" name="note_reff"
							value="{{ isset($note_reff) ? $note_reff : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Tgl. Surat<code>*</code></label>
					<div class="col-sm-2">
						<input type="date" class="form-control form-control-sm" name="note_date"
							value="{{ isset($note_date) ? $note_date : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Tgl. Upload Surat<code>*</code></label>
					<div class="col-sm-2">
						<input type="date" class="form-control form-control-sm" name="note_upload"
							value="{{ isset($note_upload) ? $note_upload : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : ''
							}}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Perihal') }}<code>*</code></label>
					<div class="col-sm-6">
						<input type="text" class="form-control form-control-sm" maxlength="200" name="regarding"
							value="{{ isset($regarding) ? $regarding : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Link URL') }}<code>*</code></label>
					<div class="col-sm-6">
						<input type="text" class="form-control form-control-sm" maxlength="200" name="link_url"
							value="{{ isset($link_url) ? $link_url : '' }}" {{ isset($mandatory) && $mandatory ? 'required' : '' }}>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Pengajuan Dana') }}<code>*</code></label>
					<div class="col-sm-2">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Rp</span>
							</div>
							<input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount_requested"
								id="validate_max_approve" onkeypress="preventAlpha(event)" onkeyup="numberFormat(this, true)"
								onblur="numberFormat(this, true);amountText(this.value, '#text_amount_request')"
								value="{{ isset($amount_requested) ? $amount_requested : '' }}" {{ isset($mandatory) && $mandatory
								? 'required' : '' }}>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
					<div class="col-sm-7">
						<input type="text" class="form-control form-control-sm" id="text_amount_request" readonly>
					</div>
				</div>
				@if (isset($status) && $status == config('global.status.code.finished'))
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">{{ __('Realisasi Dana') }}<code>*</code></label>
					<div class="col-sm-2">
						<div class="input-group input-group-sm">
							<div class="input-group-prepend">
								<span class="input-group-text">Rp</span>
							</div>
							<input type="text" class="form-control form-control-sm text-right" maxlength="20" name="amount_approved"
								onkeypress="preventAlpha(event)" onkeyup="numberFormat(this, true)"
								onblur="numberFormat(this, true);amountText(this.value, '#text_amount_approve')"
								value="{{ isset($amount_approved) ? $amount_approved : '' }}" {{ isset($mandatory) && $mandatory
								? 'required' : '' }}>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Terbilang<code>*</code></label>
					<div class="col-sm-7">
						<input type="text" class="form-control form-control-sm" id="text_amount_approve" readonly>
					</div>
				</div>
				@endif
			</div>
			<div class="card-footer">
				<div class="form-button">
					<div class="row justify-content-center">
						<div class="col-sm-1">
							@include('partials.button.back', [
							'class' => 'btn-sm btn-block',
							'action' => route('master.note.index'),
							])
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
@section('push-js')
<script type="text/javascript">
	$('.select2').select2({
            theme: 'bootstrap4'
        });

        if ($('.table-ma').length) {
            $('.table-ma').DataTable({
                dom: 'rf'
            });
        }

        $('select[name="division_id"]').on('change', function() {
            if ($(this).val() == "{{ config('global.division.code.fakultas') }}") {
                $('.form-staff-select').removeClass('d-none');
                $('.form-staff-input-1').addClass('d-none');
                $('.form-staff-input-2').addClass('d-none');
                $('.form-staff-select').find('input').attr('disabled', false);
                $('.form-staff-input-1').find('input').attr('disabled', true);
                $('.form-staff-input-2').find('input').attr('disabled', true);
            } else if ($(this).val() == "{{ config('global.division.code.arsitektur') }}" || $(this).val() ==
                "{{ config('global.division.code.sipil') }}") {
                $('.form-staff-select').addClass('d-none');
                $('.form-staff-input-1').removeClass('d-none');
                $('.form-staff-input-2').addClass('d-none');
                $('.form-staff-select').find('input').attr('disabled', true);
                $('.form-staff-input-1').find('input').attr('disabled', false);
                $('.form-staff-input-2').find('input').attr('disabled', true);
            } else if ($(this).val() == "{{ config('global.division.code.mta') }}" || $(this).val() ==
                "{{ config('global.division.code.mts') }}") {
                $('.form-staff-select').addClass('d-none');
                $('.form-staff-input-1').addClass('d-none');
                $('.form-staff-input-2').removeClass('d-none');
                $('.form-staff-select').find('input').attr('disabled', true);
                $('.form-staff-input-1').find('input').attr('disabled', true);
                $('.form-staff-input-2').find('input').attr('disabled', false);
            } else {
                $('.form-staff-select').addClass('d-none');
                $('.form-staff-input-1').addClass('d-none');
                $('.form-staff-input-2').addClass('d-none');
                $('.form-staff-select').find('input').attr('disabled', true);
                $('.form-staff-input-1').find('input').attr('disabled', true);
                $('.form-staff-input-2').find('input').attr('disabled', true);
            }
        })

        $('select[name="division_id"]').trigger('change')

        if ($('input[name=amount]').val() != '') {
            amountText($('input[name=amount]').val(), '#text_amount')
        }

        if ($('input[name=amount_requested]').val() != '') {
            amountText($('input[name=amount_requested]').val(), '#text_amount_request')
        }

        if ($('input[name=amount_approved]').length && $('input[name=amount_approved]').val() != '') {
            amountText($('input[name=amount_approved]').val(), '#text_amount_approve')
        }

        function getData() {
            var year = $('select[name="year"]').val();
            var division_id = $('select[name="division_id"]').val();

            if (year != '' && division_id != '' && $('.table-ma').length) {
                $('.table-ma').dataTable().fnClearTable();
                $('.table-ma').dataTable().fnDestroy();

								var data = {
										year: year,
										division_id: division_id
								};

								if(division_id == 1) {
									var staff_id = [];
									$('input[name=staff_id]:checked').each(function(index, value){
										staff_id.push($(value).val());
									});

									if(staff_id.length > 0) {
										data.staff_id = staff_id;
									} else {
										data.staff_id = [0];
									}

								}

                $('.table-ma').DataTable({
                    responsive: true,
                    autoWidth: true,
                    processing: true,
                    paging: false,
                    info: false,
                    ajax: {
                        method: 'get',
                        url: "{{ route('master.note.data') }}",
                        data: data
                    },
                    dom: 'rf',
                    order: [],
                    columns: [{
                            data: 'input',
                            name: 'input',
                            orderable: false,
                            searchable: false,
                            class: "text-center"
                        },
                        {
                            data: 'ma_id',
                            name: 'ma_id',
                            orderable: true,
                            searchable: true
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: true,
                            searchable: true,
                            class: "text-wrap"
                        },
                        {
                            data: 'staff',
                            name: 'staff',
                            orderable: true,
                            searchable: true,
                            class: "text-wrap"
                        },
                        {
                            data: 'amount',
                            name: 'amount',
                            orderable: true,
                            searchable: true,
                            class: "text-right"
                        },
                    ],
                    fnInitComplete: function() {
                        $('.table-ma').off().on('click', 'input[name="data_id"]', function() {
                            $('input[name="ma_id"]').val($(this).data('ma'))
                            $('input[name="program"]').val($(this).data('description'))
                            $('input[name="amount"]').val(formatCurrency($(this).data('amount')))
                            amountText($('input[name=amount]').val(), '#text_amount')
                        })
                    }
                });
            }
        }
</script>
@endsection