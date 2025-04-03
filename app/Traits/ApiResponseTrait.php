<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;

trait ApiResponseTrait
{
    protected function success($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function error($message, $code)
    {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->success(['data' => $message], $code);
    }

    /**
     * Return standardized JSON response
     * 
     * @param bool $status Success status
     * @param int $statusCode HTTP status code
     * @param string $message Response message
     * @param array|null $data Response data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function apiResponse($status, $statusCode, $message = '', $data = null)
    {
        $response = [
            'status' => $status,
            'statusCode' => $statusCode,
            'message' => $message
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        // $response['last_data_update'] = $this->getLastDataUpdate();

        return response()->json($response, $statusCode);
    }

    /**
     * Get the last data update timestamp from settings
     * 
     * @return string|null
     */
    protected function getLastDataUpdate()
    {
        try {
            return Setting::first()->value('last_data_update')->format('Y-m-d h:i:s') ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Return success response
     * 
     * @param array|null $data Response data
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, $message = 'Operation successful', $statusCode = 200)
    {
        return $this->apiResponse(true, $statusCode, $message, $data);
    }

    /**
     * Return error response
     * 
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array|null $data Additional error data
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = 'Operation failed', $statusCode = 400, $data = null)
    {
        return $this->apiResponse(false, $statusCode, $message, $data);
    }

    protected function successMessage($message, $errors, array $extra = [])
    {
        $response = [
            "success" => true,
            "status" => 200,
            "message" => $message,
        ];
        $response = array_merge($response, $extra);
        $response = $this->merge_error_response($response, $errors);

        return $this->success($response, 200);
    }

    protected function errorMessageWithError($message, $errors, array $extra = [])
    {
        $response = [
            "success" => false,
            "status" => 400,
            "message" => $message,
        ];
        $response = array_merge($response, $extra);

        $response = $this->merge_error_response($response, $errors);

        return $this->error($response, 400);
    }

    protected function merge_error_response(array $response, $errors)
    {
        if (!empty($errors->all_response())) {
            return $response = array_merge($response, ["error_response" => $errors->all_response()]);
        }
        return $response;
    }

    protected function checkSortBy($param, $checkArray)
    {
        if (!in_array($param, $checkArray)) {
            return $this->errorResponse('Invalid sort by parameter', 400);
        }
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse([], 'No records found', $code);
        }

        $collection = $this->searchData($collection); //search
        $collection = $this->sortData($collection); //sort if required
        $collection = $this->paginateData($collection); //paginate
        $collection = $this->cacheData($collection); //cache
        return $this->successResponse($collection, 'Records retrieved successfully', $code);
    }

    protected function getSortNPaginated(Builder $model, $code = 200)
    {
        if (request()->has('sort_order') && strtolower(request()->sort_order) == 'desc' && request()->has('sort_by')) {
            $model = $model->orderBy(request()->sort_by, 'desc');
        } elseif (request()->has('sort_by')) {
            $model = $model->orderBy(request()->sort_by, 'asc');
        }

        $rules = [
            'per_page' => 'integer|min:1|max:50',
        ];
        Validator::validate(request()->all(), $rules);
        $perPage = 15;
        if (request()->has('per_page')) {
            $perPage = (int)request()->per_page;
        }

        return  $model->paginate($perPage)->toArray();
    }

    protected function showOne($instance, $code = 200)
    {
        return $this->successResponse($instance, 'Record retrieved successfully', $code);
    }

    protected function sortData(Collection $collection)
    {
        if (request()->has('sort_order') && request()->sort_order == 'desc' && request()->has('sort_by')) {
            $collection = $collection->sortBy(request()->sort_by, SORT_REGULAR, true);
        } elseif (request()->has('sort_by')) {
            $collection = $collection->sortBy(request()->sort_by);
        }
        return $collection;
    }

    protected function paginateData(Collection $collection)
    {
        $validator = Validator::make(request()->all(), [
            'per_page' => 'integer|min:2|max:50',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 400);
        }

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        if (request()->has('per_page')) {
            $perPage = (int)request()->per_page;
        }
        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();
        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        $paginated->appends(request()->all());
        return $paginated;
    }

    /**
     * Cache data for 30 secs based on url
     * remember we are getting transformed data that is an array
     */
    protected function cacheData($data)
    {
        $url = request()->url();
        //sort the query params so we get a unique param
        //irrespective of order of query params
        $queryParams = request()->query();
        ksort($queryParams);
        $queryString = http_build_query($queryParams);
        $cacheKey = "{$url}?{$queryString}";
        return Cache::remember($cacheKey, 30, function () use ($data) {
            return $data;
        });
    }

    /**
     * Cache data for 30 secs based on url
     * remember we are getting transformed data that is an array
     */
    protected function searchData(Collection $collection)
    {
        if (request()->has('search') && !empty(request()->search)) {
            //$collection->search(request()->search);
            // $collection->search(function($item, $key) {
            //     return $item == request()->search;
            // });
        }
        return $collection;
    }
}
