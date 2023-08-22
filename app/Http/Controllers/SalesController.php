<?php

namespace App\Http\Controllers;

use App\Jobs\SalesCsvProcess;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesController extends Controller
{
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

            foreach ($chunks as $key => $chunk) {
                $data = array_map('str_getcsv', $chunk);
                if ($key == 0) {
                    $header = $data[0];
                    unset($data[0]);
                    $header = array_map(function ($h) {
                        return str_replace(" ", "_", $h);
                    }, $header);
                }

                SalesCsvProcess::dispatch($data, $header);
            }

            return "stored";

        } else {
            return "please select file";
        }
    }
}
