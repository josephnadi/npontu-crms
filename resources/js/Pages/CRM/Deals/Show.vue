<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import ActivityTimeline from '@/components/CRM/ActivityTimeline.vue';
import ActivityForm from '@/components/CRM/ActivityForm.vue';

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

const updateStage = (stageId) => {
  router.put(window.route('crm.deals.updateStage', props.deal.id), {
    deal_stage_id: stageId
  }, {
    preserveScroll: true
  });
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-GH', {
    style: 'currency',
    currency: 'GHS'
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
      <v-col cols="12" md="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.deals.index')" class="mr-4">
            <v-btn icon variant="text">
              <SvgSprite name="custom-chevron-left" style="width: 20px; height: 20px" />
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Deal Details</h2>
        </div>

        <v-card class="pa-6 mb-6">
          <v-row>
            <v-col cols="12" class="d-flex justify-space-between align-start">
              <div>
                <h3 class="text-h5 font-weight-bold mb-1">{{ deal.title }}</h3>
                <div class="d-flex align-center gap-2">
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-chip 
                        v-bind="props"
                        :color="deal.stage?.color || 'primary'" 
                        variant="tonal" 
                        size="small"
                        class="cursor-pointer"
                      >
                        {{ deal.stage?.name }} ({{ deal.probability }}%)
                        <v-icon end size="x-small">mdi-chevron-down</v-icon>
                      </v-chip>
                    </template>
                    <v-list density="compact">
                      <v-list-item
                        v-for="stage in stages"
                        :key="stage.id"
                        :value="stage.id"
                        @click="updateStage(stage.id)"
                        :active="stage.id === deal.deal_stage_id"
                      >
                        <v-list-item-title>{{ stage.name }}</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </div>
              <div class="text-h5 font-weight-bold text-primary">
                {{ formatCurrency(deal.value) }}
              </div>
            </v-col>

            <v-col cols="12" v-if="deal.description">
              <div class="text-caption text-medium-emphasis mb-1">Description</div>
              <p class="text-body-1">{{ deal.description }}</p>
            </v-col>

            <v-col cols="12" v-if="deal.status === 'lost' && deal.lost_reason">
              <v-alert
                type="error"
                variant="tonal"
                title="Deal Lost"
                :text="deal.lost_reason"
                class="mt-2"
              ></v-alert>
            </v-col>

            <v-col cols="12">
              <v-divider class="my-4"></v-divider>
            </v-col>

            <v-col cols="12" md="6">
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis mb-1">Contact Person</div>
                <div class="d-flex align-center">
                  <SvgSprite name="custom-user-fill" class="mr-2" style="width: 18px; height: 18px" />
                  <template v-if="deal.contact">
                    <Link :href="route('crm.contacts.show', deal.contact.id)" class="text-primary text-decoration-none font-weight-medium">
                      {{ deal.contact.first_name }} {{ deal.contact.last_name }}
                    </Link>
                  </template>
                  <span v-else class="text-body-1">{{ deal.contact_name || 'Not assigned' }}</span>
                </div>
              </div>
              
              <div>
                <div class="text-caption text-medium-emphasis mb-1">Client/Company</div>
                <div class="d-flex align-center">
                  <SvgSprite name="custom-building" class="mr-2" style="width: 18px; height: 18px" />
                  <template v-if="deal.client">
                    <Link :href="route('crm.clients.show', deal.client.id)" class="text-primary text-decoration-none font-weight-medium">
                      {{ deal.client.name }}
                    </Link>
                  </template>
                  <span v-else class="text-body-1">{{ deal.client_name || 'Not assigned' }}</span>
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

        <v-card class="pa-6">
          <div class="d-flex justify-space-between align-center mb-4">
            <h3 class="text-h5 font-weight-bold">Log Activity</h3>
          </div>
          <ActivityForm :activityable-type="'App\\Models\\Deal'" :activityable-id="deal.id" />
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <div class="mb-6">
          <h3 class="text-h5 font-weight-bold">Activity Timeline</h3>
        </div>
        <ActivityTimeline :activities="deal.activities || []" />
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
