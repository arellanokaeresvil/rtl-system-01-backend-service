<?php

namespace App\Http\Controllers\ExpenseCategory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Utils\ResponseService;
use App\Http\Requests\Expense\ExpenseCategoryRequest;
use App\Http\Resources\Expense\ExpenseCategoryResource;
use App\Http\Resources\Expense\ExpenseCategoryCollection;
use App\Http\Resources\Expense\ExpenseCategoryOptionResource;
use App\Repository\ExpenseCategory\ExpenseCategoryRepositoryInterface;

class ExpenseCategoryController extends Controller
{
    private $responseService;
    private $expenseCategoryRepository;
    private $name = 'Expense Category';

    public function __construct(ResponseService $responseService, ExpenseCategoryRepositoryInterface $expenseCategoryRepository)
    {
        $this->responseService = $responseService;
        $this->expenseCategoryRepository = $expenseCategoryRepository;  
    }

    public function index()
    {
        $search = request('search', null); 
        $expenseCategories = $this->expenseCategoryRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new ExpenseCategoryCollection($expenseCategories));
    }

    public function show($id)
    {
        $expenseCategory = $this->expenseCategoryRepository->find($id);
        return $this->responseService->showResponse($this->name, new ExpenseCategoryResource($expenseCategory));
    }

    public function store(ExpenseCategoryRequest $request)
    {
        $expenseCategory = $this->expenseCategoryRepository->create( $request->validated());
        return $this->responseService->storeResponse($this->name, new ExpenseCategoryResource($expenseCategory));
    }

    public function update(ExpenseCategoryRequest $request, $id)
    {
        $expenseCategory = $this->expenseCategoryRepository->update($id, $request->validated());
        return $this->responseService->updateResponse($this->name, new ExpenseCategoryResource($expenseCategory));
    }

    public function destroy($id)
    {
        $this->expenseCategoryRepository->delete($id);
        return $this->responseService->deleteResponse($this->name);
    }

    public function restore($id)
    {
        $expenseCategory = $this->expenseCategoryRepository->restore($id);
        return $this->responseService->restoreResponse($this->name, new ExpenseCategoryResource($expenseCategory));
    }

    public function getOptions()
    {
        $options = $this->expenseCategoryRepository->Options();
        return $this->responseService->successResponse($this->name,  new ExpenseCategoryOptionResource($options));
    }

}
