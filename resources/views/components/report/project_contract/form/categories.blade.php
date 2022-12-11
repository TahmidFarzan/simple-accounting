@props(['categories'])
@props(['activeCategorySlug'])

@foreach ( $categories as $perCategory)
    <x-report.project_contract.form.category :category="$perCategory" :activeCategorySlug="$activeCategorySlug"/>
@endforeach

