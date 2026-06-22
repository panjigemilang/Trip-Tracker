<?php

namespace App\Imports;

use App\Models\Trip;
use App\Models\Activity;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class TripImport implements ToCollection, WithHeadingRow
{
    protected $trip;
    public $errors = [];

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), [
            '*.date' => [
                'required',
                'date',
                'after_or_equal:' . $this->trip->start_date->toDateString(),
                'before_or_equal:' . $this->trip->end_date->toDateString(),
            ],
            '*.time' => 'required|date_format:H:i',
            '*.title' => 'required|string|max:255',
            '*.location' => 'nullable|string|max:500',
            '*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return;
        }

        $sortOrder = $this->trip->activities()->max('sort_order') ?? 0;

        foreach ($rows as $row) {
            $sortOrder++;
            $date = Carbon::parse($row['date'])->format('Y-m-d');
            
            // Format time correctly depending on how Excel parsed it
            $timeStr = $row['time'];
            if (is_numeric($timeStr)) {
                 // Excel time fraction
                 $time = Carbon::createFromTimestampUTC(round($timeStr * 86400))->format('H:i:s');
            } else {
                 $time = Carbon::parse($timeStr)->format('H:i:s');
            }

            Activity::create([
                'trip_id' => $this->trip->id,
                'date' => $date,
                'time' => $time,
                'title' => $row['title'],
                'location' => $row['location'],
                'notes' => $row['notes'],
                'sort_order' => $sortOrder,
            ]);
        }
    }
}
