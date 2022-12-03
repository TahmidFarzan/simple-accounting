@props(['category'])

<div class="accordion" id="accordionDiv{{$category->slug}}">
    <div class="accordion-item mb-2">
        @if ($category->children->count() > 0)
            <h2 class="accordion-header">
                <button class="accordion-button {{ (!($category->parent_id==null)) ?  "collapsed": null }}" type="button" data-bs-toggle="collapse" data-bs-target="#accordionDiv{{$category->slug}}Collapse{{$category->slug}}">
                    {{ $category->name }}
                </button>
            </h2>

            <div id="accordionDiv{{$category->slug}}Collapse{{$category->slug}}" class="accordion-collapse collapse {{ ($category->parent_id==null) ?  "show": null }}" data-bs-parent="#accordionDiv{{$category->slug}}">
                <div class="accordion-body">
                    <x-contract_category.tree.contract-categories :categories="$category->children"/>
                </div>
            </div>
        @endif

        @if ($category->children->count() == 0)
            <p class="accordion-header p-3">
                {{ $category->name }}
            </p>
        @endif
    </div>
</div>
