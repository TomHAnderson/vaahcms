<?php

namespace WebReinvent\VaahCms\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class ExtendController extends Controller
{


    //----------------------------------------------------------
    public function __construct()
    {

    }

    //----------------------------------------------------------

    public static function extendTopLeftMenu()
    {

        $vue_prefix = "vaah";
        $base_url = route('vh.backend')."#/";
        $dashboard_url = $base_url."vaah/";
        $link = $base_url.$vue_prefix;

        $list[0] = [
            'link' => $dashboard_url,
            'label'=> 'Dashboard'
        ];

        $list[1] = [
            'link' => $dashboard_url,
            'label'=> 'Users & Access',

        ];

        if(\Auth::user()->hasPermission('has-access-of-registrations-section'))
        {
            $list[1]['child'][] =  [
                'link' => $link."/registrations/",
                'label'=> 'Registration'
            ];
        }

        if(\Auth::user()->hasPermission('has-access-of-users-section'))
        {
            $list[1]['child'][] =  [
                'link' => $link."/users/",
                'label'=> 'Users'
            ];
        }

        if(\Auth::user()->hasPermission('has-access-of-roles-section'))
        {
            $list[1]['child'][] =  [
                'link' => $link."/roles/",
                'label'=> 'Roles'
            ];
        }


        if(\Auth::user()->hasPermission('has-access-of-permissions-section'))
        {
            $list[1]['child'][] =  [
                'link' => $link."/permissions/",
                'label'=> \Auth::user()->hasPermission('has-access-of-permissions-section')
            ];
        }

        $list[2] = [
            'link' => route('vh.backend.dashboard'),
            'label'=> 'Extend',
            'child' => [
                [
                    'link' => route('vh.backend.dashboard'),
                    'label'=> 'Modules'
                ],
                [
                    'link' => route('vh.backend.dashboard'),
                    'label'=> 'Themes'
                ]
            ]
        ];

        $list[3] = [
            'link' => route('vh.backend.dashboard'),
            'label'=> 'Settings',
            'child' => [
                [
                    'link' => route('vh.backend.dashboard'),
                    'label'=> 'Localization'
                ],
            ]
        ];




        return $list;
    }

    //----------------------------------------------------------
    public static function extendTopRightUserMenu()
    {
        $list = [
            [
                'link' => route('vh.backend.logout'),
                'label'=> 'Logout'
            ],
        ];

        return $list;
    }
    //----------------------------------------------------------


}
