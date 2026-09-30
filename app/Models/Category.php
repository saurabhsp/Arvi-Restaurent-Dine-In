<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    protected $fillable = ['name','name_mr','description','description_mr','sort_order','is_active'];
    public function getDisplayNameAttribute(): string { return app()->getLocale()==='mr' && $this->name_mr ? $this->name_mr : $this->name; }
    public function getDisplayDescriptionAttribute(): ?string { return app()->getLocale()==='mr' && $this->description_mr ? $this->description_mr : $this->description; }
    protected $casts = ['is_active'=>'boolean'];
    public function products() { return $this->hasMany(Product::class); }
}
