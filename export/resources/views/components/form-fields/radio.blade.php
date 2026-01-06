@foreach($data['options'] as $key => $label)
    <x-rapidez::input.radio
        name="{{ $data['handle'] }}"
        id="{{ $data['id'] }}"
        value="{{ $key }}"
        type="{{ $data['type'] }}"
        label="{{ $label }}"
        :required="isset($field['validate']) && in_array('required', $field['validate']) ? 'required' : false"
        x-model="dynamic_form.{{ $data['handle'] }}"
    >
        {{ $label }}
    </x-rapidez::input.radio>
@endforeach