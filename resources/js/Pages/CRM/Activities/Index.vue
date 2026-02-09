<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  activities: {
    type: Array,
    required: true
  }
});

const search = ref('');
const typeFilter = ref('all');
const statusFilter = ref('all');

const filteredActivities = computed(() => {
  return props.activities.filter(activity => {
    const matchesSearch = activity.subject.toLowerCase().includes(search.value.toLowerCase()) ||
                         activity.description?.toLowerCase().includes(search.value.toLowerCase());
    const matchesType = typeFilter.value === 'all' || activity.type === typeFilter.value;
    const matchesStatus = statusFilter.value === 'all' || activity.status === statusFilter.value;
    return matchesSearch && matchesType && matchesStatus;
  });
});

const deleteActivity = (id) => {
  if (confirm('Are you sure you want to delete this activity?')) {
    router.delete(window.route('crm.activities.destroy', id));
  }
};

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
  switch (type) {
    case 'deal': return window.route('crm.deals.show', activity.activityable_id);
    case 'lead': return window.route('crm.leads.show', activity.activityable_id);
    case 'contact': return window.route('crm.contacts.show', activity.activityable_id);
    case 'client': return window.route('crm.clients.show', activity.activityable_id);
    default: return '#';
  }
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
        <v-card class="mb-4">
          <v-card-text>
            <v-row align="center">
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="search"
                  label="Search activities..."
                  prepend-inner-icon="mdi-magnify"
                  hide-details
                  variant="outlined"
                  density="comfortable"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="typeFilter"
                  :items="['all', 'call', 'meeting', 'email', 'task', 'note']"
                  label="Type"
                  hide-details
                  variant="outlined"
                  density="comfortable"
                  class="text-capitalize"
                ></v-select>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="statusFilter"
                  :items="['all', 'pending', 'completed', 'cancelled']"
                  label="Status"
                  hide-details
                  variant="outlined"
                  density="comfortable"
                  class="text-capitalize"
                ></v-select>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

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
              <tr v-for="activity in filteredActivities" :key="activity.id">
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
                  <div class="text-caption text-grey mb-1">
                    {{ activity.activityable_type.split('\\').pop() }}
                  </div>
                  <Link :href="getEntityLink(activity)" class="text-primary text-decoration-none font-weight-medium">
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
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                    </template>
                    <v-list size="small">
                      <v-list-item @click="router.get(getEntityLink(activity))" prepend-icon="mdi-eye">
                        <v-list-item-title>View Related</v-list-item-title>
                      </v-list-item>
                      <v-list-item 
                        v-if="activity.status === 'pending'"
                        @click="router.put(route('crm.activities.update', activity.id), { ...activity, status: 'completed' })" 
                        prepend-icon="mdi-check"
                        color="success"
                      >
                        <v-list-item-title>Mark Completed</v-list-item-title>
                      </v-list-item>
                      <v-divider></v-divider>
                      <v-list-item @click="deleteActivity(activity.id)" prepend-icon="mdi-delete" color="error">
                        <v-list-item-title>Delete</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
              <tr v-if="filteredActivities.length === 0">
                <td colspan="6" class="text-center py-8 text-grey">
                  No activities found matching your criteria.
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
