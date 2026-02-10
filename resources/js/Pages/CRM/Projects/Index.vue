<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  projects: Object,
  filters: Object,
  stats: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const priorityFilter = ref(props.filters.priority || '');
const loading = ref(false);

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'Pending', value: 'pending' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'Completed', value: 'completed' },
  { title: 'Cancelled', value: 'cancelled' },
];

const priorities = [
  { title: 'All Priorities', value: '' },
  { title: 'Low', value: 'low' },
  { title: 'Medium', value: 'medium' },
  { title: 'High', value: 'high' },
  { title: 'Urgent', value: 'urgent' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.projects.index'), { 
    search: search.value, 
    status: statusFilter.value,
    priority: priorityFilter.value
  }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  search.value = '';
  statusFilter.value = '';
  priorityFilter.value = '';
  applyFilters();
};

const deleteProject = (id) => {
  if (confirm('Are you sure you want to delete this project?')) {
    router.delete(route('crm.projects.destroy', id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'pending': return 'warning';
    case 'in_progress': return 'info';
    case 'completed': return 'success';
    case 'cancelled': return 'error';
    default: return 'grey';
  }
};

const getPriorityColor = (priority) => {
  switch (priority) {
    case 'low': return 'grey';
    case 'medium': return 'info';
    case 'high': return 'warning';
    case 'urgent': return 'error';
    default: return 'grey';
  }
};

const formatCurrency = (value) => {
  if (!value) return '$0.00';
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
};

watch([search, statusFilter, priorityFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <Head title="Projects" />
  <DashboardLayout>
    <v-container fluid>
      <!-- Stats Cards -->
      <v-row class="mb-4">
        <v-col cols="12" sm="6" md="3">
          <v-card border elevation="0" class="bg-light-primary">
            <v-card-text class="d-flex align-center">
              <v-avatar color="primary" variant="tonal" class="mr-4">
                <v-icon>mdi-folder-outline</v-icon>
              </v-avatar>
              <div>
                <div class="text-subtitle-2 text-grey">Total Projects</div>
                <div class="text-h4 font-weight-bold">{{ stats.total }}</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <v-card border elevation="0" class="bg-light-info">
            <v-card-text class="d-flex align-center">
              <v-avatar color="info" variant="tonal" class="mr-4">
                <v-icon>mdi-progress-check</v-icon>
              </v-avatar>
              <div>
                <div class="text-subtitle-2 text-grey">Active</div>
                <div class="text-h4 font-weight-bold">{{ stats.active }}</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <v-card border elevation="0" class="bg-light-error">
            <v-card-text class="d-flex align-center">
              <v-avatar color="error" variant="tonal" class="mr-4">
                <v-icon>mdi-alert-circle-outline</v-icon>
              </v-avatar>
              <div>
                <div class="text-subtitle-2 text-grey">Overdue</div>
                <div class="text-h4 font-weight-bold">{{ stats.overdue }}</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" sm="6" md="3">
          <v-card border elevation="0" class="bg-light-success">
            <v-card-text class="d-flex align-center">
              <v-avatar color="success" variant="tonal" class="mr-4">
                <v-icon>mdi-check-all</v-icon>
              </v-avatar>
              <div>
                <div class="text-subtitle-2 text-grey">Completed</div>
                <div class="text-h4 font-weight-bold">{{ stats.completed }}</div>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12">
          <v-card elevation="0" class="mb-6">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div>
                <h1 class="text-h4 mb-1">Projects</h1>
                <p class="text-subtitle-1 text-medium-emphasis">Manage your projects and their progress.</p>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                :to="route('crm.projects.create')"
              >
                New Project
              </v-btn>
            </v-card-text>
          </v-card>

          <v-card elevation="0">
            <v-card-text class="pa-4">
              <v-row>
                <v-col cols="12" sm="6" md="4">
                  <v-text-field
                    v-model="search"
                    prepend-inner-icon="mdi-magnify"
                    label="Search projects..."
                    variant="outlined"
                    density="comfortable"
                    hide-details
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                  <v-select
                    v-model="statusFilter"
                    :items="statuses"
                    label="Status"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                  <v-select
                    v-model="priorityFilter"
                    :items="priorities"
                    label="Priority"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" md="4" class="d-flex align-center">
                  <v-btn
                    variant="text"
                    color="primary"
                    @click="clearFilters"
                    class="ml-2"
                  >
                    Clear Filters
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>

            <v-divider></v-divider>

            <v-table hover>
              <thead>
                <tr>
                  <th class="text-left">Project Name</th>
                  <th class="text-left">Client / Deal</th>
                  <th class="text-left">Status & Priority</th>
                  <th class="text-left" style="width: 150px;">Progress</th>
                  <th class="text-left">Budget</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="6" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                  </td>
                </tr>
                <tr v-else-if="projects.data.length === 0">
                  <td colspan="6" class="text-center py-10">
                    <v-icon size="64" color="medium-emphasis" class="mb-4">mdi-folder-open-outline</v-icon>
                    <p class="text-h6 text-medium-emphasis">No projects found.</p>
                  </td>
                </tr>
                <tr v-for="project in projects.data" :key="project.id">
                  <td class="py-4">
                    <div class="d-flex flex-column">
                      <Link :href="route('crm.projects.show', project.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary">
                        {{ project.name }}
                      </Link>
                      <span class="text-caption text-medium-emphasis text-truncate" style="max-width: 250px;">
                        {{ project.description }}
                      </span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <span v-if="project.client" class="text-subtitle-2">{{ project.client.name }}</span>
                      <span v-if="project.deal" class="text-caption text-medium-emphasis">
                        <v-icon size="x-small">mdi-handshake-outline</v-icon> {{ project.deal.title }}
                      </span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex flex-column gap-1">
                      <v-chip
                        :color="getStatusColor(project.status)"
                        size="x-small"
                        label
                        class="text-uppercase font-weight-bold"
                      >
                        {{ project.status.replace('_', ' ') }}
                      </v-chip>
                      <v-chip
                        :color="getPriorityColor(project.priority)"
                        size="x-small"
                        variant="outlined"
                        class="text-uppercase font-weight-bold"
                      >
                        {{ project.priority }}
                      </v-chip>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-center">
                      <v-progress-linear
                        v-model="project.progress"
                        color="primary"
                        height="8"
                        rounded
                      ></v-progress-linear>
                      <span class="ml-2 text-caption">{{ project.progress }}%</span>
                    </div>
                  </td>
                  <td class="font-weight-medium">
                    {{ formatCurrency(project.budget) }}
                  </td>
                  <td class="text-right">
                    <v-menu>
                      <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                      </template>
                      <v-list density="compact">
                        <v-list-item :to="route('crm.projects.show', project.id)" prepend-icon="mdi-eye">
                          <v-list-item-title>View</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to="route('crm.projects.edit', project.id)" prepend-icon="mdi-pencil">
                          <v-list-item-title>Edit</v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                        <v-list-item @click="deleteProject(project.id)" prepend-icon="mdi-delete" color="error">
                          <v-list-item-title>Delete</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </tbody>
            </v-table>

            <v-divider></v-divider>

            <v-card-text v-if="projects.links.length > 3" class="pa-4 d-flex justify-center">
              <v-pagination
                v-model="projects.current_page"
                :length="projects.last_page"
                @update:modelValue="router.get(route('crm.projects.index', { page: $event }))"
                total-visible="7"
                rounded="circle"
              ></v-pagination>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>
