<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const show1 = ref(false);
const Regform = ref();

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
});

// Password validation rules
const passwordRules = ref([
  (v: string) => !!v || 'Password is required',
  (v: string) => v.length >= 8 || 'Password must be at least 8 characters'
]);
const firstRules = ref([(v: string) => !!v || 'First Name is required']);
const lastRules = ref([(v: string) => !!v || 'Last Name is required']);
// Email validation rules
const emailRules = ref([
  (v: string) => !!v.trim() || 'E-mail is required',
  (v: string) => /.+@.+\..+/.test(v.trim()) || 'E-mail must be valid'
]);

function validate() {
  Regform.value.validate().then(({ valid }: { valid: boolean }) => {
    if (valid) {
      form.post(route('register'), {
        onFinish: () => form.reset('password'),
      });
    }
  });
}
</script>

<template>
  <div class="d-flex justify-space-between align-center">
    <h3 class="text-h3 text-center mb-0">Sign up</h3>
    <v-btn variant="text" color="primary" @click="$inertia.visit(route('login'))" class="text-decoration-none">Already have an account?</v-btn>
  </div>
  <v-form ref="Regform" lazy-validation class="mt-7 loginForm" @submit.prevent="validate">
    <v-row class="my-0">
      <v-col cols="12" sm="6" class="py-0">
        <div class="mb-6">
          <v-label>First Name*</v-label>
          <v-text-field
            v-model="form.first_name"
            :rules="firstRules"
            :error-messages="form.errors.first_name"
            hide-details="auto"
            required
            variant="outlined"
            class="mt-2"
            color="primary"
            placeholder="John"
          ></v-text-field>
        </div>
      </v-col>
      <v-col cols="12" sm="6" class="py-0">
        <div class="mb-6">
          <v-label>Last Name*</v-label>
          <v-text-field
            v-model="form.last_name"
            :rules="lastRules"
            :error-messages="form.errors.last_name"
            hide-details="auto"
            required
            variant="outlined"
            class="mt-2"
            color="primary"
            placeholder="Doe"
          ></v-text-field>
        </div>
      </v-col>
    </v-row>
    <div class="mb-6">
      <v-label>Email Address*</v-label>
      <v-text-field
        v-model="form.email"
        :rules="emailRules"
        :error-messages="form.errors.email"
        placeholder="demo@company.com"
        class="mt-2"
        required
        hide-details="auto"
        variant="outlined"
        color="primary"
      ></v-text-field>
    </div>
    <div class="mb-6">
      <v-label>Password</v-label>
      <v-text-field
        v-model="form.password"
        :rules="passwordRules"
        :error-messages="form.errors.password"
        placeholder="*****"
        required
        variant="outlined"
        color="primary"
        hide-details="auto"
        :type="show1 ? 'text' : 'password'"
        class="pwdInput mt-2"
      >
        <template v-slot:append-inner>
          <v-btn color="secondary" aria-label="icon" icon rounded variant="text">
            <SvgSprite name="custom-eye-invisible" style="width: 20px; height: 20px" v-if="show1 == false" @click="show1 = !show1" />
            <SvgSprite name="custom-eye" style="width: 20px; height: 20px" v-if="show1 == true" @click="show1 = !show1" />
          </v-btn>
        </template>
      </v-text-field>
    </div>

    <div class="d-sm-inline-flex align-center mt-2 mb-7 mb-sm-0 font-weight-bold">
      <h6 class="text-caption">
        By Signing up, you agree to our
        <a href="#" class="text-primary link-hover font-weight-medium">Terms of Service </a>
        and
        <a href="#" class="text-primary link-hover font-weight-medium">Privacy Policy</a>
      </h6>
    </div>
    <v-btn color="primary" block class="mt-4" variant="flat" rounded="md" size="large" type="submit" :loading="form.processing">Create Account</v-btn>
  </v-form>
</template>
