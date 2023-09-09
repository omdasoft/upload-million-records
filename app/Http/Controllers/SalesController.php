<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\View\View;
use App\Jobs\ExportSalesJob;
use Illuminate\Http\Request;
use App\Exports\SalessExport;
use App\Jobs\MergeExcelFiles;
use App\Jobs\SalesCsvProcess;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;

class SalesController extends Controller
{
    const PER_PAGE = 50;
    public function index(Request $request)
    {
        $perPage = isset($request->perPage) && !empty($request->perPage) ? $request->perPage : PER_PAGE;
        $sales = Sales::latest()->paginate($perPage);
        $out = ['metadata' => '', 'data' => ''];
        if ($sales) {
            $metadata = [
                'current_page' => $sales->currentPage(),
                'last_page' => $sales->lastPage(),
                'total' => $sales->total(),
                'next_page_url' => $sales->nextPageUrl(),
                'previous_page_url' => $sales->previousPageUrl(),
                'form' => $sales->firstItem(),
                'to' => $sales->lastItem(),
            ];

            $out['metadata'] = $metadata;
            $out['data'] = $sales->getCollection();
        }

        return response()->json($out);
    }

    public function create(): View
    {
        return view('upload-file');
    }

    public function upload(Request $request)
    {
        if ($request->has('mycsv')) {
            $data = file($request->mycsv);
            $chunks = array_chunk($data, 1000);
            //$path = resource_path('temp');

            //    foreach($chunks as $key => $chunk) {
            //         $name = "/temp{$key}.csv";
            //         file_put_contents($path.$name, $chunk);
            //    }

            //    $files = glob("$path/*.csv");
            $header = [];
            $batch = Bus::batch([])->dispatch();
            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);
                if ($key == 0) {
                    $header = $data[0];
                    unset($data[0]);
                    $header = array_map(function ($h) {
                        return str_replace(" ", "_", $h);
                    }, $header);
                }

                $batch->add(new SalesCsvProcess($data, $header));
            }

            return $batch;

        } else {
            return "please select file";
        }
    }

    public function batch()
    {
        $batchId = request('id');
        return Bus::findBatch($batchId);
    }

    public function inProgressBatches()
    {
        $batches = \DB::table('job_batches')->where('pending_jobs', '>', 0)->get();
        if (count($batches) > 0) {
            return Bus::findBatch($batches[0]->id);
        }

        return [];
    }
}
