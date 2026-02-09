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
    icon: 'custom-briefcase',
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
    title: 'Activities',
    icon: 'custom-calendar',
    to: '/crm/activities'
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
