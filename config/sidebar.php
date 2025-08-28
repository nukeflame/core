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
                    'title' => 'Dashboard',
                    'route' => '/',
                    'icon' => 'bx bx-grid-alt',
                    'permission' => 'app.dashboard.view'
                ],
                [
                    'title' => 'Approvals',
                    'route' => 'approvals.index',
                    'icon' => 'bx bx-check-circle',
                    'permission' => 'app.dashboard.approvals'
                ],
                [
                    'title' => 'Mail',
                    'icon' => 'bx bx-envelope',
                    'permission' => 'app.dashboard.view',
                    'submenu' => [
                        [
                            'title' => 'Mail App',
                            'route' => 'mail.index',
                            'permission' => 'app.dashboard.view'
                        ],
                        // [
                        //     'title' => 'Mail Settings',
                        //     'route' => 'mail.index',
                        //     'permission' => 'app.dashboard.view'
                        // ],
                    ]
                ],
            ]
        ],
        [
            'category' => 'Business',
            'visibility_check' => ['app.business_development.view', 'app.cover_administration.view', 'app.claims_administration.view'],
            'items' => [
                [
                    'title' => 'Business Development',
                    'icon' => 'bx bx-building',
                    'permission' => 'app.business_development.view',
                    'submenu' => [
                        [
                            'title' => 'Pipeline',
                            'permission' => 'business_development.pipeline.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Facultative',
                                    'route' => 'leads.listing',
                                    'permission' => 'business_development.facultative.view'
                                ],
                                [
                                    'title' => 'Treaty',
                                    'route' => 'treaty.leads.listing',
                                    'permission' => 'business_development.treaty.view'
                                ],
                            ]
                        ],
                        [
                            'title' => 'Sales Management',
                            'permission' => 'business_development.pipeline.view',
                            'has_sub' => true,
                            'children' => [
                                [
                                    'title' => 'Facultative',
                                    'route' => 'pipeline.view',
                                    'permission' => 'business_development.facultative.view'
                                ],
                                [
                                    'title' => 'Treaty',
                                    'route' => 'treaty.pipeline.view',
                                    'permission' => 'business_development.treaty.view'
                                ],


                            ]
                        ],

                        [
                            'title' => 'BD Handovers',
                            'route' => 'pipeline.bd_handovers',
                            'permission' => 'business_development.bd_handovers.view'
                        ],
                        [
                            'title' => 'Tender Approval',
                            'route' => 'tender.list',
                            'permission' => 'business_development.bd_handovers.view'

                        ],
                    ]
                ],
                [
                    'title' => 'Cover Administration',
                    'icon' => 'ri-umbrella-line lh-20',
                    'permission' => 'app.cover_administration.view',
                    'submenu' => [
                        [
                            'title' => 'Cedants',
                            'route' => 'cedant.info',
                            'permission' => 'cover.cedants.view'
                        ],
                        [
                            'title' => 'Treaty Proportional Enquiry',
                            'route' => 'trtpropenquiry.info',
                            'permission' => 'cover.treaty.view'
                        ],
                        [
                            'title' => 'Treaty Non Proportional Enquiry',
                            'route' => 'trtnonpropenquiry.info',
                            'permission' => 'cover.treaty_non_proportional.view'
                        ],
                        [
                            'title' => 'Facultative Proportional Enquiry',
                            'route' => 'trtfacpropenquiry.info',
                            'permission' => 'cover.facultative.view'
                        ],
                        [
                            'title' => 'Facultative Non Proportional Enquiry',
                            'route' => 'trtfacnonpropenquiry.info',
                            'permission' => 'cover.facultative_non_proportional.view'
                        ],
                    ]
                ],
                [
                    'title' => 'Claims Administration',
                    'icon' => 'ri-file-list-line lh-20',
                    'permission' => 'app.claims_administration.view',
                    'submenu' => [
                        [
                            'title' => 'Claim Notifications Enquiry',
                            'route' => 'claim.notification.enquiry',
                            'permission' => 'claims.notification.view'
                        ],
                        [
                            'title' => 'Claims Enquiry',
                            'route' => 'claim.enquiry',
                            'permission' => 'claims.enquiry.view'
                        ],
                    ]
                ],
                [
                    'title' => 'Budget Allocation',
                    'route' => 'admin.budget_allocation',
                    'icon' => 'bx bx-calculator',
                    'permission' => 'reports.budget_allocation.view',
                ],
            ]
        ],
        // [
        //     'category' => 'Set Up',
        //     'visibility_check' => ['app.reports.view'],
        //     'items' => [
        //         [
        //             'title' => 'Budget Allocation',
        //             'route' => 'admin.budget_allocation',
        //             'icon' => 'bx bx-wallet',
        //             'permission' => 'reports.budget_allocation.view',
        //         ],
        //         [
        //             'title' => 'Staff Notice',
        //             'route' => 'admin.staff_notices',
        //             'icon' => 'bx bx-pin',
        //             'permission' => 'reports.budget_allocation.view',
        //         ],
        //     ]
        // ],
        [
            'category' => 'Settings',
            'visibility_check' => ['app.system_settings.view', 'app.reins_settings.view', 'app.user_management.view', 'app.integrations_api.view'],
            'items' => [
                [
                    'title' => 'Staff Notice',
                    'route' => 'admin.staff_notices',
                    'icon' => 'bx bx-bell',
                    'permission' => 'reports.budget_allocation.view',
                ],
                [
                    'title' => 'System Settings',
                    'icon' => 'bx bx-cog',
                    'permission' => 'app.system_settings.view',
                    'submenu' => [
                        [
                            'title' => 'Profile',
                            'route' => 'settings.profile',
                            'permission' => 'system_settings.profile.view'
                        ],
                        [
                            'title' => 'Departments',
                            'route' => 'settings.departments',
                            'permission' => 'system_settings.departments.view'
                        ],
                        [
                            'title' => 'Branches',
                            'route' => 'settings.branches',
                            'permission' => 'system_settings.branches.view'
                        ],
                        [
                            'title' => 'System Processes',
                            'route' => 'admin.system-processes',
                            'permission' => 'system_settings.system_processes.view'
                        ],
                        [
                            'title' => 'System Actions',
                            'route' => 'admin.system-actions',
                            'permission' => 'system_settings.system_actions.view'
                        ],
                        [
                            'title' => 'Company Profile',
                            'route' => 'settings.company_info',
                            'permission' => 'system_settings.company_profile.view'
                        ],
                        [
                            'title' => 'General Configuration',
                            'route' => 'settings.general_config',
                            'permission' => 'system_settings.general_config.view'
                        ],
                    ]
                ],
                [
                    'title' => 'Reinsurance Settings',
                    'icon' => 'bx bx-add-to-queue',
                    'permission' => 'app.reins_settings.view',
                    'submenu' => [
                        [
                            'title' => 'Customers',
                            'route' => 'customer.info',
                            'permission' => 'reins_settings.customers.view'
                        ],
                        [
                            'title' => 'Class Groups',
                            'route' => 'classGroup.info',
                            'permission' => 'reins_settings.class_groups.view'
                        ],
                        [
                            'title' => 'Classes',
                            'route' => 'class.info',
                            'permission' => 'reins_settings.classes.view'
                        ],
                        [
                            'title' => 'Policy Clauses',
                            'route' => 'clauseparam.info',
                            'permission' => 'reins_settings.policy_clauses.view'
                        ],
                        [
                            'title' => 'Sum Insured Types',
                            'route' => 'sumInsType.info',
                            'permission' => 'reins_settings.sum_insured_types.view'
                        ],
                        [
                            'title' => 'Reins Division',
                            'route' => 'reinsDivision.info',
                            'permission' => 'reins_settings.rein_division.view'
                        ],
                        [
                            'title' => 'Reins Classes',
                            'route' => 'reinsClass.info',
                            'permission' => 'reins_settings.rein_classes.view'
                        ],
                        [
                            'title' => 'Reins Class PremTypes',
                            'route' => 'reinsClassPremtypes.info',
                            'permission' => 'reins_settings.rein_class_prem_types.view'
                        ],
                        [
                            'title' => 'Treaty Types',
                            'route' => 'treatyType.info',
                            'permission' => 'reins_settings.treaty_types.view'
                        ],
                        [
                            'title' => 'Customer Types',
                            'route' => 'customerType.info',
                            'permission' => 'reins_settings.customer_types.view'
                        ],
                        [
                            'title' => 'Countries',
                            'route' => 'country.info',
                            'permission' => 'reins_settings.countries.view'
                        ],
                        [
                            'title' => 'Business Types',
                            'route' => 'businessType.info',
                            'permission' => 'reins_settings.business_types.view'
                        ],
                        [
                            'title' => 'Binder Classes',
                            'route' => 'binder.info',
                            'permission' => 'reins_settings.binder_classes.view'
                        ],
                        [
                            'title' => 'Pay Method',
                            'route' => 'payMethod.info',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Lead Status',
                            'route' => 'lead.status.info',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Bd Schedule Headers',
                            'route' => 'bd.schedule.info',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Bd Schedule Slip Template',
                            'route' => 'docs-setup.bd-schedule-slip-template',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Bd Stage Document',
                            'route' => 'stage.doc.info',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Bd Document Types',
                            'route' => 'doc.type.info',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Bd Operation Checklists',
                            'route' => 'operationchecklist.info',
                            'permission' => 'reins_settings.pay_methods.view'
                        ],
                        [
                            'title' => 'Tender Docs',
                            'route' => 'tender.docsparam',
                            'permission' => 'reins_settings.pay_methods.view'
                        ]



                    ]
                ],
                [
                    'title' => 'User management',
                    'icon' => 'bx bx-user',
                    'permission' => 'app.user_management.view',
                    'submenu' => [
                        [
                            'title' => 'Users',
                            'route' => 'admin.users',
                            'permission' => 'user_management.users.view'
                        ],
                        [
                            'title' => 'Roles',
                            'route' => 'admin.roles',
                            'permission' => 'user_management.roles.view'
                        ],
                        [
                            'title' => 'Permissions',
                            'route' => 'admin.permissions',
                            'permission' => 'user_management.permissions.view'
                        ],

                    ]
                ],
                [
                    'title' => 'Integration & APIs',
                    'route' => 'admin.integrations_api',
                    'icon' => 'bx bx-diamond',
                    'permission' => 'app.integrations_api.view'
                ],
                [
                    'title' => 'Reports',
                    'route' => 'reports.facultative',
                    'icon' => 'bx bxs-file',
                    'permission' => 'app.reports.view',
                    'classes' => 'reporting-dashboard'
                ],
            ]

        ],
        // [
        //     'category' => 'Reports',
        //     'visibility_check' => ['app.reports.view'],
        //     'items' => [
        //         [
        //             'title' => 'Reports',
        //             'route' => 'reports.facultative',
        //             'icon' => 'bx bxs-file',
        //             'permission' => 'app.reports.view',
        //             'classes' => 'reporting-dashboard'
        //         ],
        //     ]
        // ],
        // [
        //     'category' => 'Help',
        //     'visibility_check' => ['app.help.view'],
        //     'items' => [
        //         [
        //             'title' => 'Documentation & Support',
        //             'route' => 'approvals.index',
        //             'icon' => 'bx bx-party',
        //             'permission' => 'app.help.view'
        //         ],
        //     ]
        // ],
    ]
];
