<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { computed } from 'vue';

const props = defineProps({
  activities: {
    type: Array,
    required: true
  }
});

const groupedActivities = computed(() => {
  const groups = {};
  props.activities.forEach(activity => {
    const date = new Date(activity.activity_date).toDateString();
    if (!groups[date]) {
      groups[date] = [];
    }
    groups[date].push(activity);
  });
  
  // Sort dates
  return Object.keys(groups)
    .sort((a, b) => new Date(a) - new Date(b))
    .reduce((acc, date) => {
      acc[date] = groups[date].sort((a, b) => new Date(a.activity_date) - new Date(b.activity_date));
      return acc;
    }, {});
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

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
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

const isToday = (dateString) => {
  return new Date(dateString).toDateString() === new Date().toDateString();
};
</script>

<template>
  <Head title="Activity Calendar" />
  
  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center">
        <div>
          <h2 class="text-h4 font-weight-bold">Activity Schedule</h2>
          <p class="text-subtitle-1 text-grey">View your upcoming calls and meetings</p>
        </div>
        <div class="d-flex gap-2">
          <Link :href="route('crm.activities.index')" class="text-decoration-none mr-2">
            <v-btn variant="outlined" prepend-icon="mdi-format-list-bulleted">List View</v-btn>
          </Link>
          <Link :href="route('crm.activities.create')" class="text-decoration-none">
            <v-btn color="primary" prepend-icon="mdi-plus">Add Activity</v-btn>
          </Link>
        </div>
      </v-col>

      <v-col cols="12" md="8" lg="6">
        <div v-if="Object.keys(groupedActivities).length === 0" class="text-center py-12">
          <v-icon icon="mdi-calendar-blank" size="64" color="grey-lighten-1" class="mb-4"></v-icon>
          <h3 class="text-h5 text-grey">No scheduled activities found</h3>
          <p class="text-grey mb-6">Start by adding a new call or meeting.</p>
          <Link :href="route('crm.activities.create')">
            <v-btn color="primary">Schedule Activity</v-btn>
          </Link>
        </div>

        <div v-for="(activities, date) in groupedActivities" :key="date" class="mb-8">
          <div class="d-flex align-center mb-4">
            <h3 :class="['text-h6 font-weight-bold', isToday(date) ? 'text-primary' : '']">
              {{ isToday(date) ? 'Today - ' : '' }}{{ date }}
            </h3>
            <v-divider class="ml-4"></v-divider>
          </div>

          <v-card v-for="activity in activities" :key="activity.id" class="mb-3 overflow-hidden" variant="outlined">
            <div class="d-flex">
              <div :class="['pa-4 d-flex flex-column align-center justify-center bg-' + getColor(activity.type)]" style="width: 80px">
                <SvgSprite :name="getIcon(activity.type)" style="width: 24px; height: 24px; color: white" />
                <span class="text-caption text-white mt-1 font-weight-bold">{{ formatTime(activity.activity_date) }}</span>
              </div>
              <div class="pa-4 flex-grow-1">
                <div class="d-flex justify-space-between align-start mb-1">
                  <div>
                    <h4 class="text-subtitle-1 font-weight-bold">{{ activity.subject }}</h4>
                    <div class="d-flex align-center mt-1">
                      <span class="text-caption text-grey mr-2">Related to:</span>
                      <Link :href="getEntityLink(activity)" class="text-caption text-primary text-decoration-none font-weight-bold">
                        {{ getEntityName(activity) }}
                      </Link>
                    </div>
                  </div>
                  <v-chip
                    :color="activity.status === 'completed' ? 'success' : 'warning'"
                    size="x-small"
                    variant="flat"
                    class="text-capitalize"
                  >
                    {{ activity.status }}
                  </v-chip>
                </div>
                <p class="text-body-2 text-medium-emphasis mt-2">{{ activity.description }}</p>
              </div>
              <div class="pa-2 d-flex flex-column justify-center">
                <v-btn icon="mdi-check" variant="text" size="small" color="success" v-if="activity.status !== 'completed'"></v-btn>
                <v-btn icon="mdi-pencil-outline" variant="text" size="small" color="grey"></v-btn>
              </div>
            </div>
          </v-card>
        </div>
      </v-col>
      
      <v-col cols="12" md="4">
        <v-card class="pa-4">
          <h3 class="text-h6 font-weight-bold mb-4">Quick Stats</h3>
          <v-list density="compact">
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="info" icon="mdi-phone"></v-icon>
              </template>
              <v-list-item-title>Pending Calls</v-list-item-title>
              <template v-slot:append>
                <v-chip size="small" color="info">2</v-chip>
              </template>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="primary" icon="mdi-account-group"></v-icon>
              </template>
              <v-list-item-title>Upcoming Meetings</v-list-item-title>
              <template v-slot:append>
                <v-chip size="small" color="primary">1</v-chip>
              </template>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
