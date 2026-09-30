@extends('layout')
@section('title',__('Categories'))
@section('content')
@include('admin.nav')
<h1>{{ __('Categories') }}</h1>
<div class="panel"><h2 id="form-title">{{ __('Add category') }}</h2>
    <form method="post" action="{{ route('admin.categories.save') }}">@csrf<input type="hidden" name="id" id="category-id">
        <label for="category-name">{{ __('Name (English)') }}</label><input name="name" id="category-name" required maxlength="100">
        <label for="category-name-mr">{{ __('Name (Marathi)') }}</label><input name="name_mr" id="category-name-mr" maxlength="100">
        <label for="category-description">{{ __('Description (English)') }}</label><textarea name="description" id="category-description"></textarea>
        <label for="category-description-mr">{{ __('Description (Marathi)') }}</label><textarea name="description_mr" id="category-description-mr"></textarea>
        <label for="category-sort">{{ __('Sort order') }}</label><input type="number" name="sort_order" id="category-sort" min="0" value="0">
        <p><label><input type="checkbox" name="is_active" id="category-active" checked> {{ __('Active') }}</label></p>
        <button type="submit">{{ __('Save category') }}</button> <button type="button" onclick="location.reload()">{{ __('Clear') }}</button>
    </form>
</div>
<div class="panel table-wrap"><table><thead><tr><th>{{ __('Name') }}</th><th>{{ __('Marathi name') }}</th><th>{{ __('Products') }}</th><th>{{ __('Visible') }}</th><th>{{ __('Actions') }}</th></tr></thead><tbody>
@foreach($categories as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->name_mr ?: '—' }}</td><td>{{ $category->products_count }}</td><td>{{ $category->is_active?__('Yes'):__('No') }}</td><td class="actions"><button type="button" onclick='editCategory(@json($category))'>{{ __('Edit') }}</button><form method="post" action="{{ route('admin.categories.delete',$category) }}" data-confirm="{{ __('Delete category?') }}" onsubmit="return confirm(this.dataset.confirm)">@csrf @method('DELETE')<button type="submit">{{ __('Delete') }}</button></form></td></tr>@endforeach
</tbody></table></div>
<script>function editCategory(c){document.querySelector('#form-title').textContent=@json(__('Edit category'));for(const [key,value] of Object.entries({'category-id':c.id,'category-name':c.name,'category-name-mr':c.name_mr||'','category-description':c.description||'','category-description-mr':c.description_mr||'','category-sort':c.sort_order})){document.getElementById(key).value=value}document.querySelector('#category-active').checked=!!c.is_active;scrollTo(0,0)}</script>
@endsection
