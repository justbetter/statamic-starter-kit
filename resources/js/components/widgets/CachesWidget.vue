<template>
    <Widget :title="title" icon="cache">
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            <div class="flex items-center justify-between gap-4 px-4 py-3">
                <div class="min-w-0">
                    <Text :text="labels.application" variant="strong" />
                    <Description :text="labels.application_description" class="mt-1" />
                </div>
                <Button
                    size="sm"
                    :text="labels.clear"
                    :disabled="busy"
                    @click="confirming = 'application'"
                />
            </div>

            <div class="flex items-center justify-between gap-4 px-4 py-3">
                <div class="min-w-0">
                    <Text :text="labels.static" variant="strong" />
                    <Description
                        :text="staticCacheEnabled ? labels.static_description : labels.static_disabled"
                        class="mt-1"
                    />
                </div>
                <Button
                    v-if="staticCacheEnabled"
                    size="sm"
                    :text="labels.clear"
                    :disabled="busy"
                    @click="confirming = 'static'"
                />
            </div>
        </div>

        <ConfirmationModal
            :open="confirming !== null"
            :title="confirmTitle"
            :body-text="confirmBody"
            :button-text="labels.confirm_button"
            :busy="busy"
            danger
            @update:open="onModalOpenChange"
            @confirm="clearCache"
        />
    </Widget>
</template>

<script setup>
import { computed, getCurrentInstance, ref } from 'vue';
import { Button, ConfirmationModal, Description, Text, Widget } from '@statamic/cms/ui';

const { title, clearApplicationUrl, clearStaticUrl, staticCacheEnabled, labels } = defineProps({
    title: { type: String, required: true },
    clearApplicationUrl: { type: String, required: true },
    clearStaticUrl: { type: String, required: true },
    staticCacheEnabled: { type: Boolean, default: false },
    labels: { type: Object, required: true },
});

const confirming = ref(null);
const busy = ref(false);
const app = getCurrentInstance()?.proxy;

const confirmTitle = computed(() => {
    if (confirming.value === 'application') {
        return labels.confirm_application_title;
    }

    if (confirming.value === 'static') {
        return labels.confirm_static_title;
    }

    return '';
});

const confirmBody = computed(() => {
    if (confirming.value === 'application') {
        return labels.confirm_application_body;
    }

    if (confirming.value === 'static') {
        return labels.confirm_static_body;
    }

    return '';
});

function onModalOpenChange(open) {
    if (!open && !busy.value) {
        confirming.value = null;
    }
}

async function clearCache() {
    if (!confirming.value || busy.value) {
        return;
    }

    const type = confirming.value;
    const url = type === 'application' ? clearApplicationUrl : clearStaticUrl;
    const successMessage = type === 'application' ? labels.success_application : labels.success_static;

    busy.value = true;

    try {
        const response = await app.$axios.post(url);
        app.$toast.success(response.data?.message || successMessage);
        confirming.value = null;
    } catch {
        app.$toast.error(labels.error);
    } finally {
        busy.value = false;
    }
}
</script>
