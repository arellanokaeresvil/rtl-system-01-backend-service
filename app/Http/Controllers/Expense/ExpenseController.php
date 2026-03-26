<?php

namespace App\Http\Controllers\Expense;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\ExpenseRequest;
use App\Http\Resources\Expense\ExpenseResource;
use App\Services\Utils\ResponseServiceInterface;
use App\Http\Resources\Expense\ExpenseCollection;
use App\Repository\Expense\ExpenseRepositoryInterface;
use App\Services\Expense\ExpenseServiceInterface;

class ExpenseController extends Controller
{
    private $expenseRepository;
    private $expenserServices;
    private $responseService;
    private $name = 'Expense';

    public function __construct(ExpenseRepositoryInterface $expenseRepository, ResponseServiceInterface $responseService, ExpenseServiceInterface $expenserServices)
    {
        $this->expenseRepository = $expenseRepository;
        $this->responseService = $responseService;
        $this->expenserServices = $expenserServices;
    }

    public function index()
    {
        $search = request('search', null); 
        $expenses = $this->expenseRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new ExpenseCollection($expenses));
    }
    public function summary()
    {
       
        $expenses = $this->expenserServices->summary();
        return $this->responseService->successResponse($this->name,$expenses);
    }

    public function show($id)
    {
        $expense= $this->expenseRepository->find($id);
        return $this->responseService->showResponse($this->name, new ExpenseResource($expense));
    }

    public function store(ExpenseRequest $request)
    {
        $expense = $this->expenseRepository->create($request->validated());
        return $this->responseService->storeResponse($this->name, new ExpenseResource($expense));
    }

    public function update(ExpenseRequest $request, $id)
    {
        $expense = $this->expenseRepository->update($id, $request->validated());
        return $this->responseService->updateResponse($this->name, new ExpenseResource($expense));
    }

        public function destroy($id)
    {
        $this->expenseRepository->delete($id);
        return $this->responseService->deleteResponse($this->name);
    }

    public function restore($id)
    {
        $expenseCategory = $this->expenseRepository->restore($id);
        return $this->responseService->restoreResponse($this->name, new ExpenseResource($expenseCategory));
    }

    // public function getOptions()
    // {
    //     $options = $this->expenseRepository->Options();
    //     return $this->responseService->successResponse($this->name,  new ExpenseCategoryOptionResource($options));
    // }
}