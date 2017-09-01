<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class NotifyDeliver extends Model
{
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'notify_deliver';
}
