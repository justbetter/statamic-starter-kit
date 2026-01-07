@if($global_component->page_builder ?? false)
    @include('page_builder', ['page_builder' => $global_component->page_builder])
@endif
