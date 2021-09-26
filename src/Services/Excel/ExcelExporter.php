<?php

namespace Framework\Baseapp\Services\Excel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExporter extends AbstractExporter
{
    /**
     * {@inheritdoc}
     */
    public function export()
    {
        Excel::create($this->getTable().date('YmdHis'), function ($excel) {
            $excel->sheet($this->getTable(), function ($sheet) {
                $titles = [];

                $this->chunk(function ($records) use ($sheet, &$titles) {
                    if (empty($titles)) {
                        $titles = $this->getHeaderRowFromRecords($records);
                        $sheet->prependRow($titles);
                    }
                    $rows = collect($records->toArray())->map(function ($item) {
                        $return = [];
                        foreach ($item as $in => $it) {
                            if (is_array($it)) {
                                $return[$in] = Arr::isAssoc($it) ? implode(',', $it) : json_encode($it);
                            } else {
                                $return[$in] = $it;
                            }
                        }

                        return $return;
                    });
                    $sheet->rows($rows);
                });
            });
        })->export('xlsx');
    }

    public function getHeaderRowFromRecords(Collection $records): array
    {
        $titles = collect(array_dot($records->first()->toArray()))->keys()->map(
            function ($key) {
                $key = str_replace('.', ' ', $key);

                return Str::ucfirst($key);
            }
        );

        return $titles->toArray();
    }

    public function getFormattedRecord(Model $record)
    {
        return array_dot($record->getAttributes());
    }
}
