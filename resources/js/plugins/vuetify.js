import '@mdi/font/css/materialdesignicons.css';
import 'vuetify/styles';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { VDataTable } from 'vuetify/components';

const LightTheme = {
    dark: false,
    colors: {
        primary: '#5D87FF',
        secondary: '#49BEFF',
        info: '#53CDCD',
        success: '#5AC18E',
        accent: '#FFAB91',
        warning: '#FFC107',
        error: '#FA896B',
        lightprimary: '#ECF2FF',
        lightsecondary: '#E8F7FF',
        lightsuccess: '#E6FFFA',
        lighterror: '#FDEDE8',
        lightwarning: '#FEF5E5',
        lightinfo: '#EBF3FE',
        darkprimary: '#4070F4',
        darksecondary: '#39B6FF',
        darkinfo: '#4DD0E1',
        darksuccess: '#4CAF50',
        darkaccent: '#FF8A65',
        darkwarning: '#FFB300',
        darkerror: '#EF5350',
        textPrimary: '#2A3547',
        textSecondary: '#677180',
        borderColor: '#EAEFF4',
        inputBorder: '#DFE5EF',
        containerBg: '#FFFFFF',
        background: '#F8F9FA',
        hoverColor: '#F6F9FC',
        surface: '#FFFFFF',
        'on-surface': '#2A3547',
        grey100: '#F2F6FA',
        grey200: '#EAEFF4',
        grey300: '#DFE5EF',
        grey400: '#D0D7DD',
        grey500: '#BFC6CE',
        grey600: '#AAB3BA',
        grey700: '#919EAB',
        grey800: '#677180',
        grey900: '#404B5A',
        darkText: '#2A3547',
        lightText: '#677180',
    },
};

export default createVuetify({
    components: {
        ...components,
        VDataTable,
    },
    directives,
    theme: {
        defaultTheme: 'LightTheme',
        themes: {
            LightTheme,
        },
    },
    icons: {
        defaultSet: 'mdi',
    },
});
