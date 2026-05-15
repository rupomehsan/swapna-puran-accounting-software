<?php

namespace Modules\Management\ShareAdjustment\Database\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Model extends EloquentModel
{
    use HasFactory, SoftDeletes;

    protected $table = "share_adjustments";
    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($data) {
            $random_no = random_int(100, 999) . $data->id . random_int(100, 999);
            $slug = 'share-adj ' . $random_no;
            $data->slug = Str::slug($slug);
            if (strlen($data->slug) > 50) {
                $data->slug = substr($data->slug, strlen($data->slug) - 50, strlen($data->slug));
            }
            if (auth()->check()) {
                $data->creator = auth()->user()->id ?? null;
            }
            $data->save();
        });
    }

    public function member()
    {
        return $this->belongsTo(\Modules\Management\UserManagement\User\Database\Models\Model::class, 'user_id');
    }

    public function deposit()
    {
        return $this->belongsTo(\Modules\Management\Deposit\Database\Models\Model::class, 'linked_deposit_id');
    }

    public function withdrawal()
    {
        return $this->belongsTo(\Modules\Management\Withdrawal\Database\Models\Model::class, 'linked_withdrawal_id');
    }
}
