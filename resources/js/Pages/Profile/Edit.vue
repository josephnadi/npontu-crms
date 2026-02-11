<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
const props = defineProps({ user: Object });
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  avatar: null
});
const submit = () => form.post(route('profile.update'));
</script>

<template>
  <Head title="Edit Profile" />
  <DashboardLayout>
    <v-row>
      <v-col cols="12" md="6">
        <v-card elevation="0" border>
          <v-card-item title="Edit Profile"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <form @submit.prevent="submit">
              <div class="d-flex align-center mb-4">
                <v-avatar size="64" class="me-4">
                  <img :src="props.user.avatar ? '/storage/' + props.user.avatar + '?' + new Date().getTime() : '/src/assets/images/users/avatar-6.png'" width="64" alt="avatar" />
                </v-avatar>
                <v-file-input
                  v-model="form.avatar"
                  label="Upload Avatar"
                  accept="image/*"
                  variant="outlined"
                  density="comfortable"
                  :error-messages="form.errors.avatar"
                />
              </div>
              <v-text-field v-model="form.name" label="Name" :error-messages="form.errors.name" variant="outlined" density="comfortable" />
              <v-text-field v-model="form.email" label="Email" :error-messages="form.errors.email" variant="outlined" density="comfortable" />
              <v-text-field v-model="form.password" label="New Password" type="password" :error-messages="form.errors.password" variant="outlined" density="comfortable" />
              <v-text-field v-model="form.password_confirmation" label="Confirm Password" type="password" variant="outlined" density="comfortable" />
              <div class="mt-4 d-flex gap-2">
                <Link :href="route('profile.show')"><v-btn variant="outlined">Cancel</v-btn></Link>
                <v-btn type="submit" color="primary" :loading="form.processing" flat>Save Changes</v-btn>
              </div>
            </form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
