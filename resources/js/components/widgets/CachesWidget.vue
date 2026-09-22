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
            :title="labels.confirm[confirming]?.title"
            :body-text="labels.confirm[confirming]?.body"
            :button-text="labels.confirm.button"
            :busy="busy"
            danger
            @update:open="onModalOpenChange"
            @confirm="clearCache"
        />
    </Widget>
</template>

<script setup>
import { getCurrentInstance, ref } from 'vue';
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

    busy.value = true;

    try {
        const response = await app.$axios.post(url);
        app.$toast.success(response.data?.message || labels.success[type]);
        confirming.value = null;
    } catch {
        app.$toast.error(labels.error);
    } finally {
        busy.value = false;
    }
}
</script>
