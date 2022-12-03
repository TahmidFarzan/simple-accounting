@props(['categories'])

@foreach ( $categories as $perCategory)
    <x-contract_category.tree.contract-category :category="$perCategory"/>
@endforeach

