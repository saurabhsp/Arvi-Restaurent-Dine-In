<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['category_id','name','description','price','image_path','is_available','sort_order'];
    protected $casts = ['is_available'=>'boolean','price'=>'decimal:2'];
    public function category() { return $this->belongsTo(Category::class); }
}
