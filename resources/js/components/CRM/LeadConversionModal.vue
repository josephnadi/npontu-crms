<template>
    <v-dialog :model-value="show" @update:model-value="$emit('close')" max-width="700px" persistent>
        <v-card>
            <v-card-title class="d-flex align-center justify-space-between pa-4 bg-primary">
                <span class="text-h5 text-white">Convert Lead</span>
                <v-btn icon="mdi-close" variant="text" color="white" @click="$emit('close')"></v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-card-text class="pa-6">
                <!-- Lead Summary -->
                <v-sheet class="pa-4 mb-6 bg-grey-lighten-4 rounded" elevation="0">
                    <div class="text-subtitle-2 text-grey-darken-2 mb-2">Lead Summary</div>
                    <div class="text-body-1 font-weight-medium mb-1">
                        {{ lead.first_name }} {{ lead.last_name }}
                    </div>
                    <div class="text-body-2 text-grey-darken-1 mb-1">
                        <v-icon size="small" class="mr-1">mdi-email</v-icon>
                        {{ lead.email || 'No email' }}
                    </div>
                    <div class="text-body-2 text-grey-darken-1" v-if="lead.company_name">
                        <v-icon size="small" class="mr-1">mdi-domain</v-icon>
                        {{ lead.company_name }}
                    </div>
                </v-sheet>

                <!-- Conversion Form -->
                <div>
                    <!-- Create Client Checkbox -->
                    <v-checkbox
                        v-model="form.create_client"
                        label="Create Client"
                        color="primary"
                        disabled
                        hide-details
                        class="mb-4"
                    >
                        <template v-slot:label>
                            <span class="text-body-1">
                                Create Client
                                <v-chip size="x-small" color="error" class="ml-2">Required</v-chip>
                            </span>
                        </template>
                    </v-checkbox>

                    <!-- Create Deal Checkbox -->
                    <v-checkbox
                        v-model="form.create_deal"
                        label="Create Deal (Optional)"
                        color="primary"
                        hide-details
                        class="mb-4"
                        :disabled="!dealStages || dealStages.length === 0"
                    ></v-checkbox>
                    
                    <!-- Warning if no deal stages -->
                    <v-alert 
                        v-if="(!dealStages || dealStages.length === 0)"
                        type="warning"
                        density="compact"
                        class="mb-4"
                    >
                        Cannot create deal: No pipeline stages available
                    </v-alert>

                    <!-- Deal Fields (shown when create_deal is checked) -->
                    <v-expand-transition>
                        <div v-if="form.create_deal" class="mt-4 pa-4 bg-blue-lighten-5 rounded">
                            <div class="text-subtitle-2 text-grey-darken-2 mb-4">Deal Details</div>

                            <!-- Deal Title -->
                            <v-text-field
                                v-model="form.deal_title"
                                label="Deal Title *"
                                variant="outlined"
                                density="comfortable"
                                class="mb-4"
                                :error="form.create_deal && !form.deal_title && attempted"
                                :error-messages="form.create_deal && !form.deal_title && attempted ? ['Deal title is required when creating a deal'] : []"
                            ></v-text-field>

                            <!-- Deal Value -->
                            <v-text-field
                                v-model="form.deal_value"
                                label="Deal Value *"
                                type="number"
                                variant="outlined"
                                density="comfortable"
                                prefix="$"
                                class="mb-4"
                                :error="form.create_deal && (!form.deal_value || form.deal_value <= 0) && attempted"
                                :error-messages="form.create_deal && (!form.deal_value || form.deal_value <= 0) && attempted ? ['Deal value must be greater than 0'] : []"
                            ></v-text-field>

                            <!-- Pipeline Stage -->
                            <v-select
                                v-model="form.deal_stage_id"
                                :items="dealStages"
                                item-title="name"
                                item-value="id"
                                label="Pipeline Stage *"
                                variant="outlined"
                                density="comfortable"
                                :error="form.create_deal && !form.deal_stage_id && attempted"
                                :error-messages="form.create_deal && !form.deal_stage_id && attempted ? ['Pipeline stage is required when creating a deal'] : []"
                                :disabled="!dealStages || dealStages.length === 0"
                            >
                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props">
                                        <template v-slot:prepend>
                                            <v-chip :color="item.raw.color" size="x-small" class="mr-2">
                                                {{ item.raw.probability }}%
                                            </v-chip>
                                        </template>
                                    </v-list-item>
                                </template>
                            </v-select>
                        </div>
                    </v-expand-transition>
                </div>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions class="pa-4">
                <v-spacer></v-spacer>
                <v-btn
                    variant="text"
                    color="grey-darken-1"
                    @click="$emit('close')"
                    :disabled="processing"
                >
                    Cancel
                </v-btn>
                <v-btn
                    color="primary"
                    variant="elevated"
                    @click="handleSubmit"
                    :loading="processing"
                    :disabled="processing"
                >
                    <v-icon left class="mr-2">mdi-swap-horizontal</v-icon>
                    Convert Lead
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
    show: {
        type: Boolean,
        required: true
    },
    lead: {
        type: Object,
        required: true
    },
    dealStages: {
        type: Array,
        default: () => []
    }
});

// Emits
const emit = defineEmits(['close', 'converted']);

// Form state
const processing = ref(false);
const attempted = ref(false);

const form = ref({
    create_client: true,
    create_deal: false,
    deal_title: '',
    deal_value: '',
    deal_stage_id: null
});

// Validation function
const validateForm = () => {
    if (!form.value.create_deal) {
        return true;
    }
    
    return !!(
        form.value.deal_title &&
        form.value.deal_value &&
        form.value.deal_value > 0 &&
        form.value.deal_stage_id
    );
};

// Set default deal title when dialog opens
watch(() => props.show, (newVal) => {
    if (newVal) {
        attempted.value = false;
        form.value.deal_title = `${props.lead.company_name || props.lead.first_name + ' ' + props.lead.last_name} - New Deal`;
        if (props.dealStages.length > 0) {
            form.value.deal_stage_id = props.dealStages[0].id;
        }
    }
});

// Handle form submission
const handleSubmit = async () => {
    attempted.value = true;
    if (!validateForm()) {
        return;
    }

    processing.value = true;

    const data = {
        create_deal: form.value.create_deal
    };

    if (form.value.create_deal) {
        data.deal_title = form.value.deal_title;
        data.deal_value = form.value.deal_value;
        data.deal_stage_id = form.value.deal_stage_id;
    }

    router.post(route('crm.leads.convert', props.lead.id), data, {
        preserveScroll: true,
        onSuccess: () => {
            processing.value = false;
            emit('converted');
            emit('close');
        },
        onError: () => {
            processing.value = false;
        }
    });
};
</script>

<style scoped>
.bg-primary {
    background-color: rgb(var(--v-theme-primary));
}

.bg-grey-lighten-4 {
    background-color: #f5f5f5;
}

.bg-blue-lighten-5 {
    background-color: #E3F2FD;
}
</style>
