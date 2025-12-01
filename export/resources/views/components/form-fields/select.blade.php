<x-rapidez::input.select
    name="{{ $data['handle'] }}"
    id="{{ $data['id'] }}"
    type="{{ $data['type'] }}"
    :required="isset($field['validate']) && in_array('required', $field['validate']) ? 'required' : false"
    x-model="dynamic_form.{{ $data['handle'] }}"
>
    <option disabled selected>{{ $data['placeholder'] }}</option>
    @foreach($data['options'] as $key => $label)
        <option value="{{ $key }}">{{ $key }}</option>
    @endforeach
</x-rapidez::input.select>

