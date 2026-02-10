<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  invoice: Object,
});

const deleteInvoice = () => {
  if (confirm('Are you sure you want to delete this invoice?')) {
    router.delete(route('crm.invoices.destroy', props.invoice.id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'draft': return 'grey';
    case 'sent': return 'info';
    case 'paid': return 'success';
    case 'overdue': return 'error';
    case 'cancelled': return 'warning';
    default: return 'grey';
  }
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

const printInvoice = () => {
  window.print();
};
</script>

<template>
  <Head :title="'Invoice ' + invoice.invoice_number" />
  <DashboardLayout>
    <v-container fluid>
      <v-row justify="center">
        <v-col cols="12" lg="9">
          <!-- Action Buttons (Hidden on Print) -->
          <v-card elevation="0" class="mb-6 d-print-none">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div class="d-flex align-center">
                <v-btn
                  icon="mdi-arrow-left"
                  variant="text"
                  class="mr-2"
                  @click="$window.history.back()"
                ></v-btn>
                <h1 class="text-h5 mb-0">Invoice Details</h1>
              </div>
              <div class="d-flex gap-2">
                <v-btn
                  color="secondary"
                  variant="outlined"
                  prepend-icon="mdi-printer"
                  @click="printInvoice"
                >
                  Print
                </v-btn>
                <v-btn
                  color="primary"
                  prepend-icon="mdi-pencil"
                  :to="route('crm.invoices.edit', invoice.id)"
                >
                  Edit
                </v-btn>
                <v-btn
                  color="error"
                  variant="outlined"
                  prepend-icon="mdi-delete"
                  @click="deleteInvoice"
                >
                  Delete
                </v-btn>
              </div>
            </v-card-text>
          </v-card>

          <!-- Invoice Paper -->
          <v-card elevation="2" class="pa-10 invoice-paper">
            <v-row class="mb-10">
              <v-col cols="6">
                <div class="d-flex align-center mb-4">
                  <v-icon color="primary" size="40" class="mr-2">mdi-cube-outline</v-icon>
                  <span class="text-h4 font-weight-bold">AblePro CRM</span>
                </div>
                <div class="text-body-2 text-medium-emphasis">
                  123 Business Avenue, Suite 100<br>
                  New York, NY 10001<br>
                  contact@ablepro.com
                </div>
              </v-col>
              <v-col cols="6" class="text-right">
                <h1 class="text-h3 font-weight-black primary--text mb-2">INVOICE</h1>
                <div class="text-h6 font-weight-bold mb-1"># {{ invoice.invoice_number }}</div>
                <v-chip
                  :color="getStatusColor(invoice.status)"
                  label
                  class="text-uppercase font-weight-bold"
                >
                  {{ invoice.status }}
                </v-chip>
              </v-col>
            </v-row>

            <v-row class="mb-10">
              <v-col cols="4">
                <div class="text-subtitle-2 text-medium-emphasis mb-2 text-uppercase font-weight-bold">Bill To:</div>
                <div class="text-h6 font-weight-bold mb-1">{{ invoice.client.name }}</div>
                <div class="text-body-2 text-medium-emphasis">
                  {{ invoice.client.email }}<br>
                  {{ invoice.client.phone || 'No phone' }}<br>
                  {{ invoice.client.address || '' }}
                </div>
              </v-col>
              <v-col cols="4">
                <div v-if="invoice.project">
                  <div class="text-subtitle-2 text-medium-emphasis mb-2 text-uppercase font-weight-bold">Project:</div>
                  <div class="text-body-1 font-weight-bold">{{ invoice.project.name }}</div>
                </div>
              </v-col>
              <v-col cols="4" class="text-right">
                <div class="mb-2">
                  <span class="text-subtitle-2 text-medium-emphasis text-uppercase font-weight-bold mr-2">Date Issued:</span>
                  <span class="text-body-1 font-weight-bold">{{ new Date(invoice.issue_date).toLocaleDateString() }}</span>
                </div>
                <div>
                  <span class="text-subtitle-2 text-medium-emphasis text-uppercase font-weight-bold mr-2">Due Date:</span>
                  <span class="text-body-1 font-weight-bold text-error">{{ new Date(invoice.due_date).toLocaleDateString() }}</span>
                </div>
              </v-col>
            </v-row>

            <v-table class="mb-10 border-b">
              <thead>
                <tr class="bg-grey-lighten-4">
                  <th class="text-left font-weight-bold">Description</th>
                  <th class="text-center font-weight-bold" style="width: 15%">Quantity</th>
                  <th class="text-right font-weight-bold" style="width: 20%">Unit Price</th>
                  <th class="text-right font-weight-bold" style="width: 20%">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in invoice.items" :key="item.id">
                  <td class="py-4">{{ item.description }}</td>
                  <td class="text-center">{{ parseFloat(item.quantity) }}</td>
                  <td class="text-right">{{ formatCurrency(item.unit_price) }}</td>
                  <td class="text-right font-weight-bold">{{ formatCurrency(item.total) }}</td>
                </tr>
              </tbody>
            </v-table>

            <v-row justify="end">
              <v-col cols="12" md="6">
                <div v-if="invoice.notes" class="mt-4">
                  <div class="text-subtitle-2 text-medium-emphasis mb-1 text-uppercase font-weight-bold">Notes:</div>
                  <div class="text-body-2">{{ invoice.notes }}</div>
                </div>
              </v-col>
              <v-col cols="12" md="4">
                <v-list density="compact" class="pa-0">
                  <v-list-item class="px-0">
                    <v-list-item-title class="text-body-1">Subtotal</v-list-item-title>
                    <template v-slot:append>
                      <span class="text-body-1 font-weight-bold">{{ formatCurrency(invoice.subtotal) }}</span>
                    </template>
                  </v-list-item>
                  <v-list-item class="px-0">
                    <v-list-item-title class="text-body-1">Tax (10%)</v-list-item-title>
                    <template v-slot:append>
                      <span class="text-body-1 font-weight-bold">{{ formatCurrency(invoice.tax_total) }}</span>
                    </template>
                  </v-list-item>
                  <v-divider class="my-2"></v-divider>
                  <v-list-item class="px-0">
                    <v-list-item-title class="text-h5 font-weight-black primary--text">Total</v-list-item-title>
                    <template v-slot:append>
                      <span class="text-h5 font-weight-black primary--text">{{ formatCurrency(invoice.total_amount) }}</span>
                    </template>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>

            <div class="mt-16 text-center text-caption text-medium-emphasis">
              <p>Thank you for your business!</p>
              <p>Please make payment within the due date to avoid overdue charges.</p>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.invoice-paper {
  background-color: white;
  min-height: 29.7cm; /* A4 height approx */
}
.gap-2 {
  gap: 8px;
}
@media print {
  .v-container {
    padding: 0 !important;
  }
  .invoice-paper {
    box-shadow: none !important;
    padding: 0 !important;
  }
  .d-print-none {
    display: none !important;
  }
}
</style>
