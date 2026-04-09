<?php

return [

    // HR Center
    [
        'group_title' => '',
        [
            'title'      => 'HR Management',
            'icon'       => 'fa-solid fa-people-group',
            'icon_color' => 'text-primary',
            'permission' => 'hr',
            'order'      => 10,
            'children'   => [
                [
                    'title'      => 'Basic Info.',
                    'icon'       => 'fa-solid fa-gear',
                    'icon_color' => 'text-info',
                    'permission' => 'hr',
                    'children'   => [
                        [
                            'title'      => 'Classification',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/classifications',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Block/Line',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr/floor-lines',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],

                        [
                            'title'      => 'Bonus Policy',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/bonus-policies',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Bonus Title',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/bonus-titles',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Country',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/countries',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Division',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/divisions',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],

                        [
                            'title'      => 'District',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/districts',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Po. Station',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/police-stations',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Department',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr/departments',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Designation',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/designations',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Factory',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/factories',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Leave Info.',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/leave-infos',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Marital Status',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/marital-statuses',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],

                        [
                            'title'      => 'Production Bonus(%)',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/production-bonuses',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Religion',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/religions',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Sex',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/sexes',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Salary Keys',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/salary-keys',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Salary Payment Mode',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/payment-methods',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Shift',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/shifts',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Section',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/sections',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Sub Section',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/sub-sections',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Weeks',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/weeks',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Working Place',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/masters/working-places',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                    ]
                ],
                [
                    'title'      => 'Employee',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'route'      => '/admin/hr-center/employees',
                    'icon_color' => 'text-warning',
                    'permission' => 'hr'
                ],
                [
                    'title'      => 'Reports',
                    'icon'       => 'fa-solid fa-arrow-right',
                    'icon_color' => 'text-warning',
                    'permission' => 'hr',
                    'children'   => [
                        [
                            'title'      => 'Employee',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/employee',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Monthly',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/monthly',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Machine Id',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/machine-id',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Job Card',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/job-card',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Personal File',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/personal-file',
                            'icon_color' => 'text-warning',
                            'permission' => 'hr',
                        ],
                        [
                            'title'      => 'Attendance',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/attendance',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Tiffin / Night / Dinner',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/tiffin-night-dinner',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Production Job Card',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/pro-job-card',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Bonus Salary Fixed',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/bonus-salary-fixed',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Bonus Salary Production',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/bonus-salary-production',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Salary Fixed',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/salary-fixed',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Salary Production',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/salary-production',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                        [
                            'title'      => 'Wages And Salary Summary',
                            'icon'       => 'fa-solid fa-arrow-right',
                            'route'      => '/admin/hr-center/reports/salary-summary',
                            'icon_color' => 'text-warning',
                            'permission' => 'dev',
                        ],
                    ]
                ],
            ]
        ],
    ],

];

