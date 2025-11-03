import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import Chart from 'chart.js/auto';
import ApexCharts from 'apexcharts';
import './admin/theme';
import './frontend';
import megaMenu from './components/mega-menu';
import './components/livechat';
window.Alpine = Alpine;
Alpine.plugin(focus);
window.Chart = Chart;
window.ApexCharts = ApexCharts;
window.megaMenu = megaMenu;

Alpine.start();

document.querySelectorAll('[data-checkout-react]').forEach(async (node) => {
  const configRaw = node.getAttribute('data-checkout-config');
  let initialData = {};
  if (configRaw) {
    try {
      initialData = JSON.parse(configRaw);
    } catch (error) {
      console.error('Failed to parse checkout config', error);
    }
  }

  try {
    const [{ default: React }, { createRoot }, { default: CheckoutApp }] = await Promise.all([
      import('react'),
      import('react-dom/client'),
      import('./react/CheckoutApp.jsx'),
    ]);

    const root = createRoot(node);
    root.render(React.createElement(CheckoutApp, { initialData }));
  } catch (error) {
    console.error('Unable to mount checkout React app', error);
  }
});
