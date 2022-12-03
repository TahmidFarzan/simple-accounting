@props(['categories'])

@foreach ( $categories as $perCategory)
    <x-project_contract.category.tree.category :category="$perCategory"/>
@endforeach

