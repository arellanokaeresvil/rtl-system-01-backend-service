<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportServiceInterface;
use App\Services\Utils\ResponseServiceInterface;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $reportService;
    private $responseService;
    private $name = 'Report';

    public function __construct(ReportServiceInterface $reportService, ResponseServiceInterface $responseService)
    {
        $this->reportService = $reportService;
        $this->responseService = $responseService;
    }

    public function summary(Request $request)
    {
        $data = $this->reportService->summary();
        return $this->responseService->successResponse($this->name, $data);
    }

    public function generated(Request $request)
    {
        $data = $this->reportService->generated();
        return $this->responseService->successResponse($this->name, $data);
    }

    public function generatedDetails(Request $request, $id)
    {
        $data = $this->reportService->generatedDetails($id);
        return $this->responseService->successResponse($this->name, $data);
    }
}
