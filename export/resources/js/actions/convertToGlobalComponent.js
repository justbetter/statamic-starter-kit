export default function convertToGlobalComponent() {
    return {
        title: __('Convert to global component'),
        confirm: {
            title: __('Convert to global component'),
            text: __('This will create a reusable global component and replace this component with a reference to it.'),
            buttonText: __('Convert'),
            fields: {
                title: {
                    type: 'text',
                    display: __('Title'),
                    validate: ['required'],
                },
            },
        },

        visible(payload) {
            return payload.config.handle !== 'global_component';
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
                    throw new Error(data.message || __('Could not create global component.'));
                }

                payload.updateMeta('global_component', relationshipMeta(data));
                payload.update('global_component', [data.id]);
                payload.update('type', 'global_component');

                Statamic.$toast.success(__('Global component created.'));
            }).catch((error) => {
                Statamic.$toast.error(error.message || __('Could not create global component.'));
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
