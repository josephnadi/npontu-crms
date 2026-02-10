<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
  metrics: Object,
  pipelineData: Array,
  activityBreakdown: Array,
  recentActivities: Array,
  highPotentialLeads: Array,
  criticalTasks: Array,
});

// Real-time polling
let pollInterval;
onMounted(() => {
  pollInterval = setInterval(() => {
    router.reload({ 
      only: ['metrics', 'pipelineData', 'activityBreakdown', 'recentActivities', 'highPotentialLeads', 'criticalTasks'],
      preserveScroll: true 
    });
  }, 30000); // Poll every 30 seconds
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-GH', {
    style: 'currency',
    currency: 'GHS',
  }).format(value);
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Funnel Chart Options
const funnelChartOptions = computed(() => ({
  chart: {
    type: 'bar',
    height: 350,
    toolbar: { show: false }
  },
  plotOptions: {
    bar: {
      borderRadius: 4,
      horizontal: true,
      distributed: true,
      barHeight: '80%',
    }
  },
  colors: ['#4680ff', '#2ca87f', '#e58a00', '#dc2626', '#673ab7', '#3ec9d6'],
  dataLabels: {
    enabled: true,
    textAnchor: 'start',
    style: { colors: ['#fff'] },
    formatter: function (val, opt) {
      return opt.w.globals.labels[opt.dataPointIndex] + ": " + val
    },
    offsetX: 0,
  },
  xaxis: {
    categories: props.pipelineData.map(d => d.name),
  },
  yaxis: {
    labels: { show: false }
  },
  grid: {
    borderColor: '#f1f1f1',
  }
}));

const funnelChartSeries = computed(() => [{
  name: 'Deals',
  data: props.pipelineData.map(d => d.count)
}]);

// Activity Chart Options
const activityChartOptions = computed(() => ({
  chart: {
    type: 'donut',
    height: 300,
  },
  labels: props.activityBreakdown.map(d => d.type.charAt(0).toUpperCase() + d.type.slice(1)),
  colors: ['#4680ff', '#2ca87f', '#e58a00', '#dc2626', '#673ab7'],
  legend: {
    position: 'bottom'
  },
  plotOptions: {
    pie: {
      donut: {
        size: '70%'
      }
    }
  }
}));

const activityChartSeries = computed(() => props.activityBreakdown.map(d => d.count));

const getIcon = (type) => {
  switch (type) {
    case 'call': return 'custom-phone';
    case 'meeting': return 'custom-user-fill';
    case 'email': return 'custom-mail';
    case 'note': return 'custom-file-text';
    default: return 'custom-notification';
  }
};

const getColor = (type) => {
  switch (type) {
    case 'call': return 'primary';
    case 'meeting': return 'success';
    case 'email': return 'warning';
    case 'note': return 'info';
    default: return 'secondary';
  }
};
</script>

<template>
  <Head title="CRM Dashboard" />

  <DashboardLayout>
    <v-row>
      <!-- Page Header -->
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div>
          <h2 class="text-h3 font-weight-bold">CRM Dashboard</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Overview of your sales and activities</p>
        </div>
        <div class="d-flex gap-2">
          <Link :href="route('crm.deals.create')">
            <v-btn color="primary" prepend-icon="mdi-plus">Add Deal</v-btn>
          </Link>
          <Link :href="route('crm.activities.create')">
            <v-btn variant="outlined" color="primary" prepend-icon="mdi-calendar-plus">Log Activity</v-btn>
          </Link>
        </div>
      </v-col>

      <!-- Stat Cards -->
      <v-col cols="12" sm="6" lg="3">
        <Link :href="route('crm.contacts.index')" class="text-decoration-none">
          <v-card class="pa-4 stat-card" elevation="2">
            <div class="d-flex align-center mb-2">
              <v-avatar color="blue-lighten-5" size="40" class="mr-3 icon-avatar">
                <v-icon color="primary">mdi-account-group</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium text-high-emphasis">Total Contacts</span>
            </div>
            <div class="text-h4 font-weight-bold counter-value text-high-emphasis">{{ metrics.totalContacts }}</div>
            <div class="text-caption text-success mt-1">
              <v-icon size="small">mdi-trending-up</v-icon> 12% from last month
            </div>
          </v-card>
        </Link>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <Link :href="route('crm.deals.pipeline')" class="text-decoration-none">
          <v-card class="pa-4 stat-card" elevation="2">
            <div class="d-flex align-center mb-2">
              <v-avatar color="green-lighten-5" size="40" class="mr-3 icon-avatar">
                <v-icon color="success">mdi-briefcase-check</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium text-high-emphasis">Open Deals</span>
            </div>
            <div class="text-h4 font-weight-bold counter-value text-high-emphasis">{{ metrics.openDeals }}</div>
            <div class="text-caption text-success mt-1">
              <v-icon size="small">mdi-trending-up</v-icon> 5 new this week
            </div>
          </v-card>
        </Link>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <Link :href="route('crm.deals.index')" class="text-decoration-none">
          <v-card class="pa-4 stat-card" elevation="2">
            <div class="d-flex align-center mb-2">
              <v-avatar color="orange-lighten-5" size="40" class="mr-3 icon-avatar">
                <v-icon color="warning">mdi-currency-usd</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium text-high-emphasis">Pipeline Value</span>
            </div>
            <div class="text-h4 font-weight-bold counter-value text-high-emphasis">{{ formatCurrency(metrics.totalValue) }}</div>
            <div class="text-caption text-warning mt-1">
              Expected by end of quarter
            </div>
          </v-card>
        </Link>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <Link :href="route('crm.activities.index')" class="text-decoration-none">
          <v-card class="pa-4 stat-card" elevation="2">
            <div class="d-flex align-center mb-2">
              <v-avatar color="purple-lighten-5" size="40" class="mr-3 icon-avatar">
                <v-icon color="secondary">mdi-calendar-clock</v-icon>
              </v-avatar>
              <span class="text-subtitle-2 font-weight-medium text-high-emphasis">Pending Tasks</span>
            </div>
            <div class="text-h4 font-weight-bold counter-value text-high-emphasis">{{ metrics.pendingActivities }}</div>
            <div class="text-caption text-error mt-1">
              {{ metrics.slaBreachesCount }} SLA breaches
            </div>
          </v-card>
        </Link>
      </v-col>

      <!-- AI Insights Row -->
      <v-col cols="12" md="6">
        <v-card elevation="2">
          <v-card-item title="Lead Intelligence">
            <template v-slot:subtitle>
              <span class="text-success">{{ metrics.highPotentialLeadsCount }} High Potential Leads</span>
            </template>
          </v-card-item>
          <v-list lines="two">
            <v-list-item
              v-for="lead in highPotentialLeads"
              :key="lead.id"
              :title="lead.first_name + ' ' + lead.last_name"
              :subtitle="lead.company_name"
            >
              <template v-slot:prepend>
                <v-avatar color="primary-lighten-5">
                  <span class="text-primary font-weight-bold">{{ lead.score }}</span>
                </v-avatar>
              </template>
              <template v-slot:append>
                <v-chip :color="lead.score > 70 ? 'success' : 'warning'" size="small">
                  {{ lead.score > 70 ? 'Hot' : 'Warm' }}
                </v-chip>
              </template>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn variant="text" color="primary" block :href="route('crm.leads.index')">View All Leads</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card elevation="2">
          <v-card-item title="Task Intelligence">
            <template v-slot:subtitle>
              <span class="text-error">Priority & SLA Tracking</span>
            </template>
          </v-card-item>
          <v-list lines="two">
            <v-list-item
              v-for="task in criticalTasks"
              :key="task.id"
              :title="task.title"
              :subtitle="'Priority Score: ' + task.priority_score"
            >
              <template v-slot:prepend>
                <v-icon :color="task.priority === 'high' ? 'error' : 'warning'">
                  {{ task.priority === 'high' ? 'mdi-alert-circle' : 'mdi-alert' }}
                </v-icon>
              </template>
              <template v-slot:append>
                <div class="text-right">
                  <div class="text-caption font-weight-bold" :class="task.escalated_at ? 'text-error' : ''">
                    {{ task.escalated_at ? 'ESCALATED' : 'In SLA' }}
                  </div>
                  <v-progress-linear
                    :model-value="task.priority_score"
                    :color="task.priority_score > 70 ? 'error' : 'warning'"
                    height="4"
                    rounded
                  ></v-progress-linear>
                </div>
              </template>
            </v-list-item>
          </v-list>
          <v-divider></v-divider>
          <v-card-actions>
            <v-btn variant="text" color="primary" block :href="route('crm.activities.index')">Manage Tasks</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>

      <!-- Charts Row -->
      <v-col cols="12" md="8">
        <v-card elevation="2">
          <v-card-item title="Sales Pipeline Funnel">
            <template v-slot:append>
              <v-btn icon variant="text" size="small">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </template>
          </v-card-item>
          <v-card-text>
            <apexchart
              type="bar"
              height="350"
              :options="funnelChartOptions"
              :series="funnelChartSeries"
            ></apexchart>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card elevation="2" height="100%">
          <v-card-item title="Activity Breakdown"></v-card-item>
          <v-card-text class="d-flex flex-column align-center justify-center pt-8">
            <apexchart
              type="donut"
              width="100%"
              :options="activityChartOptions"
              :series="activityChartSeries"
            ></apexchart>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Recent Activities -->
      <v-col cols="12">
        <v-card elevation="2">
          <v-card-item title="Recent Activities">
            <template v-slot:append>
              <Link :href="route('crm.activities.index')">
                <v-btn variant="text" color="primary">View All</v-btn>
              </Link>
            </template>
          </v-card-item>
          <v-divider></v-divider>
          <v-list lines="two">
            <v-list-item
              v-for="activity in recentActivities"
              :key="activity.id"
              :prepend-icon="null"
            >
              <template v-slot:prepend>
                <v-avatar :color="getColor(activity.type)" size="40" class="mr-4">
                  <SvgSprite :name="getIcon(activity.type)" style="width: 20px; height: 20px; color: white" />
                </v-avatar>
              </template>
              
              <v-list-item-title class="font-weight-bold">
                {{ activity.subject }}
              </v-list-item-title>
              
              <v-list-item-subtitle>
                {{ activity.description }}
              </v-list-item-subtitle>

              <template v-slot:append>
                <div class="d-flex align-center">
                  <div class="text-right mr-3">
                    <div class="text-caption font-weight-medium">{{ formatDate(activity.created_at) }}</div>
                    <v-chip size="x-small" :color="activity.status === 'completed' ? 'success' : 'warning'" variant="flat" class="mt-1">
                      {{ activity.status }}
                    </v-chip>
                  </div>
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                    </template>
                    <v-list size="small">
                      <v-list-item 
                        v-if="activity.status === 'pending'"
                        @click="router.put(route('crm.activities.update', activity.id), { ...activity, status: 'completed' })" 
                        prepend-icon="mdi-check"
                        color="success"
                      >
                        <v-list-item-title>Mark Completed</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="router.delete(route('crm.activities.destroy', activity.id))" prepend-icon="mdi-delete" color="error">
                        <v-list-item-title>Delete</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </div>
              </template>
            </v-list-item>
            
            <v-list-item v-if="recentActivities.length === 0" class="text-center py-8">
              <div class="text-medium-emphasis">No recent activities found</div>
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

.stat-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  animation: cardEntrance 0.8s cubic-bezier(0.4, 0, 0.2, 1) backwards;
  cursor: pointer;
  z-index: 1;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, rgba(var(--v-theme-primary), 0.05) 0%, transparent 100%);
  opacity: 0;
  transition: opacity 0.4s ease;
  z-index: -1;
}

.stat-card:hover::before {
  opacity: 1;
}

.stat-card:hover {
  transform: translateY(-12px) scale(1.03);
  box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
  border-color: rgb(var(--v-theme-primary)) !important;
}

.icon-avatar {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover .icon-avatar {
  transform: scale(1.2) rotate(10deg);
  background-color: rgb(var(--v-theme-primary)) !important;
}

.stat-card:hover .icon-avatar .v-icon {
  color: white !important;
}

.counter-value {
  transition: all 0.3s ease;
}

.stat-card:hover .counter-value {
  color: rgb(var(--v-theme-primary)) !important;
  letter-spacing: 0.5px;
}

@keyframes cardEntrance {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    filter: blur(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
  }
}
</style>
