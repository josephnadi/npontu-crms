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
  /** Accordion section: unique key for open state */
  sectionKey?: string;
  /** Section title (e.g. "HIGH LEVEL") */
  sectionHeader?: string;
  /** Section subtitle (e.g. "Overview") */
  sectionSubCaption?: string;
}

const sidebarItem: menu[] = [
  
  {
    title: 'CRM Dashboard',
    icon: 'custom-home-trend',
    to: '/dashboard'
  },
  // SECTION 2: SALES & MONEY (The Funnel)
  {
    sectionKey: 'sales-money',
    sectionHeader: 'SALES & MONEY',
    
    children: [
      { title: 'Leads', icon: 'custom-notification', to: '/crm/leads' },
      { title: 'Deals', icon: 'custom-dollar-square', to: '/crm/deals-pipeline' },
      { title: 'Invoices', icon: 'custom-invoice', to: '/crm/invoices' }
    ]
  },
  // SECTION 3: PEOPLE (The Database)
  {
    sectionKey: 'people',
    sectionHeader: 'PEOPLE',
    children: [
      { title: 'Clients', icon: 'custom-user-fill', to: '/crm/clients' },
      { title: 'Contacts', icon: 'custom-users', to: '/crm/contacts' },
      { title: 'Partners', icon: 'custom-user-square', to: '/crm/partners' }
    ]
  },
  // SECTION 4: WORK & EXECUTION (Getting things done)
  {
    sectionKey: 'work-execution',
    sectionHeader: 'WORK & EXECUTION',
    children: [
      { title: 'Projects', icon: 'custom-kanban', to: '/crm/projects' },
      { title: 'Tasks', icon: 'custom-clipboard', to: '/crm/tasks' },
      { title: 'Activities', icon: 'custom-calendar', to: '/crm/activities' },
      { title: 'Engagements', icon: 'custom-star-outline', to: '/crm/engagements' }
    ]
  },
  // SECTION 5: COMMUNICATION & SUPPORT
  {
    sectionKey: 'communication-support',
    sectionHeader: 'COMMUNICATION & SUPPORT',
    sectionSubCaption: '',
    children: [
      { title: 'Unified Inbox', icon: 'custom-status-up', to: '/crm/communications' },
      { title: 'Tickets', icon: 'custom-support', to: '/crm/tickets' },
      { title: 'Marketing Automation', icon: 'custom-message-2', to: '/crm/marketing-automations' }
    ]
  },
  // { header: 'Utilities' },
  // {
  //   title: 'Typography',
  //   icon: 'custom-typography',
  //   to: '/utils/typography'
  // },
  // {
  //   title: 'Colors',
  //   icon: 'custom-colorpick',
  //   to: '/utils/colors'
  // },
  // {
  //   title: 'Shadows',
  //   icon: 'custom-shadow',
  //   to: '/utils/shadows'
  // },
  // { header: 'Pages' },
  // {
  //   title: 'Login',
  //   icon: 'custom-shield',
  //   to: '/login1'
  // },
  // {
  //   title: 'Register',
  //   icon: 'custom-register',
  //   to: '/register1'
  // },
  // { header: 'Others' },
  // {
  //   title: 'Sample Page',
  //   icon: 'custom-sample',
  //   to: '/starter'
  // },
  // {
  //   title: 'Documentation',
  //   icon: 'custom-support',
  //   to: 'https://phoenixcoded.gitbook.io/able-pro/v/vue/',
  //   type: 'external'
  // }
];

export default sidebarItem;
