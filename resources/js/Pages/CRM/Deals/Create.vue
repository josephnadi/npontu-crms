<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  stages: {
    type: Array,
    required: true
  },
  contacts: {
    type: Array,
    default: () => []
  },
  clients: {
    type: Array,
    default: () => []
  }
});

const form = useForm({
  title: '',
  description: '',
  value: 0,
  deal_stage_id: props.stages[0]?.id || null,
  contact_id: null,
  client_id: null,
  contact_name: '',
  client_name: '',
  expected_close_date: null,
  probability: props.stages[0]?.probability || 0
});

const submit = () => {
  form.post(window.route('crm.deals.store'), {
    onSuccess: () => form.reset()
  });
};

const updateProbability = () => {
  const stage = props.stages.find(s => s.id === form.deal_stage_id);
  if (stage) {
    form.probability = stage.probability;
  }
};
</script>

<template>
  <Head title="Create Deal" />
  
  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.deals.pipeline')" class="mr-4">
            <v-btn icon variant="text">
              <SvgSprite name="custom-chevron-left" style="width: 20px; height: 20px" />
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Create New Deal</h2>
        </div>

        <v-card class="pa-6">
          <v-form @submit.prevent="submit">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.title"
                  label="Deal Title"
                  :error-messages="form.errors.title"
                  required
                  variant="outlined"
                  prepend-inner-icon="mdi-briefcase-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description"
                  :error-messages="form.errors.description"
                  variant="outlined"
                  rows="3"
                  prepend-inner-icon="mdi-text-box-outline"
                ></v-textarea>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
  v-model="form.value"
  label="Deal Value"
  type="number"
  prefix="GH₵"
  :error-messages="form.errors.value"
  required
  variant="outlined"
  prepend-inner-icon="mdi-cash"
></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.deal_stage_id"
                  :items="stages"
                  item-title="name"
                  item-value="id"
                  label="Stage"
                  :error-messages="form.errors.deal_stage_id"
                  required
                  variant="outlined"
                  prepend-inner-icon="mdi-stairs-up"
                  @update:model-value="updateProbability"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-autocomplete
                  v-model="form.contact_id"
                  :items="contacts"
                  :item-title="item => `${item.first_name} ${item.last_name}`"
                  item-value="id"
                  label="Contact"
                  :error-messages="form.errors.contact_id"
                  variant="outlined"
                  prepend-inner-icon="mdi-account-outline"
                  clearable
                ></v-autocomplete>
              </v-col>

              <v-col cols="12" md="6">
                <v-autocomplete
                  v-model="form.client_id"
                  :items="clients"
                  item-title="name"
                  item-value="id"
                  label="Client / Company"
                  :error-messages="form.errors.client_id"
                  variant="outlined"
                  prepend-inner-icon="mdi-office-building-outline"
                  clearable
                ></v-autocomplete>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.expected_close_date"
                  label="Expected Close Date"
                  type="date"
                  :error-messages="form.errors.expected_close_date"
                  variant="outlined"
                  prepend-inner-icon="mdi-calendar-clock"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-slider
                  v-model="form.probability"
                  label="Probability (%)"
                  min="0"
                  max="100"
                  step="5"
                  thumb-label
                  color="primary"
                ></v-slider>
              </v-col>

              <v-col cols="12" class="d-flex justify-end gap-2 mt-4">
                <Link :href="route('crm.deals.pipeline')" class="text-decoration-none mr-2">
                  <v-btn variant="text" :disabled="form.processing">Cancel</v-btn>
                </Link>
                <v-btn
                  color="primary"
                  type="submit"
                  :loading="form.processing"
                  size="large"
                >
                  Create Deal
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
