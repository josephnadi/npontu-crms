<script setup>
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
const props = defineProps({ notifications: Object });
const markRead = (id) => router.post(route('notifications.read', id));
const markAllRead = () => router.post(route('notifications.readAll'));
</script>

<template>
  <Head title="Notifications" />
  <DashboardLayout>
    <v-row>
      <v-col cols="12">
        <v-card elevation="0" border>
          <v-card-item title="Notifications"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="d-flex justify-end mb-2">
              <v-btn variant="outlined" color="primary" @click="markAllRead">Mark All Read</v-btn>
            </div>
            <v-table>
              <thead>
                <tr>
                  <th class="text-left">Message</th>
                  <th class="text-left">Date</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="n in notifications.data" :key="n.id">
                  <td>{{ typeof n.data === 'string' ? n.data : JSON.stringify(n.data) }}</td>
                  <td>{{ new Date(n.created_at).toLocaleString() }}</td>
                  <td class="text-right">
                    <v-btn size="small" variant="text" color="success" @click="markRead(n.id)" v-if="!n.read_at">Mark Read</v-btn>
                  </td>
                </tr>
                <tr v-if="notifications.data.length === 0">
                  <td colspan="3" class="text-center py-8 text-medium-emphasis">No notifications</td>
                </tr>
              </tbody>
            </v-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
