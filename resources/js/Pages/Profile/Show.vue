<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
const props = defineProps({ user: Object });
</script>

<template>
  <Head title="Profile" />
  <DashboardLayout>
    <v-row>
      <v-col cols="12" md="6">
        <v-card elevation="0" border>
          <v-card-item title="User Profile"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="mb-4">
              <v-avatar size="96">
                <img :src="props.user.avatar ? '/storage/' + props.user.avatar + '?' + new Date().getTime() : '/src/assets/images/users/avatar-6.png'" width="96" alt="avatar" />
              </v-avatar>
            </div>
            <div class="mb-3"><span class="text-subtitle-2">Name:</span> {{ user.name }}</div>
            <div class="mb-3"><span class="text-subtitle-2">Email:</span> {{ user.email }}</div>
            <Link :href="route('profile.edit')">
              <v-btn color="primary" flat>Edit Profile</v-btn>
            </Link>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
  </template>
