import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  base: '/laravel-imgproxy/',
  title: "Laravel imgproxy",
  description: "A Laravel package for imgproxy integration",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: [
      { text: 'Guide', link: '/guide/getting-started' },
      { text: 'Reference', link: '/reference/api' },
      { text: 'Contribute', link: '/contribute/contributing' }
    ],

    sidebar: {
      "/guide/": [
        {
          text: 'Getting Started',
          items: [
            { text: 'Introduction', link: '/guide/getting-started' },
            { text: 'Installation', link: '/guide/installation' }
          ]
        },
        {
          text: 'Core Concepts',
          items: [
            { text: 'Basic Usage', link: '/guide/usage' },
            { text: 'Resizing', link: '/guide/resizing' },
            { text: 'Quality & Format', link: '/guide/quality' },
            { text: 'Visual Effects', link: '/guide/effects' }
          ]
        },
        {
          text: 'Advanced',
          items: [
            { text: 'Blade Components', link: '/guide/blade-components' },
            { text: 'Advanced Usage', link: '/guide/advanced-usage' }
          ]
        },
        {
          text: 'Best Practices',
          items: [
            { text: 'Security', link: '/guide/security' },
            { text: 'Troubleshooting', link: '/guide/troubleshooting' }
          ]
        }
      ],
      "/reference/": [
        {
          text: 'Configuration',
          items: [
            { text: 'Options', link: '/reference/api' },
            { text: 'Enums', link: '/reference/enums' }
          ]
        }
      ],
      "/contribute/": [
        {
          text: 'Contributing',
          items: [
            { text: 'Testing', link: '/contribute/testing' }
          ]
        }
      ]
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/imsus/laravel-imgproxy' }
    ],

    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2024-present'
    }
  }
})
