/* global Statamic */

import FormEmailAvailableFieldsFieldtype from './components/FormEmailAvailableFieldsFieldtype.vue';

Statamic.booting(() => {
    Statamic.$components.register('form_email_available_fields-fieldtype', FormEmailAvailableFieldsFieldtype);
});
