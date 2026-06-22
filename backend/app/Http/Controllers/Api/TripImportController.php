<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Imports\TripImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class TripImportController extends Controller
{
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="trip_activities_template.csv"',
        ];

        $content = "date,time,title,location,notes\n";
        $content .= "2026-12-30,14:00,Jalan jalan,Jakarta,Meet driver at exit\n";
        
        return Response::make($content, 200, $headers);
    }

    public function import(Request $request, Trip $trip)
    {
        if ($trip->user_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        $import = new TripImport($trip);

        DB::beginTransaction();
        try {
            Excel::import($import, $request->file('file'));

            if (!empty($import->errors)) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $import->errors
                ], 422);
            }

            DB::commit();

            return response()->json([
                'message' => 'Activities imported successfully',
                'data' => $trip->load('activities')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to import activities: ' . $e->getMessage()
            ], 500);
        }
    }
}
