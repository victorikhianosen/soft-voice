<?php

namespace App\Http\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

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
        int|string $page = 1,
        int $perPage = 50,
        array $conditions = [],
        string $order = 'created_at',
        string $dir = 'desc',
    ): mixed
    {
        $data = $this->model->where($conditions);
        if ($page === '*') {
            return $data->orderBy($order, $dir)->get();
        }
        $total = $data->count();
        return [
            'data'  => $data->orderBy($order, $dir)->offset(($page - 1) * $perPage)->limit($perPage)->get(),
            'total' => $total
        ];
    }

    /**
     * Get the last saved model
     *
     * @param array $columns
     * @return Model
     */
    public function getLatest(array $conditions, array $columns = ['*']): Model
    {
        return $this->model
            ->where($conditions)
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
     * Get all entries
     *
     * @param array $conditions
     * @return Collection
     */
    public function all(array $conditions = []): Collection
    {
        return $this->model->where($conditions)->latest()->get();
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
    public function update(Model|int $model, array $data): bool
    {
        if ($model instanceof Model) {
            return $model->update($data);
        }
        return $this->find($model)->update($data);
    }

    /**
     * Delete a model by id
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(Model|int $model): bool
    {
        if ($model instanceof Model) {
            return $model->delete();
        }
        return $this->find($model)->delete();
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

    /**
     * Find all entries by conditions
     *
     * @param integer $page
     * @param integer $perPage
     * @param string $query
     * @param array $conditions
     * @param string $order
     * @param string $dir
     * @param array $filter
     * @return mixed
     */
    public function findAll(
        int $page = 1,
        int $perPage = 50,
        string $query = '',
        array $conditions = [],
        string $order = 'created_at',
        string $dir = 'desc',
        array $filter = [],
        array $with = [],
    ): mixed
    {
        $status = $filter['status'] ?? null;
        $date = $filter['date'] ?? null;
        $customers = $filter['customer'] ?? null;
        $data = $this->model->where($conditions)
                            ->with($with)
                            ->when($status, fn($q) => $q->whereIn('status', $status))
                            ->when($customers, fn($q) => $q->whereIn('customer_id', $customers))
                            ->when($date && isset($date['mode']), function ($q) use ($date) {
                                $q->when($date['mode'] === "range", function ($q) use ($date) {
                                    $q->when(isset($date['range']['from']) && $date['range']['from'],
                                        fn($q) => $q->whereDate('created_at', '>=', $date['range']['from'])
                                    )->when(isset($date['range']['to']) && $date['range']['to'],
                                        fn($q) => $q->whereDate('created_at', '<=',  $date['range']['to'])
                                    );
                                });
                                $q->when(($date['mode'] === "fixed" && isset($date['fixed']) && $date['fixed']), function ($q) use ($date) {
                                    $today = Carbon::today();
                                    $q->when($date['fixed'] === 'today', fn($q) => $q->whereDate('created_at', $today));
                                    $q->when($date['fixed'] === 'yesterday', fn($q) => $q->whereDate('created_at', $today->subDay()));
                                    $q->when($date['fixed'] === 'last_7_days', function ($q) use ($today) {
                                        $q->whereDate('created_at', '<=',  $today)
                                            ->whereDate('created_at', '>=', $today->subDays(7));
                                    });
                                    $q->when($date['fixed'] === 'this_month', fn($q) => $q->whereMonth('created_at', $today->month));
                                    $q->when($date['fixed'] === 'last_month', fn($q) => $q->whereMonth('created_at', $today->subMonth()->month));
                                });
                            })
                            ->when($query, function ($q) use ($query) {
                                $q->search($query);
                            })
                            ->orderBy($order, $dir);
        $total = $data->count();
        return [
            'data'  => $data->offset(($page - 1) * $perPage)->limit($perPage)->get(),
            'total' => $total
        ];
    }
}
