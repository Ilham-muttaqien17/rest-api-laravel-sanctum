<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success($data, $message = null, $code = 200, $paginate = false)
    {

        $response['status'] = true;
        $response['message'] = $message;
        $response['data'] = $data;
        if ($paginate) {
            $response['data'] = $data->items();
            $response['meta'] = [
                'current_page' => $data->currentPage(),
                'current_item' => $data->count(),
                'last_page' => $data->lastPage(),
                'next_page_url' => $data->nextPageUrl(),
                'path' => $data->path(),
                'per_page' => $data->perPage(),
                'prev_page_url' => $data->previousPageUrl(),
                'total_item' => $data->total(),
            ];
        }

        return response()->json(
            $response,
            $code
        );
    }

    protected function failed($message, $code)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);
    }

    protected function error($errors, $message, $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}
