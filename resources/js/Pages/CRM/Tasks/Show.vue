<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  task: Object,
  suggestions: Array,
});

const deleteTask = () => {
  if (confirm('Are you sure you want to delete this task?')) {
    router.delete(route('crm.tasks.destroy', props.task.id));
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
</script>

<template>
  <Head :title="task.title" />
  <DashboardLayout>
    <v-container fluid>
      <v-row justify="center">
        <v-col cols="12" md="10">
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
                  <h1 class="text-h4 mb-1">{{ task.title }}</h1>
                  <div class="d-flex align-center gap-2">
                    <v-chip
                      :color="getStatusColor(task.status)"
                      size="small"
                      label
                      class="text-uppercase font-weight-bold"
                    >
                      {{ task.status.replace('_', ' ') }}
                    </v-chip>
                    <v-chip
                      :color="getPriorityColor(task.priority)"
                      size="small"
                      variant="outlined"
                      class="text-uppercase font-weight-bold"
                    >
                      {{ task.priority }} Priority
                    </v-chip>
                  </div>
                </div>
              </div>
              <div class="d-flex gap-2">
                <v-btn
                  color="primary"
                  prepend-icon="mdi-pencil"
                  :to="route('crm.tasks.edit', task.id)"
                >
                  Edit Task
                </v-btn>
                <v-btn
                  color="error"
                  variant="outlined"
                  prepend-icon="mdi-delete"
                  @click="deleteTask"
                >
                  Delete
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <v-row>
            <v-col cols="12" md="8">
              <v-card elevation="0" class="mb-6">
                <v-card-title class="pa-4">Description</v-card-title>
                <v-divider></v-divider>
                <v-card-text class="pa-4">
                  <p class="text-body-1 whitespace-pre-wrap">{{ task.description || 'No description provided.' }}</p>
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" md="4">
              <v-card elevation="0" border class="mb-6 overflow-hidden">
                <v-card-title class="pa-4 bg-grey-lighten-4 d-flex align-center">
                  <v-icon color="primary" class="mr-2">mdi-brain</v-icon>
                  Task Intelligence
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text class="pa-4">
                  <div class="d-flex align-center justify-space-between mb-4">
                    <span class="text-subtitle-2 font-weight-bold">Priority Score</span>
                    <v-chip
                      :color="task.priority_score > 70 ? 'error' : (task.priority_score > 40 ? 'warning' : 'success')"
                      size="small"
                      class="font-weight-bold"
                    >
                      {{ task.priority_score }}/100
                    </v-chip>
                  </div>
                  <v-progress-linear
                    :model-value="task.priority_score"
                    :color="task.priority_score > 70 ? 'error' : (task.priority_score > 40 ? 'warning' : 'success')"
                    height="8"
                    rounded
                    class="mb-4"
                  ></v-progress-linear>

                  <div v-if="task.sla_minutes" class="mb-4">
                    <div class="text-caption text-medium-emphasis mb-1">SLA Deadline</div>
                    <div class="d-flex align-center gap-2">
                      <v-icon size="16" :color="task.escalated_at ? 'error' : 'success'">
                        {{ task.escalated_at ? 'mdi-alert-circle' : 'mdi-check-circle' }}
                      </v-icon>
                      <span class="text-body-2 font-weight-bold">
                        {{ task.escalated_at ? 'Escalated' : 'Within SLA' }}
                      </span>
                    </div>
                  </div>

                  <v-divider class="my-4"></v-divider>
                  
                  <div class="text-subtitle-2 font-weight-bold mb-3">Suggested Next Actions</div>
                  <template v-if="suggestions && suggestions.length > 0">
                    <v-list class="pa-0">
                      <v-list-item
                        v-for="(suggestion, idx) in suggestions"
                        :key="idx"
                        class="pa-2 border rounded mb-2 hover-bg"
                        lines="two"
                      >
                        <template v-slot:prepend>
                          <v-avatar color="primary-lighten-5" size="32">
                            <v-icon color="primary" size="18">
                              {{ suggestion.type === 'escalate' ? 'mdi-trending-up' : 'mdi-phone' }}
                            </v-icon>
                          </v-avatar>
                        </template>
                        <v-list-item-title class="text-body-2 font-weight-bold">{{ suggestion.label }}</v-list-item-title>
                        <v-list-item-subtitle class="text-caption">{{ suggestion.description }}</v-list-item-subtitle>
                      </v-list-item>
                    </v-list>
                  </template>
                  <div v-else class="text-center py-4 text-caption text-medium-emphasis">
                    No urgent suggestions at this time.
                  </div>
                </v-card-text>
              </v-card>

              <v-card elevation="0" class="mb-6">
                <v-card-title class="pa-4">Details</v-card-title>
                <v-divider></v-divider>
                <v-list class="pa-0">
                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="primary">mdi-calendar</v-icon>
                    </template>
                    <v-list-item-title class="text-caption text-medium-emphasis">Due Date</v-list-item-title>
                    <v-list-item-subtitle class="text-body-2 font-weight-bold">
                      {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : 'No due date' }}
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="primary">mdi-briefcase-outline</v-icon>
                    </template>
                    <v-list-item-title class="text-caption text-medium-emphasis">Project</v-list-item-title>
                    <v-list-item-subtitle class="text-body-2 font-weight-bold">
                      <Link v-if="task.project" :href="route('crm.projects.show', task.project.id)" class="text-decoration-none text-primary">
                        {{ task.project.name }}
                      </Link>
                      <span v-else>None</span>
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="primary">mdi-account-outline</v-icon>
                    </template>
                    <v-list-item-title class="text-caption text-medium-emphasis">Assigned To</v-list-item-title>
                    <v-list-item-subtitle class="text-body-2 font-weight-bold">
                      {{ task.assignee ? task.assignee.name : 'Unassigned' }}
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="primary">mdi-account-key-outline</v-icon>
                    </template>
                    <v-list-item-title class="text-caption text-medium-emphasis">Owner</v-list-item-title>
                    <v-list-item-subtitle class="text-body-2 font-weight-bold">
                      {{ task.owner.name }}
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template v-slot:prepend>
                      <v-icon color="primary">mdi-clock-outline</v-icon>
                    </template>
                    <v-list-item-title class="text-caption text-medium-emphasis">Created At</v-list-item-title>
                    <v-list-item-subtitle class="text-body-2">
                      {{ new Date(task.created_at).toLocaleString() }}
                    </v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-card>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.whitespace-pre-wrap {
  white-space: pre-wrap;
}
.gap-2 {
  gap: 8px;
}
</style>
