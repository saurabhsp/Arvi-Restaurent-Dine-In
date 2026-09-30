<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $fillable = ['category_id','name','name_mr','description','description_mr','price','image_path','is_available','sort_order'];
    public function getDisplayNameAttribute(): string { return app()->getLocale()==='mr' && $this->name_mr ? $this->name_mr : $this->name; }
    public function getDisplayDescriptionAttribute(): ?string { return app()->getLocale()==='mr' && $this->description_mr ? $this->description_mr : $this->description; }
    protected $casts = ['is_available'=>'boolean','price'=>'decimal:2'];
    public function category() { return $this->belongsTo(Category::class); }
}
