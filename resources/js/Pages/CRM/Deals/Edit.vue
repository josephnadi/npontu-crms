<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  deal: {
    type: Object,
    required: true
  },
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
  title: props.deal.title,
  description: props.deal.description,
  value: props.deal.value,
  currency: props.deal.currency || 'GHS',
  deal_stage_id: props.deal.deal_stage_id,
  probability: props.deal.probability,
  contact_id: props.deal.contact_id,
  client_id: props.deal.client_id,
  contact_name: props.deal.contact_name,
  client_name: props.deal.client_name,
  expected_close_date: props.deal.expected_close_date ? props.deal.expected_close_date.split(' ')[0] : '',
  status: props.deal.status || 'open',
  lost_reason: props.deal.lost_reason
});

const submit = () => {
  form.put(window.route('crm.deals.update', props.deal.id));
};
</script>

<template>
  <Head title="Edit Deal" />
  
  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.deals.index')" class="mr-4">
            <v-btn icon variant="text">
              <SvgSprite name="custom-chevron-left" style="width: 20px; height: 20px" />
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Edit Deal</h2>
        </div>

        <v-card class="pa-6">
          <form @submit.prevent="submit">
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="form.title"
                  label="Deal Title"
                  required
                  :error-messages="form.errors.title"
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
  label="Value"
  type="number"
  prefix="GH₵"
  required
  :error-messages="form.errors.value"
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
                  required
                  :error-messages="form.errors.deal_stage_id"
                  variant="outlined"
                  prepend-inner-icon="mdi-stairs-up"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-slider
                  v-model="form.probability"
                  label="Probability (%)"
                  thumb-label
                  min="0"
                  max="100"
                  step="5"
                  color="primary"
                  :error-messages="form.errors.probability"
                  prepend-icon="mdi-percent-outline"
                ></v-slider>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="['open', 'won', 'lost']"
                  label="Status"
                  required
                  :error-messages="form.errors.status"
                  variant="outlined"
                  prepend-inner-icon="mdi-list-status"
                ></v-select>
              </v-col>

              <v-col cols="12" v-if="form.status === 'lost'">
                <v-textarea
                  v-model="form.lost_reason"
                  label="Reason for Loss"
                  :error-messages="form.errors.lost_reason"
                  variant="outlined"
                  rows="2"
                  prepend-inner-icon="mdi-comment-question-outline"
                ></v-textarea>
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

              <v-col cols="12" class="d-flex justify-end gap-2">
                <Link :href="route('crm.deals.index')" class="text-decoration-none mr-2">
                  <v-btn variant="outlined">Cancel</v-btn>
                </Link>
                <v-btn
                  type="submit"
                  color="primary"
                  :loading="form.processing"
                >
                  Update Deal
                </v-btn>
              </v-col>
            </v-row>
          </form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
