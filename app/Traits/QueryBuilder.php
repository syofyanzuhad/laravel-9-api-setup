<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait QueryBuilder
{
    use ApiResponser;

    public function queryBuilder($subject, $column)
    {
        if (is_subclass_of($subject, Model::class)) {
            $subject = $subject::query();
        }

        if (request()->has('search') && request()->get('search') != '') {
            // if $column is array
            if (is_array($column)) {
                foreach ($column as $col) {
                // if $column has a dot, then it's a relation
                    if (strpos($col, '.') !== false) {
                        $subject->orWhereHas(
                            explode('.', $col)[0],
                            function ($query) use ($col, $subject) {
                                $query->where(
                                    explode('.', $col)[1],
                                    'like',
                                    '%' . request()->get('search') . '%'
                                );
                            }
                        );
                    } else {
                        $subject->orWhere($col, 'like', '%' . request()->get('search') . '%');
                    }
                }
            } else {
                // if $column has a dot, then it's a relation
                if (strpos($column, '.') !== false) {
                    $subject = $subject->whereHas($column, function ($query) {
                        $query->where('name', 'like', '%' . request()->get('search') . '%');
                    });
                } else {
                    $subject = $subject->where($column, 'like', '%' . request()->get('search') . '%');
                }
            }
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

    public function queryPaginate($subject, String $nama = 'berikut', $resource = null)
    {
        // paginate handling
        if (request()->has('per_page') && request()->get('per_page') > 0) {
            $collection = $subject->paginate(request()->get('per_page'));
            $collection->each(function ($collect, $index) use ($collection) {
                $collect->nomor = $collection->perPage() * ($collection->currentPage() - 1) + $index + 1;
            });
        } else {
            $collection = $subject->get();
        }

        if (count($collection) > 0 || (isset($collection->data) && count($collection->data) > 0)) {
            if ($resource == null) {
                // collection result
                $data = $this->successResponse($collection, $nama.' berhasil ditampilkan', 200);
            } else {
                // resource result
                $data = $this->responseWithResourceCollection($resource::collection($collection), $nama.' berhasil ditampilkan', 200);
            }
        } else {
            $data = $this->notFoundResponse($nama);
        }

        return $data;
    }
}
