/* global Statamic */

import FormEmailAvailableFieldsFieldtype from './components/FormEmailAvailableFieldsFieldtype.vue';
import CachesWidget from './components/widgets/CachesWidget.vue';
import convertToGlobalComponent from './actions/convertToGlobalComponent';

Statamic.booting(() => {
    Statamic.$components.register('form_email_available_fields-fieldtype', FormEmailAvailableFieldsFieldtype);
    Statamic.$components.register('justbetter-caches-widget', CachesWidget);

    const convertConfig = Statamic.$config.get('justbetterStarterKit.convertToGlobalComponent', {});

    if (convertConfig.enabled !== false) {
        Statamic.$fieldActions.add('replicator-fieldtype-set', convertToGlobalComponent(convertConfig));
    }
});
