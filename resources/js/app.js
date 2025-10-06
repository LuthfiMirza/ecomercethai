import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Chart from 'chart.js/auto';
import ApexCharts from 'apexcharts';
import './admin/theme';
import './frontend';
import megaMenu from './components/mega-menu';

window.Alpine = Alpine;
Alpine.plugin(focus);
window.Chart = Chart;
window.ApexCharts = ApexCharts;
window.megaMenu = megaMenu;

Alpine.start();
