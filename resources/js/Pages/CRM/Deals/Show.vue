<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import ActivityTimeline from '@/components/CRM/ActivityTimeline.vue';
import ActivityForm from '@/components/CRM/ActivityForm.vue';
import EngagementTimeline from '@/components/CRM/EngagementTimeline.vue';
import EngagementForm from '@/components/CRM/EngagementForm.vue';
import { ref } from 'vue';

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

const tab = ref('activities');
const showEngagementModal = ref(false);

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

const showConvertDialog = ref(false);
const showConvertLeadDialog = ref(false);
const showTicketConversionDialog = ref(false);
const converting = ref(false);
const convertingToLead = ref(false);
const convertingToTicket = ref(false);
const showConvertEngagementDialog = ref(false);
const convertingToEngagement = ref(false);

const convertToProject = () => {
  converting.value = true;
  router.post(window.route('crm.deals.convert', props.deal.id), {}, {
    onSuccess: () => {
      showConvertDialog.value = false;
      converting.value = false;
    },
    onError: () => {
      converting.value = false;
    }
  });
};

const convertToEngagement = () => {
  convertingToEngagement.value = true;
  router.post(window.route('crm.deals.convert-to-engagement', props.deal.id), {}, {
    onFinish: () => {
      convertingToEngagement.value = false;
      showConvertEngagementDialog.value = false;
    }
  });
};

const convertToLead = () => {
  convertingToLead.value = true;
  router.post(window.route('crm.deals.convert-to-lead', props.deal.id), {}, {
    onFinish: () => {
      convertingToLead.value = false;
      showConvertLeadDialog.value = false;
    }
  });
};

const convertToTicket = () => {
  convertingToTicket.value = true;
  router.post(window.route('crm.deals.convert-to-ticket', props.deal.id), {}, {
    onFinish: () => {
      convertingToTicket.value = false;
      showTicketConversionDialog.value = false;
    }
  });
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

            <v-col cols="12" class="mt-6 d-flex justify-end gap-2">
               <v-btn color="error" variant="text" class="mr-auto" @click="deleteDeal" title="Delete Deal">
                 <v-icon>mdi-delete</v-icon>
               </v-btn>
               
               <v-btn 
                 v-if="deal.status === 'open'"
                 color="success" 
                 variant="outlined" 
                 @click="showConvertDialog = true"
                 title="Convert to Project"
               >
                 <v-icon>mdi-rocket-launch-outline</v-icon>
               </v-btn>

               <v-btn 
                 v-if="deal.status === 'open'"
                 color="warning" 
                 variant="outlined" 
                 @click="showConvertLeadDialog = true"
                 title="Convert to Lead"
               >
                 <v-icon>mdi-account-convert-outline</v-icon>
               </v-btn>

               <v-btn 
                 color="info" 
                 variant="outlined" 
                 @click="showTicketConversionDialog = true"
                 title="Convert to Ticket"
               >
                 <v-icon>mdi-ticket-outline</v-icon>
               </v-btn>

               <v-btn 
                 color="purple" 
                 variant="outlined" 
                 @click="showConvertEngagementDialog = true"
                 title="Convert to Engagement"
               >
                 <v-icon>mdi-handshake-outline</v-icon>
               </v-btn>

               <Link :href="route('crm.deals.edit', deal.id)" class="text-decoration-none">
                 <v-btn variant="outlined" color="secondary" title="Edit Deal">
                   <v-icon>mdi-pencil</v-icon>
                 </v-btn>
               </Link>
               
               <v-btn color="info" @click="showEngagementModal = true" title="Log Engagement">
                 <v-icon>mdi-calendar-plus</v-icon>
               </v-btn>
               
               <v-btn color="primary" :href="route('crm.deals.pipeline')" title="Update Stage">
                 <v-icon>mdi-view-column</v-icon>
               </v-btn>
            </v-col>
          </v-row>
        </v-card>

        <v-tabs v-model="tab" color="primary" class="mb-4">
          <v-tab value="activities">Activities</v-tab>
          <v-tab value="engagements">Engagements</v-tab>
        </v-tabs>

        <v-window v-model="tab">
          <v-window-item value="activities">
            <v-card class="pa-6 mb-6">
              <div class="d-flex justify-space-between align-center mb-4">
                <h3 class="text-h5 font-weight-bold">Log Activity</h3>
              </div>
              <ActivityForm :activityable-type="'App\\Models\\Deal'" :activityable-id="deal.id" />
            </v-card>

            <v-card class="pa-6">
              <div class="mb-6">
                <h3 class="text-h5 font-weight-bold">Activity Timeline</h3>
              </div>
              <ActivityTimeline :activities="deal.activities || []" />
            </v-card>
          </v-window-item>

          <v-window-item value="engagements">
            <v-card class="pa-6">
              <div class="d-flex justify-space-between align-center mb-4">
                <h3 class="text-h5 font-weight-bold">Engagement Timeline</h3>
                <div class="d-flex gap-2">
                  <v-btn
                    color="warning"
                    size="small"
                    variant="flat"
                    prepend-icon="mdi-star-plus-outline"
                    @click="showEngagementModal = true"
                  >
                    Log Engagement
                  </v-btn>
                  <v-btn
                    color="secondary"
                    size="small"
                    variant="text"
                    prepend-icon="mdi-star-outline"
                    @click="router.get(route('crm.engagements.index'))"
                  >
                    View All
                  </v-btn>
                </div>
              </div>
              <v-divider class="mb-4"></v-divider>
              <EngagementTimeline :engagements="deal.engagements || []" />
            </v-card>
          </v-window-item>
        </v-window>
      </v-col>
    </v-row>

    <!-- Log Engagement Modal -->
    <v-dialog v-model="showEngagementModal" max-width="700px">
      <v-card>
        <v-card-title class="pa-4 bg-warning text-white d-flex justify-space-between align-center">
          <span>Log Engagement for {{ deal.title }}</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="showEngagementModal = false"></v-btn>
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <EngagementForm 
            engageable-type="App\Models\Deal" 
            :engageable-id="deal.id"
            @success="showEngagementModal = false"
          />
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Convert to Project Confirmation -->
    <v-dialog v-model="showConvertDialog" max-width="500px">
      <v-card>
        <v-card-title class="pa-4 bg-success text-white">
          Convert Deal to Project?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <p class="text-body-1 mb-4">
            This will mark the deal as <strong>Won</strong> and create a new project with the following details:
          </p>
          <v-list density="compact">
            <v-list-item prepend-icon="mdi-format-title" title="Project Name" :subtitle="deal.title"></v-list-item>
            <v-list-item prepend-icon="mdi-currency-usd" title="Budget" :subtitle="formatCurrency(deal.value)"></v-list-item>
            <v-list-item v-if="deal.client" prepend-icon="mdi-office-building" title="Client" :subtitle="deal.client.name"></v-list-item>
          </v-list>
          <p class="text-caption text-medium-emphasis mt-4">
            All activities and engagements associated with this deal will be migrated to the new project.
          </p>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showConvertDialog = false">Cancel</v-btn>
          <v-btn color="success" variant="elevated" :loading="converting" @click="convertToProject">
            Confirm & Convert
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Convert to Lead Confirmation -->
    <v-dialog v-model="showConvertLeadDialog" max-width="500px">
      <v-card>
        <v-card-title class="pa-4 bg-warning text-white">
          Convert Deal back to Lead?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <p class="text-body-1 mb-4">
            This will create a new Lead based on this deal's information. 
          </p>
          <v-alert type="info" variant="tonal" class="mb-4">
            The deal will remain in the system, but a new lead will be created for further qualification.
          </v-alert>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showConvertLeadDialog = false">Cancel</v-btn>
          <v-btn color="warning" variant="elevated" :loading="convertingToLead" @click="convertToLead">
            Confirm & Convert
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Convert to Ticket Confirmation -->
    <v-dialog v-model="showTicketConversionDialog" max-width="500px">
      <v-card>
        <v-card-title class="pa-4 bg-info text-white">
          Create Support Ticket from Deal?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <p class="text-body-1 mb-4">
            This will create a support ticket related to this deal.
          </p>
          <v-alert type="info" variant="tonal" class="mb-4">
            Useful for tracking technical issues or support requests during the sales process.
          </v-alert>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showTicketConversionDialog = false">Cancel</v-btn>
          <v-btn color="info" variant="elevated" :loading="convertingToTicket" @click="convertToTicket">
            Confirm & Create
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Convert to Engagement Confirmation -->
    <v-dialog v-model="showConvertEngagementDialog" max-width="500px">
      <v-card>
        <v-card-title class="pa-4 bg-purple text-white">
          Convert Deal to Engagement?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <p class="text-body-1 mb-4">
            This will create a new Engagement based on this deal's information. 
          </p>
          <v-alert type="info" variant="tonal" class="mb-4">
            An engagement will be created to schedule a follow-up or meeting related to this deal.
          </v-alert>
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showConvertEngagementDialog = false">Cancel</v-btn>
          <v-btn color="purple" variant="elevated" :loading="convertingToEngagement" @click="convertToEngagement">
            Confirm & Create
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </DashboardLayout>
</template>
