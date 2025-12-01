@foreach($data['options'] as $key => $label)
    <x-rapidez::input.checkbox
        name="{{ $data['handle'] }}"
        type="{{ $data['type'] }}"
        value="{{ $key }}"
        :required="isset($field['validate']) && in_array('required', $field['validate']) ? 'required' : false"       
        x-model="dynamic_form.{{ $data['handle'] }}"
    >
        {{ $label }}
    </x-rapidez::input.checkbox>
@endforeach