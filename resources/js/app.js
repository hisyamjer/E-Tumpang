import { createApp, h } from 'vue'              // create Vue app + render helper
import { createInertiaApp } from '@inertiajs/vue3' // Inertia-Vue bridge

// scans all Vue files inside Pages/
const pages = import.meta.glob('./Pages/**/*.vue')

createInertiaApp({
  // Laravel returns Inertia::render('Todos/Index'),
  // Inertia will look for: resources/js/Pages/Todos/Index.vue
  resolve: (name) => {
    const page = pages[`./Pages/${name}.vue`]
    if (!page) throw new Error(`Page not found: ${name}`)
    return page()
  },

  // Mount Vue into the DOM element created by @inertia in app.blade.php
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) }) // render root Inertia component
      .use(plugin)                             // install Inertia plugin
      .mount(el)                               // mount into HTML
  },
})