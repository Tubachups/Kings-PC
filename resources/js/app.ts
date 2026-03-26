import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import '../css/app.css';
import { initializeTheme } from './composables/useAppearance';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia()
const progressColorVariable = '--inertia-progress-color';
const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue');

const resolveInertiaPage = (name: string) => {
    const requestedPath = `./pages/${name}.vue`;

    return resolvePageComponent(requestedPath, pages).catch(() => {
        const caseInsensitiveMatch = Object.keys(pages).find((path) => path.toLowerCase() === requestedPath.toLowerCase());

        if (caseInsensitiveMatch) {
            return resolvePageComponent(caseInsensitiveMatch, pages);
        }

        throw new Error(`Page not found: ${requestedPath}`);
    });
};

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
    resolve: resolveInertiaPage,
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
