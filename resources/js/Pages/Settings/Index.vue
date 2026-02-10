<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
const props = defineProps({ setting: Object });
const form = useForm({
  locale: props.setting.locale,
  timezone: props.setting.timezone,
  currency: props.setting.currency,
  theme: props.setting.theme,
  notif_in_app: props.setting.notif_in_app,
  notif_email: props.setting.notif_email,
  notif_due_reminders: props.setting.notif_due_reminders,
});
const submit = () => form.post(route('settings.update'));
</script>

<template>
  <Head title="Settings" />
  <DashboardLayout>
    <v-row>
      <v-col cols="12" md="6">
        <v-card elevation="0" border>
          <v-card-item title="User Settings"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <form @submit.prevent="submit">
              <v-select v-model="form.locale" :items="['en']" label="Language" variant="outlined" density="comfortable" />
              <v-text-field v-model="form.timezone" label="Timezone" variant="outlined" density="comfortable" />
              <v-text-field v-model="form.currency" label="Currency" variant="outlined" density="comfortable" />
              <v-select v-model="form.theme" :items="['light', 'dark']" label="Theme" variant="outlined" density="comfortable" />
              <v-switch v-model="form.notif_in_app" label="In-app notifications" color="primary" />
              <v-switch v-model="form.notif_email" label="Email notifications" color="primary" />
              <v-switch v-model="form.notif_due_reminders" label="Activity due reminders" color="primary" />
              <div class="mt-4 d-flex gap-2">
                <v-btn type="submit" color="primary" :loading="form.processing" flat>Save Settings</v-btn>
              </div>
            </form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
