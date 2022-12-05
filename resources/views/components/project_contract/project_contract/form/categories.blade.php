@props(['categories'])
@props(['activeCategorySlug'])

@foreach ( $categories as $perCategory)
    <x-project_contract.project_contract.form.category :category="$perCategory" :activeCategorySlug="$activeCategorySlug"/>
@endforeach

