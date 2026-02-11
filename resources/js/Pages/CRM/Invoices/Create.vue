<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, computed } from 'vue';

const props = defineProps({
  clients: Array,
  projects: Array,
  nextInvoiceNumber: String,
});

const form = useForm({
  invoice_number: props.nextInvoiceNumber,
  client_id: null,
  project_id: null,
  issue_date: new Date().toISOString().split('T')[0],
  due_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
  status: 'draft',
  notes: '',
  items: [
    { description: '', quantity: 1, unit_price: 0 }
  ],
});

const statuses = [
  { title: 'Draft', value: 'draft' },
  { title: 'Sent', value: 'sent' },
  { title: 'Paid', value: 'paid' },
  { title: 'Overdue', value: 'overdue' },
  { title: 'Cancelled', value: 'cancelled' },
];

const addItem = () => {
  form.items.push({ description: '', quantity: 1, unit_price: 0 });
};

const removeItem = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
};

const subtotal = computed(() => {
  return form.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
});

const tax = computed(() => {
  return subtotal.value * 0.1;
});

const total = computed(() => {
  return subtotal.value + tax.value;
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const submit = () => {
  form.post(route('crm.invoices.store'));
};
</script>

<template>
  <Head title="Create Invoice" />
  <DashboardLayout>
    <v-container fluid>
      <v-row justify="center">
        <v-col cols="12" lg="10">
          <v-card elevation="0">
            <v-card-text class="d-flex align-center py-4">
              <v-btn
                icon="mdi-arrow-left"
                variant="text"
                class="mr-2"
                @click="$window.history.back()"
              ></v-btn>
              <div>
                <h1 class="text-h5 mb-0">Create New Invoice</h1>
              </div>
            </v-card-text>
            <v-divider></v-divider>

            <v-form @submit.prevent="submit">
              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="form.invoice_number"
                      label="Invoice Number"
                      variant="outlined"
                      :error-messages="form.errors.invoice_number"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.client_id"
                      :items="clients"
                      item-title="name"
                      item-value="id"
                      label="Client"
                      variant="outlined"
                      :error-messages="form.errors.client_id"
                      required
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.project_id"
                      :items="projects"
                      item-title="name"
                      item-value="id"
                      label="Project (Optional)"
                      variant="outlined"
                      clearable
                      :error-messages="form.errors.project_id"
                    ></v-select>
                  </v-col>

                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="form.issue_date"
                      label="Issue Date"
                      type="date"
                      variant="outlined"
                      :error-messages="form.errors.issue_date"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="form.due_date"
                      label="Due Date"
                      type="date"
                      variant="outlined"
                      :error-messages="form.errors.due_date"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="4">
                    <v-select
                      v-model="form.status"
                      :items="statuses"
                      label="Status"
                      variant="outlined"
                      :error-messages="form.errors.status"
                      required
                    ></v-select>
                  </v-col>
                </v-row>

                <div class="mt-8 mb-4 d-flex align-center justify-space-between">
                  <h3 class="text-h6">Invoice Items</h3>
                  <v-btn
                    color="secondary"
                    variant="outlined"
                    prepend-icon="mdi-plus"
                    size="small"
                    @click="addItem"
                  >
                    Add Item
                  </v-btn>
                </div>

                <v-table class="mb-6 border rounded">
                  <thead>
                    <tr>
                      <th class="text-left" style="width: 50%">Description</th>
                      <th class="text-left" style="width: 15%">Quantity</th>
                      <th class="text-left" style="width: 15%">Unit Price</th>
                      <th class="text-left" style="width: 15%">Total</th>
                      <th class="text-right" style="width: 5%"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in form.items" :key="index">
                      <td class="py-2">
                        <v-text-field
                          v-model="item.description"
                          variant="outlined"
                          density="compact"
                          hide-details
                          required
                        ></v-text-field>
                      </td>
                      <td class="py-2">
                        <v-text-field
                          v-model.number="item.quantity"
                          type="number"
                          variant="outlined"
                          density="compact"
                          hide-details
                          required
                        ></v-text-field>
                      </td>
                      <td class="py-2">
                        <v-text-field
                          v-model.number="item.unit_price"
                          type="number"
                          variant="outlined"
                          density="compact"
                          hide-details
                          required
                        ></v-text-field>
                      </td>
                      <td class="py-2 font-weight-bold">
                        {{ formatCurrency(item.quantity * item.unit_price) }}
                      </td>
                      <td class="py-2 text-right">
                        <v-btn
                          icon="mdi-delete"
                          variant="text"
                          color="error"
                          size="small"
                          @click="removeItem(index)"
                          :disabled="form.items.length <= 1"
                        ></v-btn>
                      </td>
                    </tr>
                  </tbody>
                </v-table>

                <v-row>
                  <v-col cols="12" md="7">
                    <v-textarea
                      v-model="form.notes"
                      label="Notes"
                      variant="outlined"
                      rows="4"
                      placeholder="Thank you for your business!"
                    ></v-textarea>
                  </v-col>
                  <v-col cols="12" md="5">
                    <v-card variant="outlined" class="pa-4 bg-grey-lighten-4">
                      <div class="d-flex justify-space-between mb-2">
                        <span>Subtotal:</span>
                        <span class="font-weight-bold">{{ formatCurrency(subtotal) }}</span>
                      </div>
                      <div class="d-flex justify-space-between mb-2">
                        <span>Tax (10%):</span>
                        <span class="font-weight-bold">{{ formatCurrency(tax) }}</span>
                      </div>
                      <v-divider class="my-2"></v-divider>
                      <div class="d-flex justify-space-between text-h6 primary--text">
                        <span>Total:</span>
                        <span class="font-weight-bold">{{ formatCurrency(total) }}</span>
                      </div>
                    </v-card>
                  </v-col>
                </v-row>
              </v-card-text>

              <v-divider></v-divider>
              <v-card-actions class="pa-6">
                <v-spacer></v-spacer>
                <v-btn
                  variant="text"
                  @click="$window.history.back()"
                  class="mr-2"
                >
                  Cancel
                </v-btn>
                <v-btn
                  color="primary"
                  type="submit"
                  size="large"
                  :loading="form.processing"
                >
                  Create Invoice
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>
