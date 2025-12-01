<x-rapidez::input.textarea
    name="{{ $data['handle'] }}"
    id="{{ $data['handle'] }}"
    value="{{ old($data['handle']) }}"
    placeholder="{{ $data['placeholder'] ?? false }}"
    type="text"
    autocomplete="disabled"
    :required="isset($field['validate']) && in_array('required', $field['validate']) ? 'required' : false"
    x-model="dynamic_form.{{ $data['handle'] }}"
/>