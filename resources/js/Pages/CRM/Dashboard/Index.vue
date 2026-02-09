<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { computed } from 'vue';

const props = defineProps({
  metrics: Object,
  pipelineData: Array,
  activityBreakdown: Array,
  recentActivities: Array,
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
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
        <v-card class="pa-4" elevation="0" border>
          <div class="d-flex align-center mb-2">
            <v-avatar color="blue-lighten-5" size="40" class="mr-3">
              <v-icon color="primary">mdi-account-group</v-icon>
            </v-avatar>
            <span class="text-subtitle-2 font-weight-medium">Total Contacts</span>
          </div>
          <div class="text-h4 font-weight-bold">{{ metrics.totalContacts }}</div>
          <div class="text-caption text-success mt-1">
            <v-icon size="small">mdi-trending-up</v-icon> 12% from last month
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="pa-4" elevation="0" border>
          <div class="d-flex align-center mb-2">
            <v-avatar color="green-lighten-5" size="40" class="mr-3">
              <v-icon color="success">mdi-briefcase-check</v-icon>
            </v-avatar>
            <span class="text-subtitle-2 font-weight-medium">Open Deals</span>
          </div>
          <div class="text-h4 font-weight-bold">{{ metrics.openDeals }}</div>
          <div class="text-caption text-success mt-1">
            <v-icon size="small">mdi-trending-up</v-icon> 5 new this week
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="pa-4" elevation="0" border>
          <div class="d-flex align-center mb-2">
            <v-avatar color="orange-lighten-5" size="40" class="mr-3">
              <v-icon color="warning">mdi-currency-usd</v-icon>
            </v-avatar>
            <span class="text-subtitle-2 font-weight-medium">Pipeline Value</span>
          </div>
          <div class="text-h4 font-weight-bold">{{ formatCurrency(metrics.totalValue) }}</div>
          <div class="text-caption text-warning mt-1">
            Expected by end of quarter
          </div>
        </v-card>
      </v-col>

      <v-col cols="12" sm="6" lg="3">
        <v-card class="pa-4" elevation="0" border>
          <div class="d-flex align-center mb-2">
            <v-avatar color="purple-lighten-5" size="40" class="mr-3">
              <v-icon color="secondary">mdi-calendar-clock</v-icon>
            </v-avatar>
            <span class="text-subtitle-2 font-weight-medium">Pending Tasks</span>
          </div>
          <div class="text-h4 font-weight-bold">{{ metrics.pendingActivities }}</div>
          <div class="text-caption text-error mt-1">
            3 overdue items
          </div>
        </v-card>
      </v-col>

      <!-- Charts Row -->
      <v-col cols="12" md="8">
        <v-card elevation="0" border>
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
        <v-card elevation="0" border height="100%">
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
        <v-card elevation="0" border>
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
                <div class="text-right">
                  <div class="text-caption font-weight-medium">{{ formatDate(activity.created_at) }}</div>
                  <v-chip size="x-small" :color="activity.status === 'completed' ? 'success' : 'warning'" variant="flat" class="mt-1">
                    {{ activity.status }}
                  </v-chip>
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
</style>
