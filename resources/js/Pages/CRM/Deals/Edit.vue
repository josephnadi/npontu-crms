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
  }
});

const form = useForm({
  title: props.deal.title,
  value: props.deal.value,
  currency: props.deal.currency || 'USD',
  deal_stage_id: props.deal.deal_stage_id,
  probability: props.deal.probability,
  contact_name: props.deal.contact_name,
  client_name: props.deal.client_name,
  expected_close_date: props.deal.expected_close_date ? props.deal.expected_close_date.split(' ')[0] : '',
  status: props.deal.status || 'open'
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
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.value"
                  label="Value"
                  type="number"
                  prefix="$"
                  required
                  :error-messages="form.errors.value"
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
                ></v-slider>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="['open', 'won', 'lost']"
                  label="Status"
                  required
                  :error-messages="form.errors.status"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.contact_name"
                  label="Contact Name"
                  :error-messages="form.errors.contact_name"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.client_name"
                  label="Client/Company"
                  :error-messages="form.errors.client_name"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.expected_close_date"
                  label="Expected Close Date"
                  type="date"
                  :error-messages="form.errors.expected_close_date"
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
