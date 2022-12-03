@props(['categories'])
@props(['activeCategorySlug'])

@foreach ( $categories as $perCategory)
    <x-contract_category.form.contract-category :category="$perCategory" :activeCategorySlug="$activeCategorySlug"/>
@endforeach

