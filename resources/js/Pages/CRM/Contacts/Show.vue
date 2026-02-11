<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref } from 'vue';
import ActivityTimeline from '@/components/CRM/ActivityTimeline.vue';
import ActivityForm from '@/components/CRM/ActivityForm.vue';
import CommunicationTimeline from '@/components/CRM/CommunicationTimeline.vue';
import CommunicationForm from '@/components/CRM/CommunicationForm.vue';
import EngagementTimeline from '@/components/CRM/EngagementTimeline.vue';
import EngagementForm from '@/components/CRM/EngagementForm.vue';

const props = defineProps({
  contact: Object,
});

const tab = ref('activities');
const showActivityModal = ref(false);
const showInteractionModal = ref(false);
const showEngagementModal = ref(false);
const showConversionDialog = ref(false);
const showTicketConversionDialog = ref(false);
const converting = ref(false);
const convertingToTicket = ref(false);

const confirmConvertToLead = () => {
  showConversionDialog.value = true;
};

const convertToLead = () => {
  converting.value = true;
  router.post(route('crm.contacts.convert', props.contact.id), {}, {
    onFinish: () => {
      converting.value = false;
      showConversionDialog.value = false;
    }
  });
};

const convertToTicket = () => {
  convertingToTicket.value = true;
  router.post(route('crm.contacts.convert-to-ticket', props.contact.id), {}, {
    onFinish: () => {
      convertingToTicket.value = false;
      showTicketConversionDialog.value = false;
    }
  });
};
</script>

<template>
  <Head :title="'Contact Details - ' + contact.first_name" />

  <DashboardLayout>
    <v-row>
      <!-- Header -->
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div class="d-flex align-center">
          <Link :href="route('crm.contacts.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <div>
            <h2 class="text-h3 font-weight-bold">{{ contact.first_name }} {{ contact.last_name }}</h2>
            <div class="d-flex align-center text-subtitle-1 text-medium-emphasis">
              <v-chip :color="contact.status === 'active' ? 'success' : 'grey'" size="small" variant="flat" class="text-capitalize mr-3">
                {{ contact.status }}
              </v-chip>
              <v-icon size="small" class="mr-1">mdi-briefcase-outline</v-icon>
              {{ contact.job_title || 'No Title' }}
            </div>
          </div>
        </div>
        <div class="d-flex gap-2">
          <v-btn
            v-if="contact.owner_id === $page.props.auth.user.id"
            variant="outlined"
            color="warning"
            @click="confirmConvertToLead"
            title="Convert to Lead"
          >
            <v-icon>mdi-account-convert-outline</v-icon>
          </v-btn>
          <v-btn
            v-if="contact.owner_id === $page.props.auth.user.id"
            variant="outlined"
            color="info"
            @click="showTicketConversionDialog = true"
            title="Convert to Ticket"
          >
            <v-icon>mdi-ticket-outline</v-icon>
          </v-btn>
          <Link :href="route('crm.contacts.edit', contact.id)">
            <v-btn variant="outlined" color="primary" title="Edit Contact">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </Link>
        </div>
      </v-col>

      <!-- Left Column: Contact Details -->
      <v-col cols="12" md="4">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Contact Information"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Email</div>
              <div class="text-body-2 font-weight-bold">
                <v-icon size="small" color="primary" class="mr-2">mdi-email-outline</v-icon>
                {{ contact.email || 'N/A' }}
              </div>
            </div>
            
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Phone</div>
              <div class="text-body-2 font-weight-bold">
                <v-icon size="small" color="primary" class="mr-2">mdi-phone-outline</v-icon>
                {{ contact.phone || 'N/A' }}
              </div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Client / Company</div>
              <div class="text-body-2">
                <v-icon size="small" color="primary" class="mr-2">mdi-domain</v-icon>
                <Link v-if="contact.client" :href="route('crm.clients.show', contact.client.id)" class="text-decoration-none font-weight-bold">
                  {{ contact.client.name }}
                </Link>
                <span v-else>Individual</span>
              </div>
            </div>

            <v-divider class="my-4"></v-divider>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Location</div>
              <div class="text-body-2">
                <v-icon size="small" color="primary" class="mr-2">mdi-map-marker-outline</v-icon>
                <span v-if="contact.city">{{ contact.city }}<span v-if="contact.state">, {{ contact.state }}</span><span v-if="contact.country">, {{ contact.country }}</span></span>
                <span v-else>N/A</span>
              </div>
            </div>

            <div class="mb-0">
              <div class="text-caption text-medium-emphasis mb-1">Owner</div>
              <div class="d-flex align-center">
                <v-avatar size="24" color="primary" class="mr-2">
                  <span class="text-caption text-white">{{ contact.owner?.name.charAt(0) }}</span>
                </v-avatar>
                <span class="text-body-2">{{ contact.owner?.name }}</span>
              </div>
            </div>
          </v-card-text>
        </v-card>

        <v-card elevation="0" border>
          <v-card-item title="Internal Notes"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <p class="text-body-2" style="white-space: pre-wrap;">{{ contact.notes || 'No notes available for this contact.' }}</p>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Right Column: Activities/Related Info -->
      <v-col cols="12" md="8">
        <v-tabs v-model="tab" color="primary">
          <v-tab value="activities">Activities</v-tab>
          <v-tab value="interactions">Interactions</v-tab>
          <v-tab value="engagements">Engagements</v-tab>
          <v-tab value="deals">Deals ({{ contact.deals?.length || 0 }})</v-tab>
        </v-tabs>
        
        <v-window v-model="tab" class="mt-4">
          <v-window-item value="activities">
            <v-card elevation="0" border>
              <v-card-item title="Activity Timeline">
                <template v-slot:append>
                  <v-btn
                    color="primary"
                    size="small"
                    variant="flat"
                    prepend-icon="mdi-plus"
                    @click="showActivityModal = true"
                  >
                    Log Activity
                  </v-btn>
                </template>
              </v-card-item>
              <v-divider></v-divider>
              <v-card-text class="pa-4">
                <ActivityTimeline :activities="contact.activities" />
              </v-card-text>
            </v-card>
          </v-window-item>

          <v-window-item value="interactions">
            <v-card elevation="0" border>
              <v-card-item title="Omnichannel Interactions">
                <template v-slot:append>
                  <v-btn
                    color="secondary"
                    size="small"
                    variant="flat"
                    prepend-icon="mdi-message-plus-outline"
                    @click="showInteractionModal = true"
                  >
                    Log Interaction
                  </v-btn>
                </template>
              </v-card-item>
              <v-divider></v-divider>
              <v-card-text class="pa-4">
                <CommunicationTimeline :communications="contact.communications" />
              </v-card-text>
            </v-card>
          </v-window-item>

          <v-window-item value="engagements">
            <v-card elevation="0" border>
              <v-card-item title="Engagement Timeline">
                <template v-slot:append>
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
                </template>
              </v-card-item>
              <v-divider></v-divider>
              <v-card-text class="pa-4">
                <EngagementTimeline :engagements="contact.engagements" />
              </v-card-text>
            </v-card>
          </v-window-item>

          <v-window-item value="deals">
            <v-card elevation="0" border>
              <v-table v-if="contact.deals?.length > 0">
                <thead>
                  <tr>
                    <th class="text-left font-weight-bold">Title</th>
                    <th class="text-left font-weight-bold">Value</th>
                    <th class="text-left font-weight-bold">Stage</th>
                    <th class="text-right font-weight-bold">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="deal in contact.deals" :key="deal.id">
                    <td>{{ deal.title }}</td>
                    <td>GH₵ {{ deal.value.toLocaleString() }}</td>
                    <td>
                      <v-chip size="x-small" color="primary" variant="tonal">{{ deal.stage?.name || 'Unknown' }}</v-chip>
                    </td>
                    <td class="text-right">
                      <v-menu>
                        <template v-slot:activator="{ props }">
                          <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                        </template>
                        <v-list size="small">
                          <v-list-item @click="router.get(route('crm.deals.show', deal.id))" prepend-icon="mdi-eye">
                            <v-list-item-title>View Details</v-list-item-title>
                          </v-list-item>
                          <v-list-item @click="router.get(route('crm.deals.edit', deal.id))" prepend-icon="mdi-pencil">
                            <v-list-item-title>Edit</v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                </tbody>
              </v-table>
              <v-card-text v-else class="pa-8 text-center text-medium-emphasis">
                <v-icon size="48" class="mb-2">mdi-handshake-outline</v-icon>
                <p>No deals associated with this contact.</p>
                <Link :href="route('crm.deals.create', { contact_id: contact.id })">
                  <v-btn variant="text" color="primary" prepend-icon="mdi-plus">Create Deal</v-btn>
                </Link>
              </v-card-text>
            </v-card>
          </v-window-item>
        </v-window>
      </v-col>
    </v-row>

    <!-- Log Interaction Modal -->
    <v-dialog v-model="showInteractionModal" max-width="700px">
      <v-card>
        <v-card-title class="pa-4 bg-secondary text-white d-flex justify-space-between align-center">
          <span>Log Omnichannel Interaction</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="showInteractionModal = false"></v-btn>
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <CommunicationForm 
            communicable-type="App\Models\Contact" 
            :communicable-id="contact.id"
            :default-to="contact.email"
            @success="showInteractionModal = false"
          />
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Log Activity Modal -->
    <v-dialog v-model="showActivityModal" max-width="600px">
      <v-card>
        <v-card-title class="pa-4 bg-primary text-white d-flex justify-space-between align-center">
          <span>Log New Activity</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="showActivityModal = false"></v-btn>
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <ActivityForm 
            activityable-type="App\Models\Contact" 
            :activityable-id="contact.id"
            @success="showActivityModal = false"
          />
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Log Engagement Modal -->
    <v-dialog v-model="showEngagementModal" max-width="700px">
      <v-card>
        <v-card-title class="pa-4 bg-warning text-white d-flex justify-space-between align-center">
          <span>Log Engagement for {{ contact.first_name }}</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="showEngagementModal = false"></v-btn>
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <EngagementForm 
            engageable-type="App\Models\Contact" 
            :engageable-id="contact.id"
            @success="showEngagementModal = false"
          />
        </v-card-text>
      </v-card>
    </v-dialog>

    <!-- Conversion Confirmation Dialog -->
    <v-dialog v-model="showConversionDialog" max-width="500px">
      <v-card>
        <v-card-title class="pa-4 bg-warning text-white">
          Convert Contact to Lead?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          This will move this contact and all its activities/engagements to a new lead record. The contact record will be archived.
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showConversionDialog = false">Cancel</v-btn>
          <v-btn color="warning" variant="elevated" :loading="converting" @click="convertToLead">
            Confirm Conversion
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Convert to Ticket Dialog -->
    <v-dialog v-model="showTicketConversionDialog" max-width="500px">
      <v-card>
        <v-card-title class="pa-4 bg-info text-white d-flex justify-space-between align-center">
          <span>Convert to Ticket</span>
          <v-btn icon="mdi-close" variant="text" size="small" @click="showTicketConversionDialog = false"></v-btn>
        </v-card-title>
        <v-card-text class="pa-6">
          <div class="text-center mb-6">
            <v-icon color="info" size="64" class="mb-4">mdi-ticket-outline</v-icon>
            <h3 class="text-h5 mb-2">Create Support Ticket?</h3>
            <p class="text-body-1 text-medium-emphasis">
              This will create a support ticket for <strong>{{ contact.first_name }} {{ contact.last_name }}</strong>.
            </p>
          </div>
        </v-card-text>
        <v-card-actions class="pa-4 pt-0">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showTicketConversionDialog = false">Cancel</v-btn>
          <v-btn color="info" :loading="convertingToTicket" @click="convertToTicket">
            Confirm Ticket Creation
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </DashboardLayout>
</template>
