export interface menu {
  header?: string;
  title?: string;
  icon?: string;
  to?: string;
  divider?: boolean;
  getURL?: boolean;
  chip?: string;
  chipColor?: string;
  chipVariant?: string;
  chipIcon?: string;
  children?: menu[];
  disabled?: boolean;
  type?: string;
  subCaption?: string;
}

const sidebarItem: menu[] = [
  { header: 'CRM' },
  {
    title: 'CRM Dashboard',
    icon: 'custom-home-trend',
    to: '/dashboard'
  },
  {
    title: 'Deals',
    icon: 'custom-dollar-square',
    to: '/crm/deals-pipeline'
  },
  {
    title: 'Leads',
    icon: 'custom-notification',
    to: '/crm/leads'
  },
  {
      title: 'Clients',
      icon: 'custom-user-fill',
      to: '/crm/clients'
    },
    {
      title: 'Contacts',
      icon: 'custom-users',
      to: '/crm/contacts'
    },
  {
    title: 'Activities',
    icon: 'custom-calendar',
    to: '/crm/activities'
  },
  {
    title: 'Engagements',
    icon: 'custom-star-outline',
    to: '/crm/engagements'
  },
  {
    title: 'Projects',
    icon: 'custom-kanban',
    to: '/crm/projects'
  },
  {
    title: 'Tasks',
    icon: 'custom-clipboard',
    to: '/crm/tasks'
  },
  {
    title: 'Invoices',
    icon: 'custom-invoice',
    to: '/crm/invoices'
  },
  {
    title: 'Tickets',
    icon: 'custom-support',
    to: '/crm/tickets'
  },
  {
    title: 'Partners',
    icon: 'custom-user-square',
    to: '/crm/partners'
  },
  {
    title: 'Marketing Automation',
    icon: 'custom-message-2',
    to: '/crm/marketing-automations'
  },
  {
    title: 'Unified Inbox',
    icon: 'custom-status-up',
    to: '/crm/communications'
  },
  { header: 'Utilities' },
  {
    title: 'Typography',
    icon: 'custom-typography',
    to: '/utils/typography'
  },
  {
    title: 'Colors',
    icon: 'custom-colorpick',
    to: '/utils/colors'
  },
  {
    title: 'Shadows',
    icon: 'custom-shadow',
    to: '/utils/shadows'
  },
  { header: 'Pages' },
  {
    title: 'Login',
    icon: 'custom-shield',
    to: '/login1'
  },
  {
    title: 'Register',
    icon: 'custom-register',
    to: '/register1'
  },
  { header: 'Others' },
  {
    title: 'Sample Page',
    icon: 'custom-sample',
    to: '/starter'
  },
  {
    title: 'Documentation',
    icon: 'custom-support',
    to: 'https://phoenixcoded.gitbook.io/able-pro/v/vue/',
    type: 'external'
  }
];

export default sidebarItem;
