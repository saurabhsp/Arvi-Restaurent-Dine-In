<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    protected $fillable = ['customer_name','customer_phone','payment_method','payment_status','status','total'];
    protected $appends = ['order_number'];
    public function items() { return $this->hasMany(OrderItem::class); }
    public function getOrderNumberAttribute(): string { return 'DN'.(1000 + $this->id); }
}
