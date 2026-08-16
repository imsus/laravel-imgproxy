import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
    base: '/laravel-imgproxy/',
    title: 'Laravel imgproxy',
    description: 'A Laravel package for imgproxy integration',
    cleanUrls: true,
    srcExclude: ['agents/**'],
    themeConfig: {
        // https://vitepress.dev/reference/default-theme-config
        nav: [
            { text: 'Guide', link: '/guide/getting-started' },
            { text: 'Reference', link: '/reference/api' },
            { text: 'Contribute', link: '/contribute/contributing' },
            {
                text: 'v2.1.0',
                items: [
                    { text: 'v2.1.0 (current)', link: '/' },
                    { text: 'v1.x (frozen)', link: '/1.x/' },
                ],
            },
        ],

        sidebar: {
            // v2.0.0 — current docs at the root
            '/guide/': [
                {
                    text: 'Getting Started',
                    items: [
                        { text: 'Introduction', link: '/guide/getting-started' },
                        { text: 'Installation', link: '/guide/installation' },
                        { text: 'Upgrading from 1.x', link: '/guide/upgrading' },
                    ],
                },
                {
                    text: 'Core Concepts',
                    items: [
                        { text: 'Basic Usage', link: '/guide/usage' },
                        { text: 'Resizing', link: '/guide/resizing' },
                        { text: 'Quality & Format', link: '/guide/quality' },
                        { text: 'Visual Effects', link: '/guide/effects' },
                    ],
                },
                {
                    text: 'Advanced',
                    items: [
                        { text: 'Blade Components', link: '/guide/blade-components' },
                        { text: 'Storage Integration', link: '/guide/storage-integration' },
                        { text: 'Advanced Usage', link: '/guide/advanced-usage' },
                    ],
                },
                {
                    text: 'Best Practices',
                    items: [
                        { text: 'Security', link: '/guide/security' },
                        { text: 'Troubleshooting', link: '/guide/troubleshooting' },
                    ],
                },
            ],
            '/reference/': [
                {
                    text: 'Reference',
                    items: [
                        { text: 'API', link: '/reference/api' },
                        { text: 'Enums', link: '/reference/enums' },
                    ],
                },
            ],
            '/contribute/': [
                {
                    text: 'Contributing',
                    items: [
                        { text: 'Contributing', link: '/contribute/contributing' },
                        { text: 'Testing', link: '/contribute/testing' },
                    ],
                },
            ],

            // v1.x — frozen snapshot under /1.x/
            '/1.x/guide/': [
                {
                    text: 'Getting Started',
                    items: [
                        { text: 'Introduction', link: '/1.x/guide/getting-started' },
                        { text: 'Installation', link: '/1.x/guide/installation' },
                    ],
                },
                {
                    text: 'Core Concepts',
                    items: [
                        { text: 'Basic Usage', link: '/1.x/guide/usage' },
                        { text: 'Resizing', link: '/1.x/guide/resizing' },
                        { text: 'Quality & Format', link: '/1.x/guide/quality' },
                        { text: 'Visual Effects', link: '/1.x/guide/effects' },
                    ],
                },
                {
                    text: 'Advanced',
                    items: [
                        { text: 'Blade Components', link: '/1.x/guide/blade-components' },
                        { text: 'Advanced Usage', link: '/1.x/guide/advanced-usage' },
                    ],
                },
                {
                    text: 'Best Practices',
                    items: [
                        { text: 'Security', link: '/1.x/guide/security' },
                        { text: 'Troubleshooting', link: '/1.x/guide/troubleshooting' },
                    ],
                },
            ],
            '/1.x/reference/': [
                {
                    text: 'Reference',
                    items: [
                        { text: 'API', link: '/1.x/reference/api' },
                        { text: 'Enums', link: '/1.x/reference/enums' },
                    ],
                },
            ],
            '/1.x/contribute/': [
                {
                    text: 'Contributing',
                    items: [
                        { text: 'Testing', link: '/1.x/contribute/testing' },
                    ],
                },
            ],
        },

        socialLinks: [
            { icon: 'github', link: 'https://github.com/imsus/laravel-imgproxy' },
        ],

        footer: {
            message: 'Released under the MIT License.',
            copyright: 'Copyright © 2024-present',
        },
    },
})
