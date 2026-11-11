/* global Statamic, __, cp_url */

const defaults = {
    collection: 'global_components',
    page_builder_field: 'page_builder',
    set_handle: 'global_component',
    relationship_field: 'global_component',
};

function resolveConfig(config = {}) {
    return {
        ...defaults,
        ...config,
    };
}

export default function convertToGlobalComponent(config = {}) {
    const {
        set_handle: setHandle,
        relationship_field: relationshipField,
    } = resolveConfig(config);

    return {
        title: __('justbetter-starter-kit::messages.convert_to_global_component'),
        confirm: {
            title: __('justbetter-starter-kit::messages.convert_to_global_component'),
            text: __('justbetter-starter-kit::messages.convert_to_global_component_text'),
            buttonText: __('justbetter-starter-kit::messages.convert'),
            fields: {
                title: {
                    type: 'text',
                    display: __('justbetter-starter-kit::messages.title'),
                    validate: ['required'],
                },
            },
        },

        visible(payload) {
            return payload.config.handle !== setHandle;
        },

        run(payload) {
            const csrfToken = Statamic.$config.get('csrfToken');

            fetch(cp_url('global-components/convert'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                },
                body: JSON.stringify({
                    title: payload.confirmation.processed.title,
                    component: payload.values,
                }),
            }).then(async (response) => {
                const data = await response.json();

                if (! response.ok) {
                    throw new Error(data.message || __('justbetter-starter-kit::messages.could_not_create_global_component'));
                }

                payload.updateMeta(relationshipField, relationshipMeta(data));
                payload.update(relationshipField, [data.id]);
                payload.update('type', setHandle);

                Statamic.$toast.success(__('justbetter-starter-kit::messages.global_component_created'));
            }).catch((error) => {
                Statamic.$toast.error(error.message || __('justbetter-starter-kit::messages.could_not_create_global_component'));
            });
        }
    }
}

function relationshipMeta(entry) {
    return {
        data: [{
            id: entry.id,
            title: entry.title,
            edit_url: entry.edit_url,
            status: null,
        }],
        columns: [{ field: 'title', label: 'Title' }],
        itemDataUrl: cp_url('fieldtypes/relationship/data'),
        filtersUrl: cp_url('fieldtypes/relationship/filters'),
        baseSelectionsUrl: cp_url('fieldtypes/relationship'),
        getBaseSelectionsUrlParameters: {},
        itemComponent: 'related-item',
        canEdit: true,
        canCreate: false,
        canSearch: true,
        statusIcons: false,
        creatables: [],
        formComponent: null,
        formComponentProps: { _: '_' },
        formStackSize: null,
        taggable: false,
        tree: null,
        initialSortColumn: null,
        initialSortDirection: null,
    };
}
