@props(['category'])
@props(['activeCategorySlug'])

@php
    $depthString = null;

    for ($i = 0; $i < $category->depth; $i++) {
        $depthString = $depthString."---";
    }
@endphp

@if (($depthString == null) && ($category->id > 1))
    <option disabled></option>
@endif

<option value="{{ $category->slug }}" {{ ($activeCategorySlug == $category->slug) ?  "selected" : null }}>{{ $depthString }} {{ $category->name }}</option>

<x-project_contract.project_contract.form.categories :categories="$category->children" :activeCategorySlug="$activeCategorySlug"/>
