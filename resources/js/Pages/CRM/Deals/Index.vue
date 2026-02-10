<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  deals: {
    type: Object,
    required: true
  },
  filters: Object,
  stages: Array,
});

const search = $ref(props.filters?.search || '');
const stageId = $ref(props.filters?.stage_id || '');
const status = $ref(props.filters?.status || '');

const applyFilters = () => {
  router.get(route('crm.deals.index'), {
    search,
    stage_id: stageId,
    status
  }, {
    preserveState: true,
    replace: true
  });
};

const clearFilters = () => {
  search = '';
  stageId = '';
  status = '';
  applyFilters();
};

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-GH', {
    style: 'currency',
    currency: 'GHS'
  }).format(value);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString();
};

const deleteDeal = (id) => {
  if (confirm('Are you sure you want to delete this deal?')) {
    router.delete(window.route('crm.deals.destroy', id));
  }
};
</script>

<template>
  <Head title="Deals List" />
  
  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <div>
          <h2 class="text-h4 font-weight-bold">Deals List</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Overview of all active and closed deals</p>
        </div>
        <div class="d-flex gap-2">
          <Link :href="route('crm.deals.pipeline')" class="text-decoration-none mr-2">
            <v-btn variant="outlined" color="primary">
              <template v-slot:prepend>
                <SvgSprite name="custom-row-vertical" style="width: 18px; height: 18px" />
              </template>
              Pipeline View
            </v-btn>
          </Link>
          <v-btn variant="outlined" color="secondary" @click="router.get(route('crm.deals.export'), { search, stage_id: stageId, status })">
            <template v-slot:prepend>
              <SvgSprite name="custom-download" style="width: 18px; height: 18px" />
            </template>
            Export CSV
          </v-btn>
          <Link :href="route('crm.deals.create')" class="text-decoration-none">
            <v-btn color="primary">
              <template v-slot:prepend>
                <SvgSprite name="custom-plus" style="width: 18px; height: 18px" />
              </template>
              New Deal
            </v-btn>
          </Link>
        </div>
      </v-col>

      <v-col cols="12">
        <v-card class="mb-4">
          <v-card-text>
            <v-row>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="search"
                  label="Search deals..."
                  prepend-inner-icon="mdi-magnify"
                  hide-details
                  variant="outlined"
                  density="comfortable"
                  @keyup.enter="applyFilters"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="stageId"
                  :items="[{ title: 'All Stages', value: '' }, ...stages.map(s => ({ title: s.name, value: s.id }))]"
                  label="Stage"
                  hide-details
                  variant="outlined"
                  density="comfortable"
                ></v-select>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="status"
                  :items="[
                    { title: 'All Statuses', value: '' },
                    { title: 'Open', value: 'open' },
                    { title: 'Won', value: 'won' },
                    { title: 'Lost', value: 'lost' }
                  ]"
                  label="Status"
                  hide-details
                  variant="outlined"
                  density="comfortable"
                ></v-select>
              </v-col>
              <v-col cols="12" md="2" class="d-flex flex-column gap-2">
                <v-btn color="primary" block flat @click="applyFilters">Filter</v-btn>
                <v-btn variant="outlined" block @click="clearFilters">Clear</v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12">
        <v-card variant="flat">
          <v-table hover>
            <thead>
              <tr>
                <th class="text-left">Title</th>
                <th class="text-left">Stage</th>
                <th class="text-left">Value</th>
                <th class="text-left">Probability</th>
                <th class="text-left">Contact</th>
                <th class="text-left">Expected Close</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="deal in deals.data" :key="deal.id">
                <td class="font-weight-medium">{{ deal.title }}</td>
                <td>
                  <v-chip size="small" :color="deal.stage?.color || 'primary'" variant="tonal">
                    {{ deal.stage?.name || 'Unknown' }}
                  </v-chip>
                </td>
                <td>{{ formatCurrency(deal.value) }}</td>
                <td>
                  <v-progress-linear
                    :model-value="deal.probability"
                    :color="deal.stage?.color || 'primary'"
                    height="8"
                    rounded
                  ></v-progress-linear>
                  <span class="text-caption">{{ deal.probability }}%</span>
                </td>
                <td>
                  <template v-if="deal.contact">
                    <Link :href="route('crm.contacts.show', deal.contact.id)" class="text-primary text-decoration-none font-weight-medium">
                      {{ deal.contact.first_name }} {{ deal.contact.last_name }}
                    </Link>
                  </template>
                  <span v-else>{{ deal.contact_name || 'N/A' }}</span>
                </td>
                <td>{{ formatDate(deal.expected_close_date) }}</td>
                <td class="text-right">
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                    </template>
                    <v-list size="small">
                      <v-list-item @click="router.get(route('crm.deals.show', deal.id))">
                        <template v-slot:prepend>
                          <v-icon icon="mdi-eye-outline"></v-icon>
                        </template>
                        <v-list-item-title>View Details</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="router.get(route('crm.deals.edit', deal.id))">
                        <template v-slot:prepend>
                          <v-icon icon="mdi-pencil-outline"></v-icon>
                        </template>
                        <v-list-item-title>Edit</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deleteDeal(deal.id)" base-color="error">
                        <template v-slot:prepend>
                          <v-icon icon="mdi-delete-outline"></v-icon>
                        </template>
                        <v-list-item-title>Delete</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
              <tr v-if="deals.data.length === 0">
                <td colspan="7" class="text-center py-8 text-medium-emphasis">
                  No deals found. Start by creating a new deal!
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
      <v-col cols="12">
        <div class="pa-4 d-flex justify-center">
          <v-pagination
            v-model="deals.current_page"
            :length="deals.last_page"
            @update:modelValue="router.get(route('crm.deals.index'), { page: $event, search, stage_id: stageId, status }, { preserveState: true })"
            size="small"
            active-color="primary"
          ></v-pagination>
        </div>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
