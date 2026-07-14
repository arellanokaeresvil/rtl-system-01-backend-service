<?php 

namespace App\Services\Report;

interface ReportServiceInterface
{
    public function summary();
    public function generated();
    public function generatedDetails($id);
}
