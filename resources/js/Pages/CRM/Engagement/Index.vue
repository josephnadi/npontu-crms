<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
  engagements: {
    type: Object,
    required: true
  },
  filters: Object,
  stats: Object,
});

const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || 'all');
const dateRange = ref('');
const loading = ref(false);

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.engagements.index'), {
    search: search.value,
    type: typeFilter.value,
  }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  });
};

watch([search, typeFilter], debounce(() => {
  applyFilters();
}, 500));

const deleteEngagement = (id) => {
  if (confirm('Are you sure you want to delete this engagement?')) {
    router.delete(route('crm.engagements.destroy', id));
  }
};

const convertToDeal = (id) => {
  if (confirm('Convert this engagement to a new Deal?')) {
    router.post(route('crm.engagements.convert-to-deal', id));
  }
};

const convertToTicket = (id) => {
  if (confirm('Create a support ticket from this engagement?')) {
    router.post(route('crm.engagements.convert-to-ticket', id));
  }
};

const getIcon = (type) => {
  switch (type) {
    case 'email': return 'custom-mail';
    case 'call': return 'custom-phone';
    case 'meeting': return 'custom-users';
    case 'webinar': return 'custom-video';
    case 'form_submitted': return 'custom-file-text';
    case 'content_viewed': return 'custom-eye';
    default: return 'custom-calendar';
  }
};

const getColor = (type) => {
  switch (type) {
    case 'email': return 'warning';
    case 'call': return 'info';
    case 'meeting': return 'primary';
    case 'webinar': return 'secondary';
    case 'form_submitted': return 'success';
    case 'content_viewed': return 'info';
    default: return 'grey';
  }
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleString();
};

const getEntityLink = (engagement) => {
  if (!engagement.engageable) return '#';
  const type = engagement.engageable_type.split('\\').pop().toLowerCase();
  switch (type) {
    case 'deal': return route('crm.deals.show', engagement.engageable_id);
    case 'lead': return route('crm.leads.show', engagement.engageable_id);
    case 'contact': return route('crm.contacts.show', engagement.engageable_id);
    case 'client': return route('crm.clients.show', engagement.engageable_id);
    default: return '#';
  }
};

const getEntityName = (engagement) => {
  if (!engagement.engageable) return 'Unknown';
  const type = engagement.engageable_type.split('\\').pop();
  const name = engagement.engageable.name || engagement.engageable.full_name || engagement.engageable.title || 'Unknown';
  return `${type}: ${name}`;
};
</script>

<template>
  <Head title="Engagements" />
  <DashboardLayout>
    <v-row>
      <v-col cols="12" md="4">
        <v-card variant="outlined" class="bg-surface">
          <v-card-text class="d-flex align-center">
            <v-avatar color="primary" variant="tonal" size="40" class="mr-3">
              <SvgSprite name="custom-calendar" size="20" />
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-medium-emphasis">Total Engagements</div>
              <div class="text-h5 font-weight-bold">{{ stats.total }}</div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card variant="outlined" class="bg-surface">
          <v-card-text class="d-flex align-center">
            <v-avatar color="success" variant="tonal" size="40" class="mr-3">
              <SvgSprite name="custom-trending-up" size="20" />
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-medium-emphasis">This Month</div>
              <div class="text-h5 font-weight-bold">{{ stats.this_month }}</div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card variant="outlined" class="bg-surface">
          <v-card-text class="d-flex align-center">
            <v-avatar color="warning" variant="tonal" size="40" class="mr-3">
              <SvgSprite name="custom-star" size="20" />
            </v-avatar>
            <div>
              <div class="text-subtitle-2 text-medium-emphasis">Avg. Engagement Score</div>
              <div class="text-h5 font-weight-bold">{{ stats.avg_score }}</div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12">
        <v-card variant="outlined" class="bg-surface">
          <v-card-title class="pa-4 d-flex align-center">
            <span>Engagement History</span>
            <v-spacer></v-spacer>
            <div class="d-flex" style="gap: 12px;">
              <v-text-field
                v-model="search"
                prepend-inner-icon="mdi-magnify"
                label="Search"
                variant="outlined"
                density="compact"
                hide-details
                style="width: 250px;"
              ></v-text-field>
              <v-select
                v-model="typeFilter"
                :items="['all', 'email', 'call', 'meeting', 'webinar', 'form_submitted', 'content_viewed', 'other']"
                label="Type"
                variant="outlined"
                density="compact"
                hide-details
                style="width: 150px;"
              ></v-select>
            </div>
          </v-card-title>

          <v-table>
            <thead>
              <tr>
                <th class="text-left">Type</th>
                <th class="text-left">Subject</th>
                <th class="text-left">Related To</th>
                <th class="text-left">Date</th>
                <th class="text-left">Score</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in engagements.data" :key="item.id">
                <td>
                  <v-chip :color="getColor(item.type)" size="small" variant="tonal">
                    <template v-slot:prepend>
                      <SvgSprite :name="getIcon(item.type)" size="14" class="mr-1" />
                    </template>
                    {{ item.type }}
                  </v-chip>
                </td>
                <td>
                  <div class="font-weight-medium">{{ item.subject }}</div>
                  <div class="text-caption text-medium-emphasis text-truncate" style="max-width: 200px;">
                    {{ item.description }}
                  </div>
                </td>
                <td>
                  <Link :href="getEntityLink(item)" class="text-decoration-none text-primary">
                    {{ getEntityName(item) }}
                  </Link>
                </td>
                <td>{{ formatDate(item.engagement_date) }}</td>
                <td>
                  <v-chip :color="item.score >= 50 ? 'success' : 'grey'" size="x-small">
                    {{ item.score }}
                  </v-chip>
                </td>
                <td class="text-right">
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn size="small" v-bind="props" variant="text">
                        <v-icon color="primary">mdi-pencil</v-icon>
                      </v-btn>
                    </template>
                    <v-list>
                      <v-list-item :to="route('crm.engagements.show', item.id)">
                        <template v-slot:prepend>
                          <v-icon>mdi-eye</v-icon>
                        </template>
                        <v-list-item-title>View Details</v-list-item-title>
                      </v-list-item>
                      <v-list-item :to="route('crm.engagements.edit', item.id)">
                        <template v-slot:prepend>
                          <v-icon>mdi-pencil</v-icon>
                        </template>
                        <v-list-item-title>Edit Engagement</v-list-item-title>
                      </v-list-item>
                      <v-list-item
                        v-if="item.engageable_type !== 'App\\Models\\Deal'"
                        @click="convertToDeal(item.id)"
                      >
                        <template v-slot:prepend>
                          <v-icon>mdi-handshake-outline</v-icon>
                        </template>
                        <v-list-item-title>Convert to Deal</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="convertToTicket(item.id)">
                        <template v-slot:prepend>
                          <v-icon>mdi-ticket-outline</v-icon>
                        </template>
                        <v-list-item-title>Convert to Ticket</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteEngagement(item.id)">
                        <template v-slot:prepend>
                          <SvgSprite name="custom-trash" size="18" />
                        </template>
                        <v-list-item-title>Delete Engagement</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
              <tr v-if="engagements.data.length === 0">
                <td colspan="6" class="text-center pa-8 text-medium-emphasis">
                  No engagements found.
                </td>
              </tr>
            </tbody>
          </v-table>

          <v-divider></v-divider>
          
          <div class="pa-4 d-flex align-center justify-space-between">
            <div class="text-caption text-medium-emphasis">
              Showing {{ engagements.from || 0 }} to {{ engagements.to || 0 }} of {{ engagements.total }} engagements
            </div>
            <div class="d-flex" style="gap: 8px;">
              <v-btn
                v-for="link in engagements.links"
                :key="link.label"
                :disabled="!link.url || link.active"
                variant="outlined"
                size="small"
                @click="router.get(link.url)"
                v-html="link.label"
              ></v-btn>
            </div>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
