<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table =  'transactions';

    protected $fillable = [
        'account_id',
        'txn_date',
        'tid',
        'txn_type',
        'amount',
        'sender_account_id',
        'receiver_account_id',
        'post_balance',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function sender()
    {
        return $this->belongsTo(Account::class, 'sender_account_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Account::class, 'receiver_account_id');
    }

    public function getTxnTypeAttribute($value)
    {
        $txn_types = ['deposit', 'withdrawal', 'transfer'];
        return $txn_types[$value];
    }

    public function setTxnTypeAttribute($value)
    {
        $txn_types = ['deposit', 'withdrawal', 'transfer'];
        $this->attributes['txn_type'] = array_search($value, $txn_types);
    }

    public function getTxnDateAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function setTxnDateAttribute($value)
    {
        $this->attributes['txn_date'] = date('Y-m-d H:i:s', strtotime($value));
    }

    public function getTidAttribute($value)
    {
        return strtoupper($value);
    }

    public function setTidAttribute($value)
    {
        $this->attributes['tid'] = strtoupper($value);
    }

    public function getAmountAttribute($value)
    {
        return number_format($value, 2);
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = number_format($value, 2);
    }

    public function getPostBalanceAttribute($value)
    {
        return number_format($value, 2);
    }

    public function setPostBalanceAttribute($value)
    {
        $this->attributes['post_balance'] = number_format($value, 2);
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function getDeletedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    
}
