<x-rapidez::input
    name="{{ $data['handle'] }}"
    id="{{ $data['id'] }}"
    value="{{ old($data['handle']) }}"
    placeholder="{{ $data['placeholder'] ?? false }}"
    x-model="dynamic_form.{{ $data['handle'] }}"
    type="{{ $data['input_type']}}"
    :required="isset($field['validate']) && in_array('required', $field['validate']) ? 'required' : false"
/>