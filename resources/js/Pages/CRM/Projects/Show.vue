<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, computed } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
  project: Object,
  invoices: Array,
  financials: Object,
});

const tab = ref('overview');
const taskViewMode = ref('table');

const taskStatuses = [
  { value: 'pending', title: 'Pending', color: 'warning' },
  { value: 'in_progress', title: 'In Progress', color: 'info' },
  { value: 'completed', title: 'Completed', color: 'success' },
];

const tasksByStatus = computed(() => {
  const grouped = {
    pending: [],
    in_progress: [],
    completed: [],
  };
  props.project.tasks.forEach(task => {
    if (grouped[task.status]) {
      grouped[task.status].push(task);
    }
  });
  return grouped;
});

const onTaskDrop = (evt, newStatus) => {
  if (evt.added) {
    const task = evt.added.element;
    updateTaskStatus(task, newStatus);
  }
};

const updateTaskStatus = (task, newStatus) => {
  router.patch(route('crm.tasks.update', task.id), {
    status: newStatus,
    title: task.title,
    priority: task.priority,
  }, {
    preserveScroll: true,
  });
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

const formatDate = (date) => {
  if (!date) return 'Not set';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const deleteProject = () => {
  if (confirm('Are you sure you want to delete this project?')) {
    router.delete(route('crm.projects.destroy', props.project.id));
  }
};

const toggleTaskStatus = (task) => {
  const newStatus = task.status === 'completed' ? 'pending' : 'completed';
  router.patch(route('crm.tasks.update', task.id), {
    status: newStatus,
    title: task.title, // Required by validation
    priority: task.priority, // Required by validation
  }, {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head :title="project.name" />
  <DashboardLayout>
    <v-container fluid>
      <!-- Header -->
      <v-row>
        <v-col cols="12">
          <v-card elevation="0" class="mb-6">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div class="d-flex align-center">
                <v-btn
                  icon="mdi-arrow-left"
                  variant="text"
                  class="mr-2"
                  @click="$window.history.back()"
                ></v-btn>
                <div>
                  <div class="d-flex align-center gap-2">
                    <h1 class="text-h5 mb-0">{{ project.name }}</h1>
                    <v-chip
                      :color="getPriorityColor(project.priority)"
                      size="x-small"
                      variant="tonal"
                      class="text-uppercase font-weight-bold"
                    >
                      {{ project.priority }}
                    </v-chip>
                  </div>
                  <v-chip
                    :color="getStatusColor(project.status)"
                    size="small"
                    label
                    class="mt-1 text-uppercase font-weight-bold"
                  >
                    {{ project.status.replace('_', ' ') }}
                  </v-chip>
                </div>
              </div>
              <div class="d-flex gap-2">
                <v-btn
                  color="primary"
                  variant="outlined"
                  prepend-icon="mdi-pencil"
                  :to="route('crm.projects.edit', project.id)"
                >
                  Edit
                </v-btn>
                <v-btn
                  color="error"
                  variant="outlined"
                  prepend-icon="mdi-delete"
                  @click="deleteProject"
                >
                  Delete
                </v-btn>
              </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-tabs v-model="tab" color="primary">
              <v-tab value="overview" prepend-icon="mdi-information-outline">Overview</v-tab>
              <v-tab value="tasks" prepend-icon="mdi-checkbox-marked-circle-outline">Tasks ({{ project.tasks.length }})</v-tab>
              <v-tab value="financials" prepend-icon="mdi-cash-multiple">Financials</v-tab>
              <v-tab value="activities" prepend-icon="mdi-history">Activities</v-tab>
            </v-tabs>
          </v-card>
        </v-col>
      </v-row>

      <v-window v-model="tab">
        <!-- Overview Tab -->
        <v-window-item value="overview">
          <v-row>
            <v-col cols="12" md="8">
              <v-card border elevation="0" class="mb-6">
                <v-card-text class="pa-6">
                  <div class="d-flex justify-space-between align-center mb-6">
                    <h3 class="text-h6 font-weight-bold">Project Progress</h3>
                    <span class="text-h5 font-weight-bold text-primary">{{ project.progress }}%</span>
                  </div>
                  <v-progress-linear
                    v-model="project.progress"
                    color="primary"
                    height="12"
                    rounded
                    class="mb-6"
                  ></v-progress-linear>

                  <h3 class="text-subtitle-1 font-weight-bold mb-2">Description</h3>
                  <p class="text-body-1 mb-6 text-medium-emphasis">
                    {{ project.description || 'No description provided.' }}
                  </p>

                  <v-divider class="mb-6"></v-divider>

                  <v-row>
                    <v-col cols="12" sm="6" md="3">
                      <div class="mb-4">
                        <span class="text-caption text-medium-emphasis d-block">Start Date</span>
                        <span class="text-body-1 font-weight-medium">{{ formatDate(project.start_date) }}</span>
                      </div>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <div class="mb-4">
                        <span class="text-caption text-medium-emphasis d-block">End Date</span>
                        <span class="text-body-1 font-weight-medium">{{ formatDate(project.end_date) }}</span>
                      </div>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <div class="mb-4">
                        <span class="text-caption text-medium-emphasis d-block">Budget</span>
                        <span class="text-body-1 font-weight-bold text-success">{{ formatCurrency(project.budget) }}</span>
                      </div>
                    </v-col>
                    <v-col cols="12" sm="6" md="3">
                      <div class="mb-4">
                        <span class="text-caption text-medium-emphasis d-block">Owner</span>
                        <span class="text-body-1 font-weight-medium">{{ project.owner.name }}</span>
                      </div>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>

              <!-- Recent Tasks Preview -->
              <v-card border elevation="0" class="mb-6">
                <v-card-text class="pa-4 d-flex justify-space-between align-center">
                  <h3 class="text-h6 font-weight-bold">Recent Tasks</h3>
                  <v-btn variant="text" color="primary" @click="tab = 'tasks'">View All</v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-list v-if="project.tasks.length > 0">
                  <v-list-item v-for="task in project.tasks.slice(0, 5)" :key="task.id" :title="task.title">
                    <template v-slot:prepend>
                      <v-checkbox-btn
                        :model-value="task.status === 'completed'"
                        @click.stop="toggleTaskStatus(task)"
                        color="success"
                      ></v-checkbox-btn>
                    </template>
                    <template v-slot:subtitle>
                      <span :class="{'text-error': task.due_date && new Date(task.due_date) < new Date() && task.status !== 'completed'}">
                        Due: {{ formatDate(task.due_date) }}
                      </span>
                    </template>
                    <template v-slot:append>
                      <v-chip size="x-small" :color="task.priority === 'high' ? 'error' : 'grey'">{{ task.priority }}</v-chip>
                    </template>
                  </v-list-item>
                </v-list>
                <v-card-text v-else class="text-center py-6 text-medium-emphasis">
                  No tasks assigned to this project.
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" md="4">
              <!-- Client Card -->
              <v-card border elevation="0" class="mb-6">
                <v-card-text class="pa-6">
                  <h3 class="text-subtitle-1 font-weight-bold mb-4">Client Information</h3>
                  <div v-if="project.client">
                    <div class="d-flex align-center mb-4">
                      <v-avatar color="primary" size="48" class="mr-4 text-white">
                        {{ project.client.name.charAt(0) }}
                      </v-avatar>
                      <div>
                        <Link :href="route('crm.clients.show', project.client.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary d-block">
                          {{ project.client.name }}
                        </Link>
                        <span class="text-caption text-medium-emphasis">{{ project.client.email }}</span>
                      </div>
                    </div>
                    <v-btn block variant="outlined" size="small" :to="route('crm.clients.show', project.client.id)">
                      View Client Profile
                    </v-btn>
                  </div>
                  <div v-else class="text-center py-4 text-medium-emphasis">
                    <v-icon size="48" class="mb-2">mdi-account-off-outline</v-icon>
                    <p>No client assigned</p>
                  </div>
                </v-card-text>
              </v-card>

              <!-- Deal Card -->
              <v-card border elevation="0" class="mb-6" v-if="project.deal">
                <v-card-text class="pa-6">
                  <h3 class="text-subtitle-1 font-weight-bold mb-4">Source Deal</h3>
                  <div class="d-flex align-center mb-4">
                    <v-avatar color="success" size="48" variant="tonal" class="mr-4">
                      <v-icon>mdi-handshake-outline</v-icon>
                    </v-avatar>
                    <div>
                      <Link :href="route('crm.deals.show', project.deal.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary d-block">
                        {{ project.deal.title }}
                      </Link>
                      <span class="text-caption text-medium-emphasis">Value: {{ formatCurrency(project.deal.value) }}</span>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-window-item>

        <!-- Tasks Tab -->
        <v-window-item value="tasks">
          <v-card border elevation="0">
            <v-card-text class="pa-4 d-flex justify-space-between align-center">
              <div class="d-flex align-center gap-4">
                <h3 class="text-h6 font-weight-bold mb-0">Project Tasks</h3>
                <v-btn-toggle
                  v-model="taskViewMode"
                  mandatory
                  density="compact"
                  color="primary"
                  variant="outlined"
                >
                  <v-btn value="table" icon="mdi-table-large" size="small"></v-btn>
                  <v-btn value="board" icon="mdi-view-column" size="small"></v-btn>
                </v-btn-toggle>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                size="small"
                :to="route('crm.tasks.create', { project_id: project.id })"
              >
                Add Task
              </v-btn>
            </v-card-text>
            <v-divider></v-divider>

            <!-- Table View -->
            <v-table v-if="taskViewMode === 'table'">
              <thead>
                <tr>
                  <th style="width: 50px;"></th>
                  <th>Task</th>
                  <th>Priority</th>
                  <th>Due Date</th>
                  <th>Assigned To</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="task in project.tasks" :key="task.id">
                  <td>
                    <v-checkbox-btn
                      :model-value="task.status === 'completed'"
                      @click.stop="toggleTaskStatus(task)"
                      color="success"
                    ></v-checkbox-btn>
                  </td>
                  <td>
                    <div :class="{'text-decoration-line-through text-medium-emphasis': task.status === 'completed'}">
                      <Link :href="route('crm.tasks.show', task.id)" class="text-decoration-none text-high-emphasis">
                        {{ task.title }}
                      </Link>
                    </div>
                  </td>
                  <td>
                    <v-chip size="x-small" :color="task.priority === 'high' ? 'error' : 'grey'">{{ task.priority }}</v-chip>
                  </td>
                  <td>
                    <span :class="{'text-error': task.due_date && new Date(task.due_date) < new Date() && task.status !== 'completed'}">
                      {{ formatDate(task.due_date) }}
                    </span>
                  </td>
                  <td>{{ task.assigned_to_name || 'Unassigned' }}</td>
                  <td class="text-right">
                    <v-btn icon="mdi-pencil" variant="text" size="x-small" :to="route('crm.tasks.edit', task.id)"></v-btn>
                    <v-btn icon="mdi-delete" variant="text" size="x-small" color="error" @click="router.delete(route('crm.tasks.destroy', task.id))"></v-btn>
                  </td>
                </tr>
                <tr v-if="project.tasks.length === 0">
                  <td colspan="6" class="text-center py-10 text-medium-emphasis">
                    No tasks found for this project.
                  </td>
                </tr>
              </tbody>
            </v-table>

            <!-- Board View -->
            <v-card-text v-else class="pa-4 bg-grey-lighten-4">
              <v-row>
                <v-col
                  v-for="status in taskStatuses"
                  :key="status.value"
                  cols="12"
                  md="4"
                >
                  <div class="d-flex align-center mb-3">
                    <v-chip
                      :color="status.color"
                      size="small"
                      class="mr-2"
                      label
                    >
                      {{ tasksByStatus[status.value].length }}
                    </v-chip>
                    <span class="text-subtitle-1 font-weight-bold">{{ status.title }}</span>
                  </div>

                  <draggable
                    :list="tasksByStatus[status.value]"
                    group="tasks"
                    item-key="id"
                    class="kanban-column"
                    @change="(evt) => onTaskDrop(evt, status.value)"
                  >
                    <template #item="{ element }">
                      <v-card
                        border
                        elevation="0"
                        class="mb-3 task-card"
                        :to="route('crm.tasks.show', element.id)"
                      >
                        <v-card-text class="pa-3">
                          <div class="d-flex justify-space-between align-start mb-2">
                            <span class="text-body-2 font-weight-bold">{{ element.title }}</span>
                            <v-chip
                              size="x-small"
                              :color="getPriorityColor(element.priority)"
                              variant="tonal"
                            >
                              {{ element.priority }}
                            </v-chip>
                          </div>
                          <div class="d-flex align-center justify-space-between mt-4">
                            <div class="d-flex align-center">
                              <v-icon size="small" color="medium-emphasis" class="mr-1">mdi-calendar-clock</v-icon>
                              <span class="text-caption text-medium-emphasis">
                                {{ formatDate(element.due_date) }}
                              </span>
                            </div>
                            <v-avatar size="24" color="grey-lighten-2">
                              <span class="text-caption">{{ element.assigned_to_name ? element.assigned_to_name.charAt(0) : '?' }}</span>
                            </v-avatar>
                          </div>
                        </v-card-text>
                      </v-card>
                    </template>
                  </draggable>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-window-item>

        <!-- Financials Tab -->
        <v-window-item value="financials">
          <v-row>
            <v-col cols="12" md="3">
              <v-card border elevation="0" class="text-center pa-4 h-100">
                <span class="text-caption text-medium-emphasis d-block mb-1">Total Budget</span>
                <span class="text-h5 font-weight-bold">{{ formatCurrency(project.budget) }}</span>
                <v-progress-circular
                  :model-value="financials.budget_usage_percent"
                  :color="financials.budget_usage_percent > 100 ? 'error' : 'primary'"
                  size="64"
                  width="6"
                  class="mt-4"
                >
                  <span class="text-caption">{{ financials.budget_usage_percent }}%</span>
                </v-progress-circular>
                <span class="text-caption d-block mt-2">Budget Usage</span>
              </v-card>
            </v-col>
            <v-col cols="12" md="3">
              <v-card border elevation="0" class="text-center pa-4 h-100">
                <span class="text-caption text-medium-emphasis d-block mb-1">Total Invoiced</span>
                <span class="text-h5 font-weight-bold text-primary">{{ formatCurrency(financials.total_invoiced) }}</span>
                <div class="mt-4 text-left">
                  <div class="d-flex justify-space-between mb-1">
                    <span class="text-caption">Paid</span>
                    <span class="text-caption font-weight-bold text-success">{{ formatCurrency(financials.paid_amount) }}</span>
                  </div>
                  <v-progress-linear :model-value="(financials.paid_amount / financials.total_invoiced) * 100" color="success" height="4" rounded></v-progress-linear>
                </div>
              </v-card>
            </v-col>
            <v-col cols="12" md="3">
              <v-card border elevation="0" class="text-center pa-4 h-100">
                <span class="text-caption text-medium-emphasis d-block mb-1">Pending Invoices</span>
                <span class="text-h5 font-weight-bold text-warning">{{ formatCurrency(financials.pending_amount) }}</span>
                <v-icon color="warning" size="48" class="mt-4">mdi-clock-outline</v-icon>
              </v-card>
            </v-col>
            <v-col cols="12" md="3">
              <v-card border elevation="0" class="text-center pa-4 h-100">
                <span class="text-caption text-medium-emphasis d-block mb-1">Overdue Amount</span>
                <span class="text-h5 font-weight-bold text-error">{{ formatCurrency(financials.overdue_amount) }}</span>
                <v-icon color="error" size="48" class="mt-4">mdi-alert-circle-outline</v-icon>
              </v-card>
            </v-col>

            <v-col cols="12">
              <v-card border elevation="0">
                <v-card-text class="pa-4 d-flex justify-space-between align-center">
                  <h3 class="text-h6 font-weight-bold">Project Invoices</h3>
                  <v-btn
                    color="primary"
                    prepend-icon="mdi-plus"
                    size="small"
                    :to="route('crm.invoices.create', { project_id: project.id })"
                  >
                    Create Invoice
                  </v-btn>
                </v-card-text>
                <v-divider></v-divider>
                <v-table>
                  <thead>
                    <tr>
                      <th>Invoice #</th>
                      <th>Status</th>
                      <th>Issue Date</th>
                      <th>Due Date</th>
                      <th class="text-right">Amount</th>
                      <th class="text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="invoice in invoices" :key="invoice.id">
                      <td class="font-weight-bold">{{ invoice.invoice_number }}</td>
                      <td>
                        <v-chip size="x-small" :color="invoice.status === 'paid' ? 'success' : (invoice.status === 'overdue' ? 'error' : 'warning')" label class="text-uppercase">
                          {{ invoice.status }}
                        </v-chip>
                      </td>
                      <td>{{ formatDate(invoice.issue_date) }}</td>
                      <td>{{ formatDate(invoice.due_date) }}</td>
                      <td class="text-right font-weight-bold">{{ formatCurrency(invoice.total_amount) }}</td>
                      <td class="text-right">
                        <v-btn icon="mdi-eye-outline" variant="text" size="x-small" :to="route('crm.invoices.show', invoice.id)"></v-btn>
                        <v-btn icon="mdi-pencil" variant="text" size="x-small" :to="route('crm.invoices.edit', invoice.id)"></v-btn>
                      </td>
                    </tr>
                    <tr v-if="invoices.length === 0">
                      <td colspan="6" class="text-center py-10 text-medium-emphasis">
                        No invoices found for this project.
                      </td>
                    </tr>
                  </tbody>
                </v-table>
              </v-card>
            </v-col>
          </v-row>
        </v-window-item>

        <!-- Activities Tab -->
        <v-window-item value="activities">
          <v-card border elevation="0">
            <v-card-text class="pa-4 d-flex justify-space-between align-center">
              <h3 class="text-h6 font-weight-bold">Activity Log</h3>
              <v-btn
                color="primary"
                variant="outlined"
                prepend-icon="mdi-plus"
                size="small"
                :to="route('crm.activities.create', { activityable_type: 'Project', activityable_id: project.id })"
              >
                Log Activity
              </v-btn>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-text class="pa-6">
              <v-timeline side="end" align="start">
                <v-timeline-item
                  v-for="activity in project.activities"
                  :key="activity.id"
                  :dot-color="getStatusColor(activity.status)"
                  size="small"
                >
                  <template v-slot:opposite>
                    <span class="text-caption text-medium-emphasis">{{ formatDate(activity.activity_date) }}</span>
                  </template>
                  <div class="mb-4">
                    <div class="d-flex justify-space-between align-center mb-1">
                      <h4 class="text-subtitle-1 font-weight-bold">{{ activity.subject }}</h4>
                      <v-chip size="x-small" label class="text-uppercase">{{ activity.type }}</v-chip>
                    </div>
                    <p class="text-body-2 text-medium-emphasis">{{ activity.description }}</p>
                  </div>
                </v-timeline-item>
              </v-timeline>
              <div v-if="project.activities.length === 0" class="text-center py-10 text-medium-emphasis">
                No activities logged for this project.
              </div>
            </v-card-text>
          </v-card>
        </v-window-item>
      </v-window>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.kanban-column {
  min-height: 400px;
  border-radius: 8px;
}

.task-card {
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: grab;
}

.task-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.task-card:active {
  cursor: grabbing;
}

.gap-2 {
  gap: 8px;
}

.gap-4 {
  gap: 16px;
}
</style>
