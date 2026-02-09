<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  activities: {
    type: Array,
    required: true
  }
});

const getIcon = (type) => {
  switch (type) {
    case 'call': return 'custom-phone';
    case 'meeting': return 'custom-users';
    case 'email': return 'custom-mail';
    case 'task': return 'custom-check';
    case 'note': return 'custom-file-text';
    default: return 'custom-calendar';
  }
};

const getColor = (type) => {
  switch (type) {
    case 'call': return 'info';
    case 'meeting': return 'primary';
    case 'email': return 'warning';
    case 'task': return 'success';
    case 'note': return 'secondary';
    default: return 'grey';
  }
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleString();
};

const getEntityLink = (activity) => {
  if (!activity.activityable) return '#';
  const type = activity.activityable_type.split('\\').pop().toLowerCase();
  // We only have deals implemented so far
  if (type === 'deal') return window.route('crm.deals.show', activity.activityable_id);
  return '#';
};

const getEntityName = (activity) => {
  if (!activity.activityable) return 'Unknown';
  return activity.activityable.title || activity.activityable.name || 'View';
};
</script>

<template>
  <Head title="Activities" />
  
  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <h2 class="text-h4 font-weight-bold">Activities</h2>
        <div class="d-flex gap-2">
          <Link :href="route('crm.activities.calendar')" class="text-decoration-none mr-2">
            <v-btn variant="outlined" prepend-icon="mdi-calendar">Calendar View</v-btn>
          </Link>
          <Link :href="route('crm.activities.create')" class="text-decoration-none">
            <v-btn color="primary" prepend-icon="mdi-plus">Add Activity</v-btn>
          </Link>
        </div>
      </v-col>

      <v-col cols="12">
        <v-card>
          <v-table>
            <thead>
              <tr>
                <th class="text-left">Type</th>
                <th class="text-left">Subject</th>
                <th class="text-left">Related To</th>
                <th class="text-left">Date</th>
                <th class="text-left">Status</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="activity in activities" :key="activity.id">
                <td>
                  <div class="d-flex align-center">
                    <v-avatar :color="getColor(activity.type)" size="32" class="mr-3">
                      <SvgSprite :name="getIcon(activity.type)" style="width: 16px; height: 16px; color: white" />
                    </v-avatar>
                    <span class="text-capitalize">{{ activity.type }}</span>
                  </div>
                </td>
                <td>
                  <div class="font-weight-medium">{{ activity.subject }}</div>
                  <div class="text-caption text-grey text-truncate" style="max-width: 300px">
                    {{ activity.description }}
                  </div>
                </td>
                <td>
                  <Link :href="getEntityLink(activity)" class="text-primary text-decoration-none">
                    {{ getEntityName(activity) }}
                  </Link>
                </td>
                <td>{{ formatDate(activity.activity_date) }}</td>
                <td>
                  <v-chip
                    :color="activity.status === 'completed' ? 'success' : (activity.status === 'pending' ? 'warning' : 'error')"
                    size="x-small"
                    variant="tonal"
                    class="text-capitalize"
                  >
                    {{ activity.status }}
                  </v-chip>
                </td>
                <td class="text-right">
                  <v-btn icon="mdi-pencil-outline" variant="text" size="small"></v-btn>
                  <v-btn icon="mdi-delete-outline" variant="text" size="small" color="error"></v-btn>
                </td>
              </tr>
              <tr v-if="activities.length === 0">
                <td colspan="6" class="text-center py-8 text-grey">
                  No activities found.
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
