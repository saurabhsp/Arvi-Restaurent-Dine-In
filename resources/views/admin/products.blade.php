@extends('layout')
@section('title',__('Products'))
@section('content')
@include('admin.nav')
<h1>{{ __('Products') }}</h1>
<div class="panel"><h2 id="form-title">{{ __('Add product') }}</h2>
    <form method="post" action="{{ route('admin.products.save') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="id" id="product-id">
        <label for="product-category">{{ __('Category') }}</label><select name="category_id" id="product-category" required><option value="">{{ __('Select category') }}</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->display_name }}</option>@endforeach</select>
        <label for="product-name">{{ __('Name (English)') }}</label><input name="name" id="product-name" required maxlength="150">
        <label for="product-name-mr">{{ __('Name (Marathi)') }}</label><input name="name_mr" id="product-name-mr" maxlength="150">
        <label for="product-description">{{ __('Description (English)') }}</label><textarea name="description" id="product-description"></textarea>
        <label for="product-description-mr">{{ __('Description (Marathi)') }}</label><textarea name="description_mr" id="product-description-mr"></textarea>
        <label for="product-price">{{ __('Price (₹)') }}</label><input type="number" name="price" id="product-price" min="0" step="0.01" required>
        <label for="product-image">{{ __('Image (JPG, PNG or WebP; max 4 MB)') }}</label><input type="file" id="product-image" name="image" accept="image/jpeg,image/png,image/webp">
        <label for="product-sort">{{ __('Sort order') }}</label><input type="number" name="sort_order" id="product-sort" min="0" value="0">
        <p><label><input type="checkbox" name="is_available" id="product-available" checked> {{ __('Available') }}</label></p>
        <button type="submit">{{ __('Save product') }}</button> <button type="button" onclick="location.reload()">{{ __('Clear') }}</button>
    </form>
</div>
<div class="panel table-wrap"><table><thead><tr><th>{{ __('Image') }}</th><th>{{ __('Product') }}</th><th>{{ __('Marathi name') }}</th><th>{{ __('Category') }}</th><th>{{ __('Price') }}</th><th>{{ __('Available') }}</th><th>{{ __('Actions') }}</th></tr></thead><tbody>
@foreach($products as $product)<tr><td>@if($product->image_path)<img width="65" height="50" style="object-fit:cover" src="{{ asset('storage/'.$product->image_path) }}" alt="">@endif</td><td>{{ $product->name }}</td><td>{{ $product->name_mr ?: '—' }}</td><td>{{ $product->category->display_name }}</td><td>₹{{ number_format($product->price,2) }}</td><td>{{ $product->is_available?__('Yes'):__('No') }}</td><td class="actions"><button type="button" onclick='editProduct(@json($product))'>{{ __('Edit') }}</button><form method="post" action="{{ route('admin.products.delete',$product) }}" data-confirm="{{ __('Delete product?') }}" onsubmit="return confirm(this.dataset.confirm)">@csrf @method('DELETE')<button type="submit">{{ __('Delete') }}</button></form></td></tr>@endforeach
</tbody></table></div>
<script>function editProduct(p){document.querySelector('#form-title').textContent=@json(__('Edit product'));for(const [key,value] of Object.entries({'product-id':p.id,'product-category':p.category_id,'product-name':p.name,'product-name-mr':p.name_mr||'','product-description':p.description||'','product-description-mr':p.description_mr||'','product-price':p.price,'product-sort':p.sort_order})){document.getElementById(key).value=value}document.querySelector('#product-available').checked=!!p.is_available;scrollTo(0,0)}</script>
@endsection
