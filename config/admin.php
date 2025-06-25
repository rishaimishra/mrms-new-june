<?php

return [

    'side_nav' => [
        [
            'label' => 'Dashboard',
            'icon' => 'dashboard',
            'route' => 'admin.dashboard'
        ],
        [
            'label' => 'Users',
            'icon' => 'people',
            'route' => 'admin.user.index'
        ],
        // [
        //     'label' => 'EDSA Agents',
        //     'icon' => 'people',
        //     'route' => 'admin.edsauser.index'
        // ],
        [
            'label' => 'Sellers',
            'icon' => 'people',
            'route' => 'admin.seller.index'
        ],
        // [
        //     'label' => 'Saved Meters',
        //     'icon' => 'bolt',
        //     'route' => 'admin.savedmeter.index'

        // ],
        // [
        //     'label' => 'Saved DSTV Recharge Cards',
        //     'icon' => 'bolt',
        //     'route' => 'admin.saveddstvrechargecard.index'

        // ],
        // [
        //     'label' => 'Saved Star Times Recharge Cards',
        //     'icon' => 'bolt',
        //     'route' => 'admin.savedstartimerechargecard.index'

        // ],
        // [
        //     'label' => 'EDSA Transactions',
        //     'icon' => 'money',
        //     'route' => 'admin.edsatransaction.index'

        // ],
        // [
        //     'label' => 'Transactions',
        //     'icon' => 'money',
        //     'role' => 'admin',
        //     'children' => [
        //         [
        //             'label' => 'EDSA',
        //             'route' => 'admin.edsatransaction.index'
        //         ],
        //         [
        //             'label' => 'DSTV',
        //             'route' => 'admin.dstvtransaction.index'
        //         ],
        //         [
        //             'label' => 'STAR TIMES',
        //             'route' => 'admin.startransaction.index'
        //         ]
        //     ]
        // ],
        [
            'label' => 'Advertisement Management',
            'icon' => 'extension',
            'route' => 'admin.ad-detail.index',

        ],
        [
            'label' => 'Attributes',
            'icon' => 'people',
            'role' => 'admin',
            'children' => [
                [
                    'label' => 'Attribute Sets',
                    'route' => 'admin.attribute-set.index'
                ],
                [
                    'label' => 'Attribute Group',
                    'route' => 'admin.attribute-group.index'
                ],
                [
                    'label' => 'Attributes',
                    'route' => 'admin.attribute.index'
                ]
            ]
        ],
        [
            'label' => 'Address',
            'icon' => 'space_bar',
            'route' => 'admin.address-area.index',

        ],
        [
            'label' => 'System Users',
            'icon' => 'accessible',
            'role' => 'admin',
            'children' => [
                [
                    'label' => 'List',
                    'route' => 'admin.system-user.list',
                ],
                [
                    'label' => 'Create',
                    'route' => 'admin.system-user.create',
                ],

            ]
        ],
        [
            'label' => 'Tourism & Travel',
            'icon' => 'place',
            'children' => [
                [
                    'label' => 'Places',
                    'icon' => 'place',
                    'route' => 'admin.place.index'
                ],
                [
                    'label' => 'Places Category',
                    'icon' => 'spa',
                    'route' => 'admin.place-category.index'
                ]]

        ],
        [
            'label' => 'Autos',
            'icon' => 'place',
            'children' => [
                [
                    'label' => 'Autos',
                    'icon' => 'spa',
                    'route' => 'admin.auto.index'
                ],
                [
                    'label' => 'Subscription and Referrel fee',
                    'icon' => 'spa',
                    'route' => 'admin.autosubscription'
                ]
                ,[
                    'label' => 'Autos Category',
                    'icon' => 'spa',
                    'route' => 'admin.auto-category.index',
        
                ],
            ]

        ],
        
        [
            'label' => 'Real Estate',
            'icon' => 'place',
            'children' => [
                [
                    'label' => 'Real Estate',
                    'icon' => 'spa',
                    'route' => 'admin.real-estate.index'
                ],
                [
                    'label' => 'Subscription and Referrel fee',
                    'icon' => 'spa',
                    'route' => 'admin.realsubscription'
                ],
                [
                    'label' => 'Real Estate Category',
                    'icon' => 'spa',
                    'route' => 'admin.real-estate-category.index',
        
                ],
            ]

        ],
       
        [
            'label' => 'Fun & Games',
            'icon' => 'extension',
            'children' => [
                [
                    'label' => 'Fun & Games',
                    'icon' => 'extension',
                    'route' => 'admin.question.index'
                ],
                [
                    'label' => 'Fun & Games Category',
                    'icon' => 'extension',
                    'route' => 'admin.knowledgebase-category.index'
                ]]

        ],
        [
            'label' => 'Shop',
            'icon' => 'shopping_basket',
            'children' => [
                [
                    'label' => 'Product',
                    'icon' => 'shopping_basket',
                    'route' => 'admin.product.index'
                ],
                [
                    'label' => 'Product Category',
                    'icon' => 'shopping_basket',
                    'route' => 'admin.product-category.index'
                ]]

        ],
        [
            'label' => 'Services',
            'icon' => 'settings',
            'route' => 'admin.services.list'
        ],
        [
            'label' => 'Chat a ride',
            'icon' => 'settings',
            'route' => 'admin.chat_a_ride.list'
        ],
        [
            'label' => 'Mobi Doc',
            'icon' => 'settings',
            'route' => 'admin.movie_doc'
        ],
        

        [
            'label' => 'Transport & Delivery',
            'icon' => 'settings',
            'children' => [
                [
                    'label' => ' Vehicle Hire',
                    'route' => 'admin.transport.vehicles'
                ],
                [
                    'label' => 'Vehicle subscription & refferel fee',
                ],
                [
                    'label' => ' Delivery Services',
                    'route' => 'admin.transport.delivery'
                ],
                [
                    'label' => 'Delivery subscription & refferel fee',
                    'route' => 'admin.nationalsubscription'
                ],
            ],
        ],
        [
            'label' => 'System Settings',
            'icon' => 'settings',
            'children' => [
                [
                    'label' => 'Sponsor text',
                    'route' => 'admin.config.sponsor'
                ],
                [
                    'label' => 'TAX',
                    'route' => 'admin.config.tax'
                ],
            ],
        ],
        [
            'label' => 'Orders',
            'icon' => 'reorder',
            'route' => 'admin.order.index',
        ],
        [
            'label' => 'Utilities Orders Payments',
            'icon' => 'reorder',
            'route' => 'admin.order.index',
        ],
        [
            'label' => 'Update About App',
            'icon' => 'settings',
            'route' => 'admin.aboutapp.index', // name of controller
        ],
        [
            'label' => 'Legal Terms & Policies',
            'icon' => 'settings',
            'role' => 'admin',
            'children' => [
                [
                    'label' => 'Terms Of Use',
                    'route' => 'admin.legal.index'
                ],
                [
                    'label' => 'Privacy Policy',
                    'route' => 'admin.privacy.index'
                ],
                [
                    'label' => 'Itellectual Property',
                    'route' => 'admin.intellectual.index'
                ],
                [
                    'label' => 'Cookies and Similar Technology Policy',
                    'route' => 'admin.cookies.index'
                ],
                [
                    'label' => 'Payments & Delivery',
                    'route' => 'admin.payment.index'
                ],
                [
                    'label' => 'Returns',
                    'route' => 'admin.returns.index'
                ],
            ]
            // 'route' => 'admin.legal.index', // name of controller
        ],
        [
            'label' => 'Utilities',
            'icon' => 'reorder',
            'role' => 'admin',
            'children' => [
                [
                    'label' => 'Services',
                    'icon' => 'settings',
                    'role' => 'admin',
                    'children' => [
                        [
                            'label' => 'EDSA',
                            'icon' => 'settings',
                           
                            'children' => [
                                [
                                    'label' => 'EDSA',
                                    'route' => 'admin.edsautilities.index',
                                ],
                                [
                                    'label' => 'Subscription and Referrel fee',
                                    'route' => 'admin.edsasubscription'
                                ]
                            ]
                        ],
                        [
                            'label' => 'DSTV',
                            'icon' => 'settings',
                            'children' => [
                                [
                                    'label' => 'DSTV',
                                    'route' => 'admin.dstvutilities.index'
                                ],
                                [
                                    'label' => 'Subscription and Referrel fee',
                                    'route' => 'admin.dstvsubscription'
                                ]
                            ]
                           
                        ],
                        [
                            'label' => 'STAR TIMES',
                            'icon' => 'settings',
                            'children' => [
                                [
                                    'label' => 'STAR TIMES',
                                    'route' => 'admin.starutilities.index'
                                ],
                                [
                                    'label' => 'Subscription and Referrel fee',
                                    'route' => 'admin.startimesubscription'
                                ]
                            ]
                            
                        ],
                        
                        // [
                        //     'label' => 'Autos',
                        //     'icon' => 'settings',
                        //     'children' => [
                        //         [
                        //             'label' => 'Autos',
                        //             'route' => 'admin.autosubscription'
                        //         ]
                        //     ]
                        // ],
                        // [
                        //     'label' => 'Real Estate',
                        //     'icon' => 'settings',
                        //     'children' => [
                        //         [
                        //             'label' => 'Real Estate',
                        //             'route' => 'admin.realsubscription'
                        //         ]
                        //     ]
                        // ],
                        [
                            'label' => 'SACTON',
                            'icon' => 'settings',
                            'children' => [
                                [
                                    'label' => 'Sacton',
                                    'route' => 'admin.sactonsubscription'
                                ]
                            ]
                        ],
                        // [
                        //     'label' => 'GUMA VALLEY',
                        //     'route' => ''
                        // ]

                    ]
                ],
                [

                    'label' => 'Saved Details',
                    'icon' => 'bolt',
                    'role' => 'admin',
                    'children' => [
                        [
                            'label' => 'Saved Meters',
                            'route' => 'admin.savedmeter.index'
                
                        ],
                        [
                            'label' => 'Saved DSTV Recharge Cards',
                            'route' => 'admin.saveddstvrechargecard.index'
                
                        ],
                        [
                            'label' => 'Saved Star Times Recharge Cards',
                            'route' => 'admin.savedstartimerechargecard.index'
                
                        ],
                    ]
                ],
                [
                    'label' => 'Agents',
                    'icon' => 'people',
                    'role' => 'admin',
                    'children' => [
                        [
                            'label' => 'EDSA Agents',
                            'route' => 'admin.edsauser.index'
                        ],
                        [
                            'label' => 'DSTV Agents',
                            'route' => 'admin.edsauser.index'
                        ],
                        [
                            'label' => 'STAR TIMES Agents',
                            'route' => 'admin.edsauser.index'
                        ],
                        [
                            'label' => 'GUMA Valley Agents',
                            'route' => 'admin.edsauser.index'
                        ],
                    ]
                ],
                [
                    'label' => 'Transactions',
                    'icon' => 'money',
                    'role' => 'admin',
                    'children' => [
                        [
                            'label' => 'EDSA',
                            'route' => 'admin.edsatransaction.index'
                        ],
                        [
                            'label' => 'DSTV',
                            'route' => 'admin.dstvtransaction.index'
                        ],
                        [
                            'label' => 'STAR TIMES',
                            'route' => 'admin.startransaction.index'
                        ]
                    ]
                ],
            ]
        ],
        [
            'label' => 'Reporting',
            'icon' => 'settings',
            'children' => [
                [
                    'label' => 'Orders Report',
                    'route' => 'admin.order-report.index'
                ],
                [
                    'label' => 'Digital Addresses Report',
                    'route' => 'admin.digitl-address.index'
                ],
                [
                    'label' => 'Autos Report',
                    'route' => 'admin.auto-report.index',
                ],
                [
                    'label' => 'Real Estate Report',
                    'route' => 'admin.real-estate-report.index'
                ],
            ],
        ],
        [
            'label' => 'Sea-air freights',
            'icon' => 'settings',
            'route' => 'admin.seafrieghts',
        ],
        [
            'label' => 'Collection and payments',
            'icon' => 'settings',
            'route' => 'admin.collection',
        ],
        [
            'label' => 'Money Transfer',
            'icon' => 'settings',
            'route' => 'admin.money.transfer',
        ],
        [
            'label' => 'Notifications',
            'icon' => 'settings',
            'route' => 'admin.notification',
        ],
        [
            'label' => 'News',
            'icon' => 'settings',
            'children' => [
                [
                    'label' => 'State News',
                    'route' => 'admin.newssubscription'
                ],
                [
                    'label' => ' State news subscription & refferel fee',
                    'route' => 'admin.statesubscription'
                ],
                [
                    'label' => 'National News',
                    'route' => 'admin.national-news',
                ],
                [
                    'label' => 'National news subscription & refferel fee',
                    'route' => 'admin.nationalsubscription'
                ],
                [
                    'label' => 'Public Notice',
                    'route' => 'admin.notice'
                ],
                [
                    'label' => 'Public Notice subscription & refferel fee',
                         'route' => 'admin.noticesubscription'
                ]
            ],
        ],
        [
            'label' => 'Store Theme',
            'icon' => 'settings',
            'route' => 'admin.seller-themes.create'
        ]

    ],

    'seller_side_nav' => [
        [
            'label' => 'Dashboard',
            'icon' => 'dashboard',
            'route' => 'admin.dashboard'
        ]
    ]
];
