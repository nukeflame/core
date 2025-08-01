<?php

/**
 * Sidebar Navigation Configuration
 *
 * This file defines the structure of the application's sidebar navigation menu.
 * Each menu item can have the following properties:
 * - category: Main grouping of menu items
 * - items: Array of menu items within a category
 * - title: Display name of the menu item
 * - route: Laravel route name for the menu item
 * - icon: Boxicons icon class
 * - permission: Required user permission to view this item
 * - submenu: Nested menu items
 * - classes: Custom css class
 */

return [
    'menu_items' => [
        [
            'category' => 'Main',
            'items' => [
                [
                    'title' => 'Home',
                    'route' => '/',
                    'icon' => 'bx bx-home',
                    'permission' => 'app.dashboard.view',
                    'classes' => 'reset-sidebar'
                ],
                [
                    'title' => 'Analytics',
                    'route' => 'dashboard.analytics',
                    'icon' => 'bx bx-bar-chart-alt',
                    'permission' => 'app.dashboard.analytics'
                ],
                [
                    'title' => 'Business Intelligence',
                    'icon' => 'bx bx-droplet',
                    'permission' => 'app.business_development.view',
                    'submenu' => [
                        [
                            'title' => 'Budget Tracker',
                            'route' => 'bi.budget_tracker',
                            'permission' => 'business_development.budget_tracker.view'
                        ],
                        [
                            'title' => 'Business Development',
                            'route' => 'bi.business_development',
                            'permission' => 'business_development.dashboard.view'
                        ],
                        [
                            'title' => 'Cover Administration',
                            'route' => 'bi.cover',
                            'permission' => 'business_development.cover.view'
                        ],
                        [
                            'title' => 'Claims Administration',
                            'route' => 'bi.claims',
                            'permission' => 'business_development.claims.view'
                        ],
                        // [
                        //     'title' => 'Debtors',
                        //     'route' => 'bi.debtors',
                        //     'permission' => 'business_development.debtors.view'
                        // ],
                    ]
                ],
                [
                    'title' => 'Reports',
                    'icon' => 'bx bx-file',
                    'permission' => 'app.reports.view',
                    'submenu' => [
                        // [
                        //     'title' => 'Pipeline Reports',
                        //     'route' => 'pipeline.report',
                        //     'permission' => 'reports.pipeline.view'
                        // ],
                        // [
                        //     'title' => 'Reinsurers Declined',
                        //     'route' => 'decline.report',
                        //     'permission' => 'reports.pipeline.view'
                        // ],
                        // [
                        //     'title' => 'Sales Reports',
                        //     'route' => 'sales.report',
                        //     'permission' => 'reports.sales.view'
                        // ],
                        [
                            'title' => 'Coverage Reports',
                            'permission' => 'reports.facultative.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Cover Reports',
                                    'route' => 'cover-reports.index',
                                    'permission' => 'reports.facultative.view'
                                ],

                            ]
                        ],

                        [
                            'title' => 'Production Reports',
                            'permission' => 'reports.facultative.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Production Summary',
                                    'route' => 'production-reports.index',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Production Detailed',
                                    'route' => 'production-reports.detailed',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Production by Facultative Type',
                                    'route' => 'production-reports.fac_type',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Production by Treaty Type',
                                    'route' => 'production-reports.treaty_type',
                                    'permission' => 'reports.facultative.view'
                                ],
                            ]
                        ],
                        [
                            'title' => 'Facultative Reports',
                            'permission' => 'reports.facultative.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Pipeline Reports',
                                    'route' => 'pipeline.report',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Sales Reports',
                                    'route' => 'sales.report',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Reinsurers Declined',
                                    'route' => 'decline.report',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Facultative Summary',
                                    'route' => 'facultative-reports.summary',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Facultative Placement',
                                    'route' => 'facultative-reports.placement',
                                    'permission' => 'reports.facultative.view'
                                ],
                                                               
                            ]
                        ],
                        [
                            'title' => 'Treaty Reports',
                            'permission' => 'reports.facultative.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Treaty Proportional',
                                    'route' => 'treaty-reports.proportional',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Treaty Non-Proportional',
                                    'route' => 'treaty-reports.non_proportional',
                                    'permission' => 'reports.facultative.view'
                                ],
                            ]
                        ],
                        [
                            'title' => 'Other Reports',
                            'permission' => 'reports.facultative.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Exception Reports',
                                    'route' => 'other-reports.exception_reports',
                                    'permission' => 'reports.facultative.view'
                                ],
                                [
                                    'title' => 'Other Reports',
                                    'route' => 'other-reports.other_reports',
                                    'permission' => 'reports.facultative.view'
                                ],
                            ]
                        ]
                    ]
                ],
            ]
        ],
    ]
];
