<?php

namespace App\Repository\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


abstract class BaseRepository implements BaseRepositoryInterface
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }


    /**
     * Get all records.
     */
    public function all()
    {
        return $this->model->all();
    }

    public function list(array $search = [], array $relations = [], string $sortByColumn = 'updated_at', string $sortBy = 'DESC')
    {

        if($relations) {
            $this->model = $this->model->with($relations);
        }

        return $this->model->filter($search)->orderBy($sortByColumn, $sortBy)->paginate(request('limit') ?? 10);
    }

    /**
     * Find a record by its ID.
     */
    public function find($id, $with = []): ?Model
    {
       $record = $this->model->with($with)->find($id);
        if ($record) {
            return $record;
        } else{
            $this->notFound();
        }
    }

    /**
     * Create a new record.
     */
    public function create(array $data): Model
    {
        try {
            DB::beginTransaction();
            $record = $this->model->create($data);
            DB::commit();
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'creation_error' => "Failed to create record: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Update an existing record.
     */
    public function update($id, array $data): Model
    {
        try {
            DB::beginTransaction();
            $record = $this->find($id);
            $record->update($data);
            DB::commit();
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            throw ValidationException::withMessages([
                'update_error' => "Failed to update record: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a record by its ID.
     */
    public function delete( $id)
    {
        $model = $this->model->find($id);
        if ($model) {
            $model->delete();
        } else{
            $this->notFound();
        }
    }

        public function restore( $id)
    {
        $model = $this->model->onlyTrashed()->find($id);
        if ($model) {
            return $model->restore();
        } else{
            $this->notFound();
        }
    }

    public function notFound(){
        throw ValidationException::withMessages([
            'record_not_found' => "Record not found"
        ]);
    }

    public function getOptions(): array
    {
        return $this->model->select('type')->distinct()->pluck('type')->toArray();
    }
}