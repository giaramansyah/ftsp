<?php

namespace App\Library;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelWriter
{
    private $_filename;
    private $_type;
    private $_header;
    private $_data;
    private $_style;
    private $_columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    public function __construct($filename, $type, $header, $data)
    {
        $this->_filename = $filename;
        $this->_type = $type;
        $this->_header = $header;
        $this->_data = $data;
        $this->_style = config('global.report.excel');
    }

    public function write()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = new Worksheet($spreadsheet);
        $spreadsheet->addSheet($sheet, 0);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if ($this->_type == config('global.report.code.accountability')) {
            $this->accountability($sheet);
        } else if ($this->_type == config('global.report.code.accountability_fakultas')) {
            $this->accountability_fakultas($sheet);
        } else {
            $this->accountability_umd($sheet);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $descMonth = config('global.months');
        $today = Carbon::now();
        $year = $today->year;
        $month = $today->month;
        $month = $month < 10 ? '0' . $month : $month;

        $pathYear = public_path('download') . '/' . $year;
        $pathMonth = public_path('download') . '/' . $year . '/' . $descMonth[$month];

        if (!File::exists($pathYear)) {
            File::makeDirectory($pathYear, 0777, true, true);
        }

        if (!File::exists($pathMonth)) {
            File::makeDirectory($pathMonth, 0777, true, true);
        }

        $filepath = $pathMonth . '/' . $this->_filename;
        $writer = new Xlsx($spreadsheet);
        $writer->save($filepath);

        return $filepath;
    }

    private function accountability(Worksheet $sheet)
    {
        $row = 1;
        $sheet->setCellValue($this->_columns[1] . $row, 'LAPORAN PERTANGGUNG JAWABAN KAS-UMD');
        $sheet->getStyle($this->_columns[1] . $row)->applyFromArray($this->_style['title']);

        $row++;
        $sheet->setCellValue($this->_columns[1] . $row, $this->_data['header']);
        $sheet->getStyle($this->_columns[1] . $row)->applyFromArray($this->_style['subtitle']);

        $row++;
        $sheet->setCellValue($this->_columns[1] . $row, 'TANGGAL : ' . $this->_data['report_date']);
        $sheet->getStyle($this->_columns[1] . $row)->applyFromArray($this->_style['subtitle']);

        $row++;
        $row++;
        $width = [34, 165, 71, 79, 454, 100, 100, 100, 100, 100, 50];
        foreach ($this->_header as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['header']);
            $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($this->_columns[$key])->setWidth($width[$key], 'px');
        }

        $row++;
        $values = ['', '', '', '', 'Saldo Awal', '', '', '', '', $this->_data['balance'], ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        $values = ['', '', '', '', '', '', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
        }

        $row++;
        if (!empty($this->_data['expense'])) {
            foreach ($this->_data['expense'] as $key => $value) {
                $i = 0;
                $sheet->setCellValue($this->_columns[$i] . $row, ($key + 1));
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                foreach ($value as $index => $val) {
                    $sheet->setCellValue($this->_columns[$i] . $row, $val);
                    $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                    if (in_array($index, ['reff_no', 'reff_date', 'ma_id'])) {
                        $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    } else if (in_array($index, ['description'])) {
                        $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setWrapText(true);
                    } else if (in_array($index, ['amount'])) {
                        $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    }

                    $i++;
                }

                $sheet->setCellValue($this->_columns[$i] . $row, $value['amount']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['amount']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                
                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $row++;
            }
        } else {
            $values = ['', '', '', '', 'Tanggal ' . $this->_data['report_date'] . ' tidak ada realisasi', '', '', '', '', '', ''];
            foreach ($values as $key => $value) {
                $sheet->setCellValue($this->_columns[$key] . $row, $value);
                $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            }

            $row++;
        }

        $values = ['', '', '', '', '', $this->_data['total_expense'], $this->_data['total_expense'], $this->_data['total_expense'], '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        $values = ['', '', '', '', '', '', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
        }

        $row++;
        $values = ['', '', '', '', 'Saldo Akhir', '', '', '', '', $this->_data['balance'], ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        $row++;
        $row++;
        $sheet->setCellValue($this->_columns[0] . $row, 'Mengetahui,');
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[3] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->applyFromArray($this->_style['footer']);

        $sheet->setCellValue($this->_columns[5] . $row, 'Jakarta, ' . $this->_data['report_date']);
        $sheet->mergeCells($this->_columns[5] . $row . ':' . $this->_columns[9] . $row);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->applyFromArray($this->_style['footer']);

        $row++;
        $sheet->setCellValue($this->_columns[0] . $row, 'Wakil Dekan II');
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[3] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->applyFromArray($this->_style['footer']);

        $sheet->setCellValue($this->_columns[5] . $row, 'Kasir FTSP');
        $sheet->mergeCells($this->_columns[5] . $row . ':' . $this->_columns[9] . $row);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->applyFromArray($this->_style['footer']);

        $row++;
        $row++;
        $row++;
        $sheet->setCellValue($this->_columns[0] . $row, $this->_data['knowing']);
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[3] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->getFont()->setBaseLine(true);

        $sheet->setCellValue($this->_columns[5] . $row, $this->_data['user']);
        $sheet->mergeCells($this->_columns[5] . $row . ':' . $this->_columns[9] . $row);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->getFont()->setBaseLine(true);
    }

    private function accountability_fakultas(Worksheet $sheet)
    {
        $row = 1;
        $sheet->setCellValue($this->_columns[1] . $row, 'LAPORAN PERTANGGUNG JAWABAN KAS-UMD');
        $sheet->getStyle($this->_columns[1] . $row)->applyFromArray($this->_style['title']);

        $row++;
        $sheet->setCellValue($this->_columns[1] . $row, $this->_data['header']);
        $sheet->getStyle($this->_columns[1] . $row)->applyFromArray($this->_style['subtitle']);

        $row++;
        $sheet->setCellValue($this->_columns[1] . $row, 'TANGGAL : ' . $this->_data['report_date']);
        $sheet->getStyle($this->_columns[1] . $row)->applyFromArray($this->_style['subtitle']);

        $row++;
        $row++;
        $width = [34, 165, 71, 79, 454, 70, 100, 100, 100, 100, 100, 100, 100, 50];
        foreach ($this->_header as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['header']);
            $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($this->_columns[$key])->setWidth($width[$key], 'px');
        }

        $row++;
        $values = ['', '', '', '', 'Saldo Akhir Tanggal ' . $this->_data['opening_balance_date'], '', $this->_data['opening_balance'], '', '', '', '', '', $this->_data['opening_balance'], ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            if (in_array($key, [6, 12])) {
                $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
        }

        $row++;
        $values = ['', '', '', '', 'PENERIMAAN :', '', '', '', '', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        if (!empty($this->_data['reception'])) {
            foreach ($this->_data['reception'] as $key => $value) {
                $i = 0;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['ma_id']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['description']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setWrapText(true);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['id']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['amount']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['amount']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $row++;
            }
        } else {
            $values = ['', '', '', '', 'Tanggal ' . $this->_data['report_date'] . ' tidak ada penerimaan', '', '', '', '', '', '', '', ''];
            foreach ($values as $key => $value) {
                $sheet->setCellValue($this->_columns[$key] . $row, $value);
                $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            }

            $row++;
        }

        $values = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
        }

        $row++;
        $values = ['', '', '', '', 'PENGELUARAN :', '', '', '', '', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        if (!empty($this->_data['expense'])) {
            foreach ($this->_data['expense'] as $key => $value) {
                $i = 0;
                $sheet->setCellValue($this->_columns[$i] . $row, ($key + 1));
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['reff_no']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['reff_date']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['ma_id']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['description']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setWrapText(true);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['id']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['amount']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, ($value['status'] == config('global.status.code.unfinished') ? '' : $value['amount']));
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, ($value['status'] == config('global.status.code.unfinished') ? '' : $value['amount']));
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, ($value['status'] == config('global.status.code.unfinished') ? '' : $value['amount']));
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, '');
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);

                $row++;
            }
        } else {
            $values = ['', '', '', '', 'Tanggal ' . $this->_data['report_date'] . ' tidak ada realisasi', '', '', '', '', '', '', '', ''];
            foreach ($values as $key => $value) {
                $sheet->setCellValue($this->_columns[$key] . $row, $value);
                $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            }

            $row++;
        }

        $values = ['', '', '', '', '', '', '', '', $this->_data['total_expense'], $this->_data['total_expense'], $this->_data['total_expense'], '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        $values = ['', '', '', '', '', '', $this->_data['total_reception'], $this->_data['total_expense'], '', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
        }

        $row++;
        $values = ['', '', '', '', 'Saldo Akhir Tanggal ' . $this->_data['closing_balance_date'], '', $this->_data['closing_balance'], '', '', '', '', '', $this->_data['closing_balance'], ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            if (in_array($key, [6, 11])) {
                $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
        }

        $row++;
        $row++;
        $row++;
        $sheet->setCellValue($this->_columns[0] . $row, 'Mengetahui,');
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[3] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->applyFromArray($this->_style['footer']);

        $sheet->setCellValue($this->_columns[5] . $row, 'Jakarta, ' . $this->_data['report_date']);
        $sheet->mergeCells($this->_columns[5] . $row . ':' . $this->_columns[9] . $row);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->applyFromArray($this->_style['footer']);

        $row++;
        $sheet->setCellValue($this->_columns[0] . $row, 'Wakil Dekan II');
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[3] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->applyFromArray($this->_style['footer']);

        $sheet->setCellValue($this->_columns[5] . $row, 'Kasir FTSP');
        $sheet->mergeCells($this->_columns[5] . $row . ':' . $this->_columns[9] . $row);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->applyFromArray($this->_style['footer']);

        $row++;
        $row++;
        $row++;
        $sheet->setCellValue($this->_columns[0] . $row, $this->_data['knowing']);
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[3] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[3] . $row)->getFont()->setBaseLine(true);

        $sheet->setCellValue($this->_columns[5] . $row, $this->_data['user']);
        $sheet->mergeCells($this->_columns[5] . $row . ':' . $this->_columns[9] . $row);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[5] . $row . ':' . $this->_columns[9] . $row)->getFont()->setBaseLine(true);
    }

    private function accountability_umd(Worksheet $sheet)
    {
        $row = 1;
        $lastkey = (count($this->_header) - 1);
        $sheet->setCellValue($this->_columns[1] . $row, 'LAPORAN UMD YANG BELUM DI PERTANGGUNG JAWABKAN');
        $sheet->mergeCells($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['title']);
        $sheet->getStyle($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue($this->_columns[1] . $row, 'TA ' . $this->_data['year']);
        $sheet->mergeCells($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['subtitle']);
        $sheet->getStyle($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $sheet->setCellValue($this->_columns[1] . $row, $this->_data['header']);
        $sheet->mergeCells($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['subtitle']);
        $sheet->getStyle($this->_columns[1] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $row++;
        $width = [34, 71, 79, 454, 200, 100];
        foreach ($this->_header as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['header']);
            $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setWrapText(true);
            $sheet->getColumnDimension($this->_columns[$key])->setWidth($width[$key], 'px');
        }

        $row++;
        $values = ['', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
        }

        $row++;
        if (!empty($this->_data['expense'])) {
            foreach ($this->_data['expense'] as $key => $value) {
                $i = 0;
                $sheet->setCellValue($this->_columns[$i] . $row, ($key + 1));
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['reff_date']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['ma_id']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['description']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setWrapText(true);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['name']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setWrapText(true);

                $i++;
                $sheet->setCellValue($this->_columns[$i] . $row, $value['amount']);
                $sheet->getStyle($this->_columns[$i] . $row)->applyFromArray($this->_style['body']);
                $sheet->getStyle($this->_columns[$i] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $row++;
            }
        } else {
            $lastkey = (count($this->_header) - 1);
            $sheet->setCellValue($this->_columns[0] . $row, 'Tidak Ada');
            $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row);
            $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $row++;
        }

        $values = ['', '', '', '', '', ''];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
        }

        $row++;
        $values = ['', '', '', 'JUMLAH', '', $this->_data['total_expense']];
        foreach ($values as $key => $value) {
            $sheet->setCellValue($this->_columns[$key] . $row, $value);
            $sheet->getStyle($this->_columns[$key] . $row)->applyFromArray($this->_style['body']);
            $sheet->getStyle($this->_columns[$key] . $row)->getFont()->setBold(true);
            if ($key == 3) {
                $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }

            if ($key == 5) {
                $sheet->getStyle($this->_columns[$key] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
        }

        $row++;
        $lastkey = (count($this->_header) - 1);
        $sheet->setCellValue($this->_columns[0] . $row, 'CATATAN : Dimohon segera Laporan Keuangan diserahkan ke Wadek II FTSP paling lambat tgl. 30 Desember 2022');
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $row++;
        $row++;
        $lastkey = (count($this->_header) - 1);
        $sheet->setCellValue($this->_columns[0] . $row, 'Jakarta, ' . $this->_data['report_date']);
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $lastkey = (count($this->_header) - 1);
        $sheet->setCellValue($this->_columns[0] . $row, 'Wakil Dekan II');
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $row++;
        $row++;
        $row++;
        $lastkey = (count($this->_header) - 1);
        $sheet->setCellValue($this->_columns[0] . $row, $this->_data['knowing']);
        $sheet->mergeCells($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->applyFromArray($this->_style['footer']);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getFont()->setBold(true);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getFont()->setBaseLine(true);
        $sheet->getStyle($this->_columns[0] . $row . ':' . $this->_columns[$lastkey] . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }
}
