<?php

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository
{
    public function __construct(protected Model $model)
    {
    }

    public abstract function model();

    /**
     * Gets all model
     *
     * @param array $columns
     * @param integer $limit
     * @param string $order
     * @param string $dir
     * @return mixed
     */
    public function getAll(
        array $columns = ['*'],
        int $limit = 50,
        string $order = 'created_at',
        string $dir = 'desc'
    ): mixed
    {
        return $this->model->orderBy($order, $dir)->paginate($limit, $columns);
    }

    /**
     * Get the last saved model
     *
     * @param array $columns
     * @return Model
     */
    public function getLatest(array $columns = ['*']): Model
    {
        return $this->model
            ->latest()
            ->first($columns);
    }

    /**
     * Find a model by ID
     *
     * @param integer $id
     * @param array $columns
     * @return Model
     */
    public function find(int $id, array $columns = ['*']): Model
    {
        return $this->model->find($id, $columns);
    }

    /**
     * Create a new model
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        $class = get_class($this->model);
        return $class::create($data);
    }

    /**
     * Updates a model by id
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function update(int $id, array $data): bool
    {
        return $this->find($id)->update($data);
    }

    /**
     * Delete a model by id
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $class = get_class($this->model);
        return $class::destroy($id);
    }

    /**
     * Restore a model by id
     *
     * @param integer $id
     * @return boolean
     */
    public function restore(int $id): bool
    {
        $class = get_class($this->model);
        return $class::onlyTrashed()->where('id', $id)->restore();
    }

    /**
     * Permanently delete a model by id
     *
     * @param integer $id
     * @return boolean
     */
    public function forceDelete(int $id): bool
    {
        $class = get_class($this->model);
        return $class::onlyTrashed()->where('id', $id)->forceDelete();
    }
}
