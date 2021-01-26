<?php
namespace App\Http\Traits;

use Facade\Ignition\QueryRecorder\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait ApiResponder {
    /**
     * Returns a collection json response.
     *
     * @param Builder $builder
     * @param int $code
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiRespond(Builder $builder, $code = 200, $message = '')
    {
        $per_page = (request()->per_page) ? request()->per_page : '10';
        $order_by = (request()->order_by) ? request()->order_by : 'created_at';
        $order_dir = (request()->order_dir) ? request()->order_dir : 'DESC';

        $collection = $builder->orderBy($order_by, $order_dir)
            ->paginate($per_page);

        return response()->json(array_merge([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
        ], $collection->toArray()));
    }

    /**
     * Returns all entries from a collection as json.
     *
     * @param Collection $collection
     * @param int $code
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiRespondAll(Collection $collection, $code = 200, $message = '')
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
            'data' => $collection->toArray()
        ]);
    }

    /**
     * Returns a single instance json response.
     *
     * @param Model $model
     * @param int $code
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiRespondSingle(Model $model, $code = 200, $message = '')
    {
        return response()->json(array_merge([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
        ], $model->toArray()));
    }

    /**
     * Returns a message json response.
     *
     * @param int $code
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiRespondMessage($code = 200, $message = '')
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
        ]);
    }

    /**
     * Returns a json formatted error.
     *
     * @param $code
     * @param $message
     * @return void
     */
    public function apiRespondError($code, $message)
    {
        return abort($code, $message);
    }
}
