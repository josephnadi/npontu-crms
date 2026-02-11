<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  ticket: Object,
});

const loading = ref(false);
const showConvertLeadDialog = ref(false);
const showConvertDealDialog = ref(false);
const showConvertContactDialog = ref(false);
const converting = ref(false);

const deleteTicket = () => {
  if (confirm('Are you sure you want to delete this ticket?')) {
    router.delete(route('crm.tickets.destroy', props.ticket.id));
  }
};

const updateStatus = (status) => {
  loading.value = true;
  router.put(route('crm.tickets.update', props.ticket.id), {
    ...props.ticket,
    status: status
  }, {
    onFinish: () => loading.value = false
  });
};

const convertToLead = () => {
  converting.value = true;
  router.post(route('crm.tickets.convert-to-lead', props.ticket.id), {}, {
    onFinish: () => {
      converting.value = false;
      showConvertLeadDialog.value = false;
    }
  });
};

const convertToDeal = () => {
  converting.value = true;
  router.post(route('crm.tickets.convert-to-deal', props.ticket.id), {}, {
    onFinish: () => {
      converting.value = false;
      showConvertDealDialog.value = false;
    }
  });
};

const convertToContact = () => {
  converting.value = true;
  router.post(route('crm.tickets.convert-to-contact', props.ticket.id), {}, {
    onFinish: () => {
      converting.value = false;
      showConvertContactDialog.value = false;
    }
  });
};

const getStatusColor = (status) => {
  switch (status) {
    case 'open': return 'primary';
    case 'in_progress': return 'info';
    case 'pending': return 'warning';
    case 'resolved': return 'success';
    case 'closed': return 'grey';
    default: return 'grey';
  }
};

const getPriorityColor = (priority) => {
  switch (priority) {
    case 'low': return 'success';
    case 'medium': return 'warning';
    case 'high': return 'error';
    case 'urgent': return 'deep-orange';
    default: return 'grey';
  }
};
</script>

<template>
  <Head :title="'Ticket Details - ' + ticket.ticket_number" />

  <DashboardLayout>
    <v-row>
      <!-- Header -->
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div class="d-flex align-center">
          <Link :href="route('crm.tickets.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <div>
            <h2 class="text-h3 font-weight-bold">{{ ticket.ticket_number }}: {{ ticket.subject }}</h2>
            <div class="d-flex align-center text-subtitle-1 text-medium-emphasis">
              <v-chip :color="getStatusColor(ticket.status)" size="small" variant="flat" class="text-capitalize mr-3">
                {{ ticket.status.replace('_', ' ') }}
              </v-chip>
              <v-chip :color="getPriorityColor(ticket.priority)" size="small" variant="outlined" class="text-capitalize mr-3">
                {{ ticket.priority }}
              </v-chip>
              <v-icon size="small" class="mr-1">mdi-tag-outline</v-icon>
              {{ ticket.category }}
            </div>
          </div>
        </div>
        <div class="d-flex gap-1">
          <v-menu v-if="ticket.status !== 'resolved' && ticket.status !== 'closed'">
            <template v-slot:activator="{ props }">
              <v-btn variant="outlined" color="primary" v-bind="props" title="Update Status">
                <v-icon>mdi-list-status</v-icon>
              </v-btn>
            </template>
            <v-list size="small">
              <v-list-item v-if="ticket.status !== 'in_progress'" @click="updateStatus('in_progress')" prepend-icon="mdi-play-circle-outline">
                <v-list-item-title>Mark In Progress</v-list-item-title>
              </v-list-item>
              <v-list-item v-if="ticket.status !== 'pending'" @click="updateStatus('pending')" prepend-icon="mdi-clock-outline">
                <v-list-item-title>Mark Pending</v-list-item-title>
              </v-list-item>
              <v-list-item @click="updateStatus('resolved')" color="success" prepend-icon="mdi-check-circle-outline">
                <v-list-item-title>Mark Resolved</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <v-menu v-if="ticket.status !== 'closed'">
            <template v-slot:activator="{ props }">
              <v-btn variant="outlined" color="warning" v-bind="props" title="Convert Ticket">
                <v-icon>mdi-cached</v-icon>
              </v-btn>
            </template>
            <v-list size="small">
              <v-list-item @click="showConvertLeadDialog = true" prepend-icon="mdi-account-convert">
                <v-list-item-title>Convert to Lead</v-list-item-title>
              </v-list-item>
              <v-list-item @click="showConvertDealDialog = true" prepend-icon="mdi-handshake-outline">
                <v-list-item-title>Convert to Deal</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>

          <Link :href="route('crm.tickets.edit', ticket.id)">
            <v-btn variant="outlined" color="secondary" title="Edit Ticket">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </Link>
          
          <v-btn color="error" variant="text" @click="deleteTicket" title="Delete Ticket">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </div>
      </v-col>

      <!-- Main Content -->
      <v-col cols="12" md="8">
        <!-- Dialogs -->
        <v-dialog v-model="showConvertLeadDialog" max-width="500">
          <v-card>
            <v-card-title class="text-h5 pa-4">Convert Ticket to Lead?</v-card-title>
            <v-card-text class="pa-4">
              This will create a new Lead based on this ticket's information and mark the ticket as closed.
            </v-card-text>
            <v-card-actions class="pa-4">
              <v-spacer></v-spacer>
              <v-btn variant="text" @click="showConvertLeadDialog = false">Cancel</v-btn>
              <v-btn color="warning" variant="flat" @click="convertToLead" :loading="converting">Convert to Lead</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="showConvertContactDialog" max-width="500">
          <v-card>
            <v-card-title class="text-h5 pa-4">Convert Ticket to Contact?</v-card-title>
            <v-card-text class="pa-4">
              This will create a new Contact based on this ticket's information and mark the ticket as closed.
            </v-card-text>
            <v-card-actions class="pa-4">
              <v-spacer></v-spacer>
              <v-btn variant="text" @click="showConvertContactDialog = false">Cancel</v-btn>
              <v-btn color="warning" variant="flat" @click="convertToContact" :loading="converting">Convert to Contact</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <v-dialog v-model="showConvertDealDialog" max-width="500">
          <v-card>
            <v-card-title class="text-h5 pa-4">Convert Ticket to Deal?</v-card-title>
            <v-card-text class="pa-4">
              This will create a new Deal for the associated client and mark the ticket as closed.
            </v-card-text>
            <v-card-actions class="pa-4">
              <v-spacer></v-spacer>
              <v-btn variant="text" @click="showConvertDealDialog = false">Cancel</v-btn>
              <v-btn color="warning" variant="flat" @click="convertToDeal" :loading="converting">Convert to Deal</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Description"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="text-body-1" style="white-space: pre-wrap;">{{ ticket.description }}</div>
          </v-card-text>
        </v-card>

        <!-- Timeline Placeholder -->
        <v-card elevation="0" border>
          <v-card-item title="Ticket History"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <v-timeline density="compact" align="start">
              <v-timeline-item dot-color="primary" size="x-small">
                <div class="d-flex justify-space-between">
                  <div>
                    <div class="text-subtitle-2 font-weight-bold">Ticket Created</div>
                    <div class="text-caption">by {{ ticket.reporter?.name || 'System' }}</div>
                  </div>
                  <div class="text-caption">{{ new Date(ticket.created_at).toLocaleString() }}</div>
                </div>
              </v-timeline-item>
              <v-timeline-item v-if="ticket.resolved_at" dot-color="success" size="x-small">
                <div class="d-flex justify-space-between">
                  <div>
                    <div class="text-subtitle-2 font-weight-bold">Ticket Resolved</div>
                  </div>
                  <div class="text-caption">{{ new Date(ticket.resolved_at).toLocaleString() }}</div>
                </div>
              </v-timeline-item>
            </v-timeline>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Sidebar Info -->
      <v-col cols="12" md="4">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Ticket Details"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Related Client</div>
              <div class="text-body-2" v-if="ticket.client">
                <v-icon size="small" class="mr-2">mdi-factory</v-icon>
                <Link :href="route('crm.clients.show', ticket.client.id)" class="text-decoration-none font-weight-bold">
                  {{ ticket.client.name }}
                </Link>
              </div>
              <div v-else class="text-body-2">N/A</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Assigned To</div>
              <div class="d-flex align-center" v-if="ticket.assignee">
                <v-avatar size="24" color="primary" class="mr-2">
                  <span class="text-caption text-white">{{ ticket.assignee.name.charAt(0) }}</span>
                </v-avatar>
                <span class="text-body-2">{{ ticket.assignee.name }}</span>
              </div>
              <div v-else class="text-body-2">Unassigned</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Reported By</div>
              <div class="d-flex align-center" v-if="ticket.reporter">
                <v-avatar size="24" color="secondary" class="mr-2">
                  <span class="text-caption text-white">{{ ticket.reporter.name.charAt(0) }}</span>
                </v-avatar>
                <span class="text-body-2">{{ ticket.reporter.name }}</span>
              </div>
            </div>

            <div class="mb-0">
              <div class="text-caption text-medium-emphasis mb-1">Created</div>
              <div class="text-body-2">{{ new Date(ticket.created_at).toLocaleString() }}</div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>