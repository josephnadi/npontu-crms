<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  deal: {
    type: Object,
    required: true
  }
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(value);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

const deleteDeal = () => {
  if (confirm('Are you sure you want to delete this deal?')) {
    router.delete(window.route('crm.deals.destroy', props.deal.id));
  }
};
</script>

<template>
  <Head :title="`Deal: ${deal.title}`" />
  
  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" lg="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.deals.pipeline')" class="mr-4">
            <v-btn icon variant="text">
              <SvgSprite name="custom-chevron-left" style="width: 20px; height: 20px" />
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Deal Details</h2>
        </div>

        <v-card class="pa-6">
          <v-row>
            <v-col cols="12" class="d-flex justify-space-between align-start">
              <div>
                <h3 class="text-h5 font-weight-bold mb-1">{{ deal.title }}</h3>
                <v-chip :color="deal.stage?.color || 'primary'" variant="tonal" size="small">
                  {{ deal.stage?.name }} ({{ deal.probability }}%)
                </v-chip>
              </div>
              <div class="text-h5 font-weight-bold text-primary">
                {{ formatCurrency(deal.value) }}
              </div>
            </v-col>

            <v-col cols="12">
              <v-divider class="my-4"></v-divider>
            </v-col>

            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis mb-1">Contact Person</div>
                <div class="d-flex align-center">
                  <SvgSprite name="custom-user-fill" class="mr-2" style="width: 18px; height: 18px" />
                  <span class="text-body-1">{{ deal.contact_name || 'Not assigned' }}</span>
                </div>
              </div>
              
              <div>
                <div class="text-caption text-medium-emphasis mb-1">Client/Company</div>
                <div class="d-flex align-center">
                  <SvgSprite name="custom-building" class="mr-2" style="width: 18px; height: 18px" />
                  <span class="text-body-1">{{ deal.client_name || 'Not assigned' }}</span>
                </div>
              </div>
            </v-col>

            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis mb-1">Expected Close Date</div>
                <div class="d-flex align-center">
                  <SvgSprite name="custom-calendar" class="mr-2" style="width: 18px; height: 18px" />
                  <span class="text-body-1">{{ formatDate(deal.expected_close_date) }}</span>
                </div>
              </div>

              <div>
                <div class="text-caption text-medium-emphasis mb-1">Status</div>
                <v-chip
                  :color="deal.status === 'open' ? 'info' : (deal.status === 'won' ? 'success' : 'error')"
                  variant="flat"
                  size="small"
                  class="text-uppercase"
                >
                  {{ deal.status || 'open' }}
                </v-chip>
              </div>
            </v-col>

            <v-col cols="12" class="mt-6 d-flex justify-end">
               <v-btn color="error" variant="text" class="mr-auto" @click="deleteDeal">Delete Deal</v-btn>
               <Link :href="route('crm.deals.edit', deal.id)" class="text-decoration-none mr-2">
                 <v-btn variant="outlined" color="secondary">Edit Deal</v-btn>
               </Link>
               <v-btn color="primary" :href="route('crm.deals.pipeline')">Update Stage</v-btn>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
