<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait QueryBuilder
{
    use ApiResponser;

    public function queryBuilder($subject, ...$column)
    {
        if (is_subclass_of($subject, Model::class)) {
            $subject = $subject::query();
        }

        if (request()->has('search') && request()->get('search') != '') {
            $subject = $subject->where(function ($query) use ($column) {
                foreach ($column as $col) {
                    $query->orWhere($col, 'like', '%'.request()->get('search').'%');
                }
            });
        }

        if (request()->has('except') && request()->get('except') > 0) {
            $subject = $subject->whereNotIn('id', [request()->get('except')]);
        }
        if (request()->has('sort') && request()->get('sort') != '' && request()->has('order') && request()->get('order') != '') {
            $subject = $subject->orderBy(request()->get('sort'), request()->get('order'));
        }
        if (request()->has('limit') && request()->get('limit') > 0) {
            $subject->take(request()->get('limit'));
        }

        return $subject;
    }

    public function queryPaginate($subject, $data = 'berikut', $resource = null)
    {
        // paginate handling
        if (request()->has('per_page') && request()->get('per_page') > 0) {
            $collection = $subject->paginate(request()->get('per_page'));
        } else {
            $collection = $subject->get();
        }

        if (count($collection) > 0 || (isset($collection->data) && count($collection->data) > 0)) {
            if ($resource == null) {
                // collection result
                $data = $this->successResponse($collection, $data.' berhasil ditampilkan', 200);
            } else {
                // resource result
                $data = $this->responseWithResourceCollection($resource::collection($collection), $data.' berhasil ditampilkan', 200);
            }
        } else {
            $data = $this->notFoundResponse($data);
        }

        return $data;
    }
}
