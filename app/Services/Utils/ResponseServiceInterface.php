<?php

namespace App\Services\Utils;

interface ResponseServiceInterface
{
    public function authResponse($message, $data, $cookie);
    public function resolveResponse($message, $data);
    public function rejectResponse($message, $data);
    public function duplicateResponse($message, $data);

    public function successResponse($model, $data);
    public function showResponse($model, $data);
    public function storeResponse($model, $data);
    public function updateResponse($model, $data);
    public function deleteResponse($model);
    public function restoreResponse($model);
    public function disabledResponse($model, $data);
}