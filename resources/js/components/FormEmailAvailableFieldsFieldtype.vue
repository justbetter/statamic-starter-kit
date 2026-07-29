<template>
    <div class="card p-0">
        <div class="flex items-center justify-between border-b p-2">
            <Text size="xs" color="gray">
                {{ __('justbetter-starter-kit::messages.available_fields_for_email_content') }}
            </Text>
            <Button variant="primary" size="sm" @click="isOpen = !isOpen">
                {{ isOpen ? __('justbetter-starter-kit::messages.hide_fields') : __('justbetter-starter-kit::messages.show_fields') }}
            </Button>
        </div>

        <div v-if="isOpen && rows.length" class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th>{{ __('justbetter-starter-kit::messages.handle') }}</th>
                        <th>{{ __('justbetter-starter-kit::messages.label') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in rows" :key="row.handle" class="cursor-pointer" @click="copyToken(row.token)">
                        <td>
                            <Button variant="text" size="sm">
                                <code>{{ row.token }}</code>
                            </Button>
                        </td>
                        <td>{{ row.label }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else-if="isOpen" class="p-3">
            <Text size="xs" color="gray">
                {{ __('justbetter-starter-kit::messages.no_available_fields_found_for_this_form') }}
            </Text>
        </div>
    </div>
</template>

<script setup>
import { computed, getCurrentInstance, ref } from 'vue';
import { Button, Text } from '@statamic/cms/ui';

const { meta } = defineProps({
    meta: {
        type: Object,
        default: () => ({}),
    },
});

const isOpen = ref(false);

const rows = computed(() => {
    const availableFields = meta?.available_fields;

    return Array.isArray(availableFields) ? availableFields : [];
});

const appContext = getCurrentInstance();

async function copyToken(token) {
    try {
        await navigator.clipboard.writeText(token);
        appContext?.proxy?.$toast?.success(__('justbetter-starter-kit::messages.field_handle_copied_to_clipboard'));
    } catch {
        appContext?.proxy?.$toast?.error(__('justbetter-starter-kit::messages.unable_to_copy_field_handle'));
    }
}
</script>
