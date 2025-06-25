<?php

namespace App\Logic;

use Illuminate\Support\Facades\Auth;

class AdminMenu
{
    public static function getMenuHtml()
    {
            $menuTree = config('admin.side_nav');
        
        
        return self::arrayToHtml($menuTree);
    }

    protected static function renderMenuItem($label, $link, $icon, $hasChild = false, $childrenHtml = '', $role = null)
    {
        //dd($link, request()->fullUrl(), strpos($link, request()->fullUrl()));
        if ($role) {
            $userRole = implode('', json_decode(request()->user()->getRoleNames(), true));

            $roles = explode('|', $role);

            if (!in_array($userRole, $roles)) {
                return;
            }
        }
        return '<li ' . (($link === request()->fullUrl()) ? 'class="active"' : '') . '>
            <a class="' . ($hasChild ? 'menu-toggle' : '') . ' waves-effect waves-block" href="' . $link . '">
                ' . ($icon ? '<i class="material-icons">' . $icon . '</i>' : '') . '
                <span>' . $label . '</span>
            </a>
            ' . $childrenHtml . '
        </li>';
    }

    protected static function arrayToHtml($menuTree)
    {
        $outputHtml = '';

        foreach ($menuTree as $menu) {

            if (isset($menu['admin'])) {
                $hasAccess = Auth::user('admin')->is_admin;

                if (!$hasAccess) {
                    continue;
                }
            }

            $outputHtml .= self::renderMenuItem(
                $menu['label'],
                !empty($menu['route']) ? route($menu['route']) : ((!empty($menu['url'])) ? url($menu['url']) : 'javascript:void(0)'),
                !empty($menu['icon']) ? $menu['icon'] : null,
                isset($menu['children']),
                isset($menu['children']) ? '<ul class="ml-menu">' . self::arrayToHtml($menu['children']) . '</ul>' : '',
                (isset($menu['role']) ? $menu['role'] : null)
            );
        }

        return $outputHtml;
    }
}
