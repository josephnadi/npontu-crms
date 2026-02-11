<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  tasks: Object,
  filters: Object,
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
];

const priorities = [
  { title: 'All Priorities', value: '' },
  { title: 'Low', value: 'low' },
  { title: 'Medium', value: 'medium' },
  { title: 'High', value: 'high' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.tasks.index'), { 
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

const deleteTask = (id) => {
  if (confirm('Are you sure you want to delete this task?')) {
    router.delete(route('crm.tasks.destroy', id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'pending': return 'warning';
    case 'in_progress': return 'info';
    case 'completed': return 'success';
    default: return 'grey';
  }
};

const getPriorityColor = (priority) => {
  switch (priority) {
    case 'low': return 'success';
    case 'medium': return 'warning';
    case 'high': return 'error';
    default: return 'grey';
  }
};

watch([search, statusFilter, priorityFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <Head title="Tasks" />
  <DashboardLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-card elevation="0" class="mb-6">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div>
                <h1 class="text-h4 mb-1">Tasks</h1>
                <p class="text-subtitle-1 text-medium-emphasis">Manage your tasks and to-do list.</p>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                :to="route('crm.tasks.create')"
              >
                New Task
              </v-btn>
            </v-card-text>
          </v-card>

          <v-card elevation="0">
            <v-card-text class="pa-4">
              <v-row>
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="search"
                    prepend-inner-icon="mdi-magnify"
                    label="Search tasks..."
                    variant="outlined"
                    density="comfortable"
                    hide-details
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="3">
                  <v-select
                    v-model="statusFilter"
                    :items="statuses"
                    label="Status"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="3">
                  <v-select
                    v-model="priorityFilter"
                    :items="priorities"
                    label="Priority"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="2" class="d-flex align-center">
                  <v-btn
                    variant="text"
                    color="primary"
                    @click="clearFilters"
                    block
                  >
                    Clear
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>

            <v-divider></v-divider>

            <v-table hover>
              <thead>
                <tr>
                  <th class="text-left">Task Title</th>
                  <th class="text-left">Project</th>
                  <th class="text-left">Status</th>
                  <th class="text-left">Priority / Score</th>
                  <th class="text-left">SLA Status</th>
                  <th class="text-left">Due Date</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="7" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                  </td>
                </tr>
                <tr v-else-if="tasks.data.length === 0">
                  <td colspan="7" class="text-center py-10">
                    <v-icon size="64" color="medium-emphasis" class="mb-4">mdi-clipboard-text-outline</v-icon>
                    <p class="text-h6 text-medium-emphasis">No tasks found.</p>
                  </td>
                </tr>
                <tr v-for="task in tasks.data" :key="task.id">
                  <td class="py-4">
                    <div class="d-flex flex-column">
                      <Link :href="route('crm.tasks.show', task.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary">
                        {{ task.title }}
                      </Link>
                      <span class="text-caption text-medium-emphasis text-truncate" style="max-width: 250px;">
                        {{ task.description }}
                      </span>
                    </div>
                  </td>
                  <td>
                    <Link v-if="task.project" :href="route('crm.projects.show', task.project.id)" class="text-body-2 text-decoration-none text-secondary">
                      {{ task.project.name }}
                    </Link>
                    <span v-else class="text-medium-emphasis">No Project</span>
                  </td>
                  <td>
                    <v-chip
                      :color="getStatusColor(task.status)"
                      size="x-small"
                      label
                      class="text-uppercase font-weight-bold"
                    >
                      {{ task.status.replace('_', ' ') }}
                    </v-chip>
                  </td>
                  <td>
                    <div class="d-flex align-center gap-2">
                      <v-chip
                        :color="getPriorityColor(task.priority)"
                        size="x-small"
                        variant="outlined"
                        class="text-uppercase font-weight-bold"
                      >
                        {{ task.priority }}
                      </v-chip>
                      <v-avatar size="24" :color="task.priority_score > 70 ? 'error' : 'warning'" class="text-white text-caption">
                        {{ task.priority_score }}
                      </v-avatar>
                    </div>
                  </td>
                  <td>
                    <v-chip
                      v-if="task.escalated_at"
                      color="error"
                      size="x-small"
                      prepend-icon="mdi-alert-octagon"
                      class="text-uppercase font-weight-bold"
                    >
                      Escalated
                    </v-chip>
                    <v-chip
                      v-else-if="task.sla_minutes"
                      color="success"
                      size="x-small"
                      variant="tonal"
                      class="text-uppercase font-weight-bold"
                    >
                      In SLA
                    </v-chip>
                    <span v-else class="text-caption text-medium-emphasis">N/A</span>
                  </td>
                  <td>
                    <span :class="{'text-error': task.due_date && new Date(task.due_date) < new Date()}">
                      {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : 'No date' }}
                    </span>
                  </td>
                  <td class="text-right">
                    <div class="d-flex justify-end">
                      <v-tooltip text="View Task">
                        <template v-slot:activator="{ props }">
                          <Link :href="route('crm.tasks.show', task.id)">
                            <v-btn icon="mdi-eye" variant="text" size="small" v-bind="props"></v-btn>
                          </Link>
                        </template>
                      </v-tooltip>
                      <v-tooltip text="Edit Task">
                        <template v-slot:activator="{ props }">
                          <Link :href="route('crm.tasks.edit', task.id)">
                            <v-btn icon="mdi-pencil" variant="text" size="small" v-bind="props"></v-btn>
                          </Link>
                        </template>
                      </v-tooltip>
                      <v-tooltip text="Delete Task">
                        <template v-slot:activator="{ props }">
                          <v-btn icon="mdi-delete" variant="text" size="small" color="error" v-bind="props" @click="deleteTask(task.id)"></v-btn>
                        </template>
                      </v-tooltip>
                    </div>
                  </td>
                </tr>
              </tbody>
            </v-table>

            <v-divider></v-divider>

            <v-card-text v-if="tasks.links.length > 3" class="pa-4 d-flex justify-center">
              <v-pagination
                v-model="tasks.current_page"
                :length="tasks.last_page"
                @update:modelValue="router.get(route('crm.tasks.index'), { page: $event, search: search, status: statusFilter, priority: priorityFilter }, { preserveState: true, preserveScroll: true, replace: true })"
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
