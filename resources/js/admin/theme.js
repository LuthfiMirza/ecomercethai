import Alpine from 'alpinejs';

const palettes = {
  blue: { 600: [60,80,224], 700: [43,63,199] },
  emerald: { 600: [5,150,105], 700: [4,120,87] },
  violet: { 600: [124,58,237], 700: [109,40,217] },
  rose: { 600: [225,29,72], 700: [190,18,60] },
};

function applyTheme({ mode, color, radius, density }) {
  const root = document.documentElement;
  // dark mode
  if (mode === 'dark') root.classList.add('dark'); else root.classList.remove('dark');
  // primary
  const p = palettes[color] ?? palettes.blue;
  root.style.setProperty('--color-primary-600', p[600].join(' '));
  root.style.setProperty('--color-primary-700', p[700].join(' '));
  // radius
  const radiusMap = { md: '0.375rem', lg: '0.5rem', '2xl': '1rem' };
  root.style.setProperty('--radius', radiusMap[radius] ?? '1rem');
  // density
  document.body.classList.toggle('density-compact', density === 'compact');
}

Alpine.store('theme', {
  mode: localStorage.getItem('ui.mode') || 'light',
  color: localStorage.getItem('ui.color') || 'blue',
  radius: localStorage.getItem('ui.radius') || '2xl',
  density: localStorage.getItem('ui.density') || 'comfortable',
  init() { applyTheme(this); },
  setMode(v){ this.mode = v; localStorage.setItem('ui.mode', v); applyTheme(this); },
  toggleMode(){ this.setMode(this.mode === 'dark' ? 'light' : 'dark'); },
  setColor(v){ this.color = v; localStorage.setItem('ui.color', v); applyTheme(this); },
  setRadius(v){ this.radius = v; localStorage.setItem('ui.radius', v); applyTheme(this); },
  setDensity(v){ this.density = v; localStorage.setItem('ui.density', v); applyTheme(this); },
});
