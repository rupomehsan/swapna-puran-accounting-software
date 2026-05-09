<?php

namespace Modules\Management\Deposit\Actions;

class GetAllData
{
    static $model = \Modules\Management\Deposit\Database\Models\Model::class;

    public static function execute()
    {
        try {
            $pageLimit     = request()->input('limit') ?? 10;
            $orderByColumn = request()->input('sort_by_col') ?? 'id';
            $orderByType   = request()->input('sort_type') ?? 'desc';
            $status        = request()->input('status') ?? 'active';
            $start_date    = request()->input('start_date');
            $end_date      = request()->input('end_date');

            $with = [
                'member:id,name,email',
                'due:id,due_amount,paid_amount,remaining_amount,payment_status,for_month',
            ];

            $query = self::$model::query()->with($with);

            // Members see only their own deposits
            if (auth()->user()?->role_id === 2) {
                $query->where('user_id', auth()->id());
            } elseif (request()->filled('user_id')) {
                $query->where('user_id', request()->input('user_id'));
            }

            if (request()->filled('deposit_type')) {
                $query->where('deposit_type', request()->input('deposit_type'));
            }

            if (request()->filled('search')) {
                $searchKey = request()->input('search');
                $query->where(function ($q) use ($searchKey) {
                    $q->where('voucher_no', 'like', '%' . $searchKey . '%')
                      ->orWhere('deposit_type', 'like', '%' . $searchKey . '%')
                      ->orWhere('payment_method', 'like', '%' . $searchKey . '%')
                      ->orWhere('note', 'like', '%' . $searchKey . '%')
                      ->orWhereHas('member', function ($mq) use ($searchKey) {
                          $mq->where('name', 'like', '%' . $searchKey . '%')
                             ->orWhere('phone', 'like', '%' . $searchKey . '%');
                      });
                });
            }

            if ($start_date && $end_date) {
                if ($end_date == $start_date) {
                    $query->whereDate('payment_date', $start_date);
                } else {
                    $query->whereBetween('payment_date', [$start_date, $end_date]);
                }
            }

            if ($status === 'trashed') {
                $query->onlyTrashed();
            } else {
                $query->where('status', $status);
            }

            $query->orderBy($orderByColumn, $orderByType);

            if (request()->input('get_all') == 1) {
                $data = $query->limit($pageLimit)->get();
                return entityResponse($data);
            }

            $data = $query->paginate($pageLimit);

            return entityResponse([
                ...$data->toArray(),
                'active_data_count'   => self::$model::active()->count(),
                'inactive_data_count' => self::$model::inactive()->count(),
                'trashed_data_count'  => self::$model::onlyTrashed()->count(),
            ]);
        } catch (\Exception $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
