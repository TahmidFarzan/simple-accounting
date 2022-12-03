@props(['categories'])
@props(['activeCategorySlug'])

@foreach ( $categories as $perCategory)
    <x-project_contract.category.form.category :category="$perCategory" :activeCategorySlug="$activeCategorySlug"/>
@endforeach

