import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';
import { createPinia } from 'pinia';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()
const progressColorVariable = '--inertia-progress-color';

const applyProgressColor = (): void => {
    const isDark = document.documentElement.classList.contains('dark');
    const color = isDark ? '#FFFFFF' : '#000000';
    
    document.documentElement.style.setProperty(progressColorVariable, color);
};

const syncProgressColorWithTheme = (): void => {
    applyProgressColor();

    const observer = new MutationObserver(() => {
        applyProgressColor();
    });

    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class'],
    });
};

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .mount(el);
    },
    progress:
    {
        color: `var(${progressColorVariable})`,
    },
});

initializeTheme();
syncProgressColorWithTheme();
