<script setup lang="ts">
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { router, usePage, Link } from '@inertiajs/vue3';
const page = usePage();
const notifications = page.props.notifications || { count: 0, latest: [] };
const markAllRead = () => router.post('/notifications/read-all');
</script>

<template>
  <!-- ---------------------------------------------- -->
  <!-- notifications DD -->
  <!-- ---------------------------------------------- -->
  <v-menu :close-on-content-click="false" offset="6, 0">
    <template v-slot:activator="{ props }">
      <v-btn icon class="text-secondary ms-sm-2 ms-1" color="secondary" rounded="sm" v-bind="props">
        <v-badge color="success" :content="notifications.count" offset-x="-2" offset-y="-2">
          <SvgSprite name="custom-notification" />
        </v-badge>
      </v-btn>
    </template>
    <v-sheet rounded="md" width="420" class="notification-dropdown py-6">
      <div class="d-flex align-center justify-space-between pb-4 px-6">
        <h5 class="text-h5 mb-0">Notifications</h5>
        <a href="#" class="text-primary link-hover text-h6" @click.prevent="markAllRead"> Mark all read </a>
      </div>
      <perfect-scrollbar style="height: calc(100vh - 300px); max-height: 430px">
        <v-list class="py-0 px-6" lines="two" aria-label="notification list" aria-busy="true">
          <v-list-item
            v-for="n in notifications.latest"
            :key="n.id"
            color="secondary"
            class="no-spacer py-5 mb-3 px-3"
            rounded="md"
          >
            <template v-slot:prepend>
              <v-avatar size="40" variant="flat" color="primary" class="me-3 py-2">
                <SvgSprite name="custom-notification" />
              </v-avatar>
            </template>
            <div class="d-inline-flex justify-space-between w-100">
              <h6 class="text-h6 text-lightText mb-0">{{ typeof n.data === 'string' ? n.data : JSON.stringify(n.data) }}</h6>
              <span class="text-caption text-lightText ms-3" style="min-width: 48px">{{ new Date(n.created_at).toLocaleTimeString() }}</span>
            </div>
          </v-list-item>
        </v-list>
      </perfect-scrollbar>
      <div class="pt-2 text-center">
        <Link :href="route('notifications.index')" class="text-primary text-h6 link-hover">View All</Link>
      </div>
    </v-sheet>
  </v-menu>
</template>

<style lang="scss">
.v-tooltip {
  > .v-overlay__content.custom-tooltip {
    padding: 2px 6px;
  }
}
</style>
