<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  communications: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const typeFilter = ref(props.filters.type || '');
const directionFilter = ref(props.filters.direction || '');
const loading = ref(false);

const types = [
  { title: 'All Types', value: '' },
  { title: 'Email', value: 'email' },
  { title: 'SMS', value: 'sms' },
  { title: 'WhatsApp', value: 'whatsapp' },
  { title: 'Call', value: 'call' },
];

const directions = [
  { title: 'All Directions', value: '' },
  { title: 'Inbound', value: 'inbound' },
  { title: 'Outbound', value: 'outbound' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.communications.index'), { 
    search: search.value, 
    type: typeFilter.value,
    direction: directionFilter.value
  }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  search.value = '';
  typeFilter.value = '';
  directionFilter.value = '';
  applyFilters();
};

const getTypeIcon = (type) => {
  switch (type) {
    case 'email': return 'mdi-email-outline';
    case 'sms': return 'mdi-message-text-outline';
    case 'whatsapp': return 'mdi-whatsapp';
    case 'call': return 'mdi-phone-outline';
    default: return 'mdi-information-outline';
  }
};

const getTypeColor = (type) => {
  switch (type) {
    case 'email': return 'primary';
    case 'sms': return 'info';
    case 'whatsapp': return 'success';
    case 'call': return 'warning';
    default: return 'grey';
  }
};

watch([search, typeFilter, directionFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <Head title="Unified Inbox" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12">
        <div class="d-flex align-center text-caption text-medium-emphasis mb-2">
          <span>Home</span>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span>CRM</span>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span class="text-high-emphasis">Unified Inbox</span>
        </div>
        <h2 class="text-h3 font-weight-bold mb-6">Unified Inbox</h2>
      </v-col>

      <v-col cols="12">
        <v-card elevation="0" border>
          <v-card-text class="pa-4 border-bottom">
            <v-row align="center">
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="search"
                  label="Search interactions..."
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  prepend-inner-icon="mdi-magnify"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="typeFilter"
                  :items="types"
                  label="Type"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="directionFilter"
                  :items="directions"
                  label="Direction"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="12" md="2">
                <v-btn variant="outlined" block @click="clearFilters">Clear</v-btn>
              </v-col>
            </v-row>
          </v-card-text>

          <v-list class="pa-0 inbox-list" lines="three">
            <template v-if="communications.data.length > 0">
              <template v-for="(comm, index) in communications.data" :key="comm.id">
                <v-list-item class="pa-4 border-bottom hover-bg">
                  <template v-slot:prepend>
                    <v-avatar :color="getTypeColor(comm.type) + '-lighten-5'" size="48">
                      <v-icon :color="getTypeColor(comm.type)">{{ getTypeIcon(comm.type) }}</v-icon>
                    </v-avatar>
                  </template>

                  <v-list-item-title class="d-flex justify-space-between align-center mb-1">
                    <span class="text-h6 font-weight-bold">{{ comm.subject || '(No Subject)' }}</span>
                    <span class="text-caption text-medium-emphasis">{{ new Date(comm.created_at).toLocaleString() }}</span>
                  </v-list-item-title>

                  <v-list-item-subtitle class="mb-2">
                    <div class="d-flex align-center gap-2 mb-1">
                      <v-chip size="x-small" :color="comm.direction === 'inbound' ? 'info' : 'secondary'" variant="tonal">
                        {{ comm.direction.toUpperCase() }}
                      </v-chip>
                      <span class="text-caption font-weight-bold text-high-emphasis">
                        {{ comm.direction === 'inbound' ? 'From: ' : 'To: ' }} {{ comm.direction === 'inbound' ? comm.from_identifier : comm.to_identifier }}
                      </span>
                    </div>
                    <div class="text-body-2 text-truncate-2">{{ comm.content }}</div>
                  </v-list-item-subtitle>

                  <template v-slot:append>
                    <div class="d-flex flex-column align-end">
                      <v-chip size="x-small" color="grey-lighten-2" variant="flat" class="mb-2">
                        {{ comm.status }}
                      </v-chip>
                      <v-btn icon="mdi-eye-outline" variant="text" size="small" :href="route('crm.communications.show', comm.id)"></v-btn>
                    </div>
                  </template>
                </v-list-item>
              </template>
            </template>
            <v-list-item v-else class="pa-10 text-center">
              <v-list-item-title class="text-h6 text-medium-emphasis">No interactions found</v-list-item-title>
              <v-list-item-subtitle>Your omnichannel communications will appear here</v-list-item-subtitle>
            </v-list-item>
          </v-list>

          <v-card-text v-if="communications.links.length > 3" class="pa-4 d-flex justify-center">
            <v-pagination
              v-model="communications.current_page"
              :length="communications.last_page"
              @update:modelValue="router.get(route('crm.communications.index', { page: $event }))"
              total-visible="7"
              rounded="circle"
            ></v-pagination>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<style scoped>
.hover-bg:hover {
  background-color: #f8fafc;
}
.border-bottom {
  border-bottom: 1px solid #e2e8f0;
}
.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
