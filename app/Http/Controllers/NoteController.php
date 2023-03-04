<?php

namespace App\Http\Controllers;

use App\Library\ExcelWriter;
use App\Library\Response;
use App\Library\SecureHelper;
use App\Models\Data;
use App\Models\MapNote;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class NoteController extends Controller
{
    protected $_create = 'NTCR';
    protected $_update = 'NTUP';
    protected $_delete = 'NTRM';
    protected $_readall = 'NTRA';
    protected $_readid = 'NTRD';

    public function index($year = null)
    {
        if (!$this->hasPrivilege($this->_readall)) {
            return abort(404);
        }

        $currYear = date('Y');
        if ($year) {
            $plainYear = SecureHelper::unsecure($year);

            if ($plainYear) {
                $currYear = $plainYear;
            }
        }

        $years = $this->getYears();
        $division = $this->getDivisions();

        $view = ['divisionArr' =>  $division, 'yearArr' =>  $years, 'year' => $currYear, 'is_create' => $this->hasPrivilege($this->_create)];

        return view('contents.note.index', $view);
    }

    public function add()
    {
        if (!$this->hasPrivilege($this->_create)) {
            return abort(404);
        }

        $year = $this->getYears();
        $division = $this->getDivisions();
        $staff = $this->getStaffs();
        $staff = Arr::except($staff, 0);

        $view = ['divisionArr' =>  $division, 'yearArr' => $year, 'staffArr' => $staff, 'action' => route('master.note.post', ['action' => config('global.action.form.add'), 'id' => 0]), 'mandatory' => $this->hasPrivilege($this->_create)];

        return view('contents.note.form', $view);
    }

    public function edit($id)
    {
        if (!$this->hasPrivilege($this->_update)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Note::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        if (!$this->hasPrivilege($this->_readid)) {
            $data = array('ma_id' => $data['ma_id']);
        }

        $year = $this->getYears();
        $division = $this->getDivisions();
        $staff = $this->getStaffs();
        $staff = Arr::except($staff, 0);

        $view = ['yearArr' => $year, 'divisionArr' => $division, 'staffArr' => $staff, 'action' => route('master.note.post', ['action' => config('global.action.form.edit'), 'id' => $id]), 'mandatory' => $this->hasPrivilege($this->_readid)];

        return view('contents.note.form', array_merge($data, $view));
    }

    public function view($id)
    {
        if (!$this->hasPrivilege($this->_readid)) {
            return abort(404);
        }

        $plainId = SecureHelper::unsecure($id);

        if (!$plainId) {
            return abort(404);
        }

        $data = Note::find($plainId)->toArray();

        if (!$data) {
            return abort(404);
        }

        $data['id'] = $id;
        $amount = $this->convertAmount($data['amount'], true);
        $amount_request = $this->convertAmount($data['amount_requested'], true);
        $amount_approved = $this->convertAmount($data['amount_approved'], true);

        $amount_difference = ($amount_request - $amount_approved);
        $ammount_remain = ($amount - $amount_approved);

        $data['amount_difference'] = $amount_approved > 0 ? $this->convertAmount($amount_difference) : '-';
        $data['ammount_remain'] = $amount_approved > 0 ? $this->convertAmount($ammount_remain) : '-';

        $view = ['is_update' => $this->hasPrivilege($this->_update), 'is_delete' => $this->hasPrivilege($this->_delete), 'modal_action' => route('master.note.post', ['action' => config('global.action.form.edit'), 'id' => $id])];

        $this->writeAppLog($this->_readid, 'Note : ' . $data['ma_id']);

        return view('contents.note.view', array_merge($data, $view));
    }

    public function getList(Request $request)
    {
        if ($request->ajax()) {
            $param = $request->input('id');

            if (!isset($param)) {
                $year = 0;
                $division = 0;
            } else {
                $param = SecureHelper::unpack($param);

                if (!is_array($param)) {
                    $year = 0;
                    $division = 0;
                } else {
                    $year = $param['year'];
                    $division = $param['id'];
                }
            }

            $data = Note::select(['id', 'ma_id', 'program', 'amount', 'note_reff', 'note_date', 'amount_approved', 'amount_requested', 'status', 'updated_at'])->where('is_trash', 0)->where('year', $year)->where('division_id', $division)->orderBy('ma_id');
            if (Auth::user()->staff_id != config('global.staff.code.admin')) {
                $map = MapNote::where('staff_id',  Auth::user()->staff_id)->get()->toArray();
                $map = array_column($map, 'note_id');
                $data->whereIn('id', $map);
            }
            $data->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('ma', 'approved', 'status_desc', 'percentage');
            $table->addIndexColumn();

            $table->addColumn('ma', function ($row) {
                if ($this->hasPrivilege($this->_readid)) {
                    $column = '<a href="' . route('master.note.view', ['id' => SecureHelper::secure($row->id)]) . '">' . $row->ma_id . '</a>';
                } else {
                    $column = $row->ma_id();
                }

                return $column;
            });

            $table->addColumn('approved', function ($row) {
                if ($row->amount_approved == '0') {
                    $column = '-';
                } else {
                    $column = $row->amount_approved;
                }

                return $column;
            });

            $table->addColumn('percentage', function ($row) {
                $amount = $this->convertAmount($row->amount, true);
                $amount_requested = $this->convertAmount($row->amount_requested, true);

                $column = '0%';
                if($amount != null && $amount > 0) {
                    $column = round(($amount_requested/$amount)*100, 2) . '%';
                }

                return $column;
            });

            $table->addColumn('status_desc', function ($row) {
                $column = '';
                if ($row->status == config('global.status.code.unfinished')) {
                    $column .= '<small class="badge badge-danger">' . $row->status_desc . '</small>';
                }

                if ($row->status == config('global.status.code.finished')) {
                    $column .= '<small class="badge badge-success">' . $row->status_desc . '</small>';
                }

                return $column;
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog($this->_readall);

            return $table->toJson();
        }
    }

    public function getData(Request $request)
    {
        if ($request->ajax()) {
            if (($request->input('year') == null && $request->input('year') == '') || ($request->input('division_id') == null && $request->input('division_id') == '')) {
                $data = Data::where('data_id', 0);
                $table = DataTables::eloquent($data);
                return $table->toJson();
            }

            $year = $request->input('year');
            $division_id = $request->input('division_id');

            $data = Data::select(['id', 'ma_id', 'description', 'amount'])->where('year', $year)->where('division_id', $division_id)->where('is_trash', 0)->orderBy('ma_id');
            $table = DataTables::eloquent($data);
            $rawColumns = array('input');

            $table->addColumn('input', function ($row) {
                $column = '<div class="form-check">
                <input class="form-check-input" type="radio" name="data_id" id="ma' . $row->ma_id . '" value="' . SecureHelper::secure($row->id) . '" data-ma="' . $row->ma_id . '" data-description="' . $row->description . '" data-amount="' . $this->convertAmount($row->amount, true) . '">
                <label class="form-check-label">&nbsp;</label>
                </div>';

                return $column;
            });

            $table->rawColumns($rawColumns);

            $this->writeAppLog('DARA');

            return $table->toJson();
        }
    }

    public function post(Request $request, $action, $id)
    {
        if (!in_array($action, config('global.action.form'))) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        if (in_array($action, Arr::only(config('global.action.form'), ['add', 'edit']))) {
            $param = SecureHelper::unpack($request->input('json'));

            if (!is_array($param)) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            if ($action === config('global.action.form.add')) {
                if (!$this->hasPrivilege($this->_create)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                $note = Note::create([
                    'year' => $param['year'],
                    'division_id' => $param['division_id'],
                    'ma_id' => $param['ma_id'],
                    'note_reff' => $param['note_reff'],
                    'note_date' => $param['note_date'],
                    'note_upload' => $param['note_upload'],
                    'program' => $param['program'],
                    'regarding' => $param['regarding'],
                    'link_url' => $param['link_url'],
                    'amount' => $param['amount'],
                    'amount_requested' => $param['amount_requested'],
                    'created_by' => Auth::user()->username,
                    'updated_by' => Auth::user()->username,
                ]);

                if ($note->id) {
                    if (!is_array($param['staff_id'])) $param['staff_id'] = array($param['staff_id']);
                    foreach ($param['staff_id'] as $value) {
                        MapNote::create([
                            'note_id' => $note->id,
                            'staff_id' => $value
                        ]);
                    }

                    $response = new Response(true, __('Note created successfuly'), 1);
                    $response->setRedirect(route('master.note.index'));

                    $this->writeAppLog($this->_update, 'Note : ' . $param['ma_id']);
                } else {
                    $response = new Response(false, __('Note create failed. Please try again'));
                }
            }

            if ($action === config('global.action.form.edit')) {
                if (!$this->hasPrivilege($this->_update)) {
                    $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                    return response()->json($response->responseJson());
                }

                $plainId = SecureHelper::unsecure($id);

                if (!$plainId) {
                    $response = new Response();
                    return response()->json($response->responseJson());
                }

                if (!$this->hasPrivilege($this->_readid)) {
                    $data = Note::find($plainId);

                    if (isset($param['year']) && $param['year'] != '') $data->year = $param['year'];
                    if (isset($param['division_id']) && $param['division_id'] != '') $data->division_id = $param['division_id'];
                    if (isset($param['ma_id']) && $param['ma_id'] != '') $data->ma_id = $param['ma_id'];
                    if (isset($param['note_reff']) && $param['note_reff'] != '') $data->note_reff = $param['note_reff'];
                    if (isset($param['note_date']) && $param['note_date'] != '') $data->note_date = $param['note_date'];
                    if (isset($param['note_upload']) && $param['note_upload'] != '') $data->note_upload = $param['note_upload'];
                    if (isset($param['program']) && $param['program'] != '') $data->program = $param['program'];
                    if (isset($param['regarding']) && $param['regarding'] != '') $data->regarding = $param['regarding'];
                    if (isset($param['link_url']) && $param['link_url'] != '') $data->link_url = $param['link_url'];
                    if (isset($param['amount']) && $param['amount'] != '') $data->amount = $param['amount'];
                    if (isset($param['amount_requested']) && $param['amount_requested'] != '') $data->amount_requested = $param['amount_requested'];
                    if (isset($param['amount_approved']) && $param['amount_approved'] != '') $data->amount_approved = $param['amount_approved'];
                    if (isset($param['status']) && $param['status'] != '') $data->status = $param['status'];
                    if (isset($param['staff_id']) && !is_array($param['staff_id'])) $param['staff_id'] = array($param['staff_id']);
                    $data->updated_by = Auth::user()->username;

                    if ($data->save()) {
                        if (isset($param['staff_id'])) {
                            $map = MapNote::where('note_id', $plainId);
                            $map->forceDelete();
                            foreach ($param['staff_id'] as $value) {
                                MapNote::create([
                                    'note_id' => $plainId,
                                    'staff_id' => $value
                                ]);
                            }
                        }

                        $response = new Response(true, __('Note updated successfuly'), 1);
                        $response->setRedirect(route('master.note.index'));

                        $this->writeAppLog($this->_update, 'Note : ' . $param['ma_id']);
                    } else {
                        $response = new Response(false, __('Note update failed. Please try again'));
                    }
                } else {
                    $data = Note::find($plainId);
                    if (isset($param['year'])) $data->year = $param['year'];
                    if (isset($param['division_id'])) $data->division_id = $param['division_id'];
                    if (isset($param['ma_id'])) $data->ma_id = $param['ma_id'];
                    if (isset($param['note_reff'])) $data->note_reff = $param['note_reff'];
                    if (isset($param['note_date'])) $data->note_date = $param['note_date'];
                    if (isset($param['note_upload'])) $data->note_upload = $param['note_upload'];
                    if (isset($param['program'])) $data->program = $param['program'];
                    if (isset($param['regarding'])) $data->regarding = $param['regarding'];
                    if (isset($param['link_url'])) $data->link_url = $param['link_url'];
                    if (isset($param['amount'])) $data->amount = $param['amount'];
                    if (isset($param['amount_requested'])) $data->amount_requested = $param['amount_requested'];
                    if (isset($param['amount_approved'])) $data->amount_approved = $param['amount_approved'];
                    if (isset($param['status'])) $data->status = $param['status'];
                    if (isset($param['staff_id']) && !is_array($param['staff_id'])) $param['staff_id'] = array($param['staff_id']);
                    $data->updated_by = Auth::user()->username;

                    if ($data->save()) {
                        if (isset($param['staff_id'])) {
                            $map = MapNote::where('note_id', $plainId);
                            $map->forceDelete();
                            foreach ($param['staff_id'] as $value) {
                                MapNote::create([
                                    'note_id' => $plainId,
                                    'staff_id' => $value
                                ]);
                            }
                        }

                        $response = new Response(true, __('Note updated successfuly'), 1);
                        $response->setRedirect(route('master.note.index'));

                        $this->writeAppLog($this->_update, 'Note : ' . $param['ma_id']);
                    } else {
                        $response = new Response(false, __('Note update failed. Please try again'));
                    }
                }
            }
        }

        if ($action === config('global.action.form.delete')) {
            if (!$this->hasPrivilege($this->_delete)) {
                $response = new Response(false, __('Sorry, You are not authorized for this action'), 2);
                return response()->json($response->responseJson());
            }

            $plainId = SecureHelper::unsecure($id);

            if (!$plainId) {
                $response = new Response();
                return response()->json($response->responseJson());
            }

            $data = Note::find($plainId);
            $data->is_trash = 1;

            if ($data->save()) {
                $response = new Response(true, __('Data deleted successfuly'), 1);
                $response->setRedirect(route('master.data.index'));

                $this->writeAppLog($this->_delete, 'Data Account : ' . $data->ma_id);
            } else {
                $response = new Response(false, __('Account delete failed. Please try again'));
            }
        }

        return response()->json($response->responseJson());
    }

    public function export($year)
    {
        $year = SecureHelper::unsecure($year);
        if (!$year) {
            $response = new Response();
            return response()->json($response->responseJson());
        }

        $data = Note::where('year', $year)->where('is_trash', 0)->orderBy('division_id', 'asc')->get()->toArray();
        
        $export = array();
        $division_flip = array_flip(config('global.division.code'));
        foreach ($data as $value) {
            $export[] = array(
                'year' => $value['years'],
                'ma' => $value['ma_id'],
                'description' => $value['program'],
                'pic' => $value['staff_export'],
                'amount' => $this->convertAmount($value['amount'], true),
                'unit' => Str::ucfirst($division_flip[$value['division_id']]),
                'reff' => $value['note_reff'],
                'date' => $value['note_date'],
                'upload' => $value['note_upload'],
                'regarding' => $value['regarding'],
                'link' => $value['link_url'],
                'requested' => $this->convertAmount($value['amount_requested'], true),
                'approved' => $this->convertAmount($value['amount_approved'], true),
                'percentage' => round(($this->convertAmount($value['amount_requested'], true)/$this->convertAmount($value['amount'], true))*100, 2),
                'diff' => $this->convertAmount($value['amount_requested'], true) - $this->convertAmount($value['amount_approved'], true),
                'remain' => $this->convertAmount($value['amount'], true) - $this->convertAmount($value['amount_approved'], true),
                'status' => $value['status_desc'],
            );
        }

        $filename = config('global.report.desc.masterdata') . ' ' . date('d F y') . '.xlsx';
        $excel = new ExcelWriter($filename, config('global.report.code.masterdata'), config('global.report.header.masterdata'), $export);
        $filepath = $excel->write();

        $param = SecureHelper::pack(['path' => $filepath, 'name' => $filename]);

        $response = new Response(true, 'Data successfuly exported', 1);
        $response->setRedirect(route('master.note.download', ['id' => $param]));

        return response()->json($response->responseJson());
    }

    public function download($id) {
        $param = SecureHelper::unpack($id);

        if (!is_array($param)) {
            return abort(404);
        }

        $headers = array(
            //'Content-Type: application/pdf',
            'Content-Disposition: attachment;filename=' . $param['name'],
            'Cache-Control: max-age=0',
            'Pragma: no-cache',
            'Expires: 0'
        );

        return response()->download($param['path'], $param['name'], $headers);
    }
}
