<?php

/**
 * Created by PhpStorm.
 * Customer: Kamlesh
 * Date: 11/4/2017
 * Time: 11:33 AM
 */

namespace App\Logic;

use Illuminate\Http\Request;
use App\Models\SystemConfig as SystemConfigModel;

class SystemConfig
{
    /* Option Group Names */
    const ABOUT_GROUP = 'about';
    const SPONSOR_GROUP = 'sponsor';
    const TAX_GROUP = 'tax';
    const CONTACT_GROUP = 'contact';
    const META_GROUP = 'meta';
    const CREDIT_GROUP = 'credit';
    const CATALOG_GROUP = 'catalog';
    const SOCIAL_GROUP = 'social_group';
    const NOTICE_GROUP = 'notice';

    /* Option Group Fields */
    const OPTION_TOTAL_EMPLOYEES = 'total_employees';
    const OPTION_TOTAL_CLIENT = 'total_client';
    const OPTION_LOGO = 'logo';
    const OPTION_ABOUT = 'about';
    const OPTION_APP_STORE = 'app_store';
    const OPTION_GOOGLE_PLAY = 'google_play_store';
    const OPTION_TAG_LINE = 'tag_line';


    const OPTION_CONTACT_OPENING_HOUR = 'opening_hour';
    const OPTION_ADDRESS = 'address';
    const OPTION_POSTAL_ADDRESS = 'postal_address';
    const OPTION_TOLLFREE_NUMBER = 'tollfree';
    const OPTION_CONTACT_EMAIL = 'contact_email';
    const OPTION_ENQUIRY_EMAIL = 'enquiry_email';
    const OPTION_CONTACT_NUMBER_ONE = 'contact_one';
    const OPTION_CONTACT_NUMBER_TWO = 'contact_tow';
    const OPTION_CONTACT_NUMBER_THREE = 'contact_three';

    const OPTION_META_TITLE = 'meta_title';
    const OPTION_META_DESCRIPTION = 'meta_description';
    const OPTION_META_KEYWORDS = 'meta_keywords';

    const OPTION_CREDIT_PRICE = 'credit_price';
    const OPTION_CREDIT = 'credit';
    const OPTION_MIN_CREDIT = 'min_credit';

    const OPTION_BUSINESS_CATALOG = 'business_catalog';

    const OPTION_FACEBOOK = 'facebook';
    const OPTION_LINKEDIN = 'linkedin';
    const OPTION_TWITTER = 'twitter';
    const OPTION_YOUTUBE = 'youtube';
    const OPTION_PINTEREST = 'pinterest';
    const OPTION_INSTAGRAM = 'instagram';

    const OPTION_PUBLIC_NOTICE = 'public_notice';

    const PLACE_SPONSOR = 'place_sponsor';

    const PRODUCT_SPONSOR = 'product_sponsor';

    const AUTO_SPONSOR = 'auto_sponsor';

    const REAL_STATE_SPONSOR = 'real_state_sponsor';

    const QUIZ_SPONSOR = 'quiz_sponsor';
    
    const EDSA_SPONSOR = 'edsa_sponsor';
    const DSTV_SPONSOR = 'dstv_sponsor';
    const STAR_SPONSOR = 'star_sponsor';

    const DIGITAL_ADMINISTRATION = 'digital_administration';
    const TRANSPORT = 'transport';
    const FUEL = 'fuel';
    const GST = 'gst';
    const TIP = 'tip';

    protected static $configOptionGroup = [
        self::ABOUT_GROUP => [
            self::OPTION_TOTAL_EMPLOYEES,
            self::OPTION_TOTAL_CLIENT,
            self::OPTION_LOGO,
            self::OPTION_ABOUT,
            self::OPTION_APP_STORE,
            self::OPTION_GOOGLE_PLAY,
            self::OPTION_TAG_LINE
        ],
        self::CONTACT_GROUP => [
            self::OPTION_CONTACT_OPENING_HOUR,
            self::OPTION_ADDRESS,
            self::OPTION_POSTAL_ADDRESS,
            self::OPTION_TOLLFREE_NUMBER,
            self::OPTION_CONTACT_EMAIL,
            self::OPTION_ENQUIRY_EMAIL,
            self::OPTION_CONTACT_NUMBER_ONE,
            self::OPTION_CONTACT_NUMBER_TWO,
            self::OPTION_CONTACT_NUMBER_THREE
        ],
        self::META_GROUP => [
            self::OPTION_META_TITLE,
            self::OPTION_META_KEYWORDS,
            self::OPTION_META_DESCRIPTION
        ],
        self::CREDIT_GROUP => [
            self::OPTION_CREDIT,
            self::OPTION_CREDIT_PRICE,
            self::OPTION_MIN_CREDIT
        ],
        self::CATALOG_GROUP => [
            self::OPTION_BUSINESS_CATALOG
        ], self::SOCIAL_GROUP => [
            self::OPTION_FACEBOOK,
            self::OPTION_TWITTER,
            self::OPTION_LINKEDIN,
            self::OPTION_YOUTUBE,
            self::OPTION_INSTAGRAM,
            self::OPTION_PINTEREST
        ],
        self::NOTICE_GROUP => [
            self::OPTION_PUBLIC_NOTICE
        ],
        self::SPONSOR_GROUP => [
            self::PLACE_SPONSOR,
            self::AUTO_SPONSOR,
            self::REAL_STATE_SPONSOR,
            self::QUIZ_SPONSOR,
            self::PRODUCT_SPONSOR,
            self::EDSA_SPONSOR,
            self::DSTV_SPONSOR,
            self::STAR_SPONSOR,
        ],
        self::TAX_GROUP => [
            self::DIGITAL_ADMINISTRATION,
            self::TRANSPORT,
            self::FUEL,
            self::GST,
            self::TIP,
        ]

    ];

    /* Table fields */
    const OPTION_NAME_FIELD = 'option_name';
    const OPTION_VALUE_FILED = 'option_value';

    public static function getOption($optionName, $default = null)
    {
        static $options;

        if (!$options) {
            $options = SystemConfigModel::pluck(self::OPTION_VALUE_FILED, self::OPTION_NAME_FIELD);
        }


        if (isset($options[$optionName])) {
            return $options[$optionName];
        } else if ($default) {
            return $default;
        } else if ($value = self::getDefaultConfig($optionName)) {
            return $value;
        } else {
            return null;
        }
    }

    public static function getDefaultConfig($optionName)
    {
        return config('config.' . $optionName) ?: null;
    }

    public static function getOptionGroup($optionGroupName)
    {
        $optionSource = SystemConfigModel::whereIn(self::OPTION_NAME_FIELD, self::getOptionGroupFiled($optionGroupName))
            ->pluck(self::OPTION_VALUE_FILED, self::OPTION_NAME_FIELD)->toArray();

        $options = new \stdClass();

        foreach (self::getOptionGroupFiled($optionGroupName) as $optionName) {
            $options->{$optionName} = isset($optionSource[$optionName]) ? $optionSource[$optionName] : self::getDefaultConfig($optionName);
        }

        return $options;
    }


    public static function saveOption($optionName, $optionValue)
    {
        $option = SystemConfigModel::firstOrNew([
            self::OPTION_NAME_FIELD => $optionName
        ]);

        $option->option_value = $optionValue;
        $option->save();

        return $option;
    }

    public static function saveGroupOptions(Request $request, $optionGroupName)
    {
        $optionFields = self::getOptionGroupFiled($optionGroupName);

        \DB::beginTransaction();

        foreach ($optionFields as $field) {
            self::saveOption(
                $field,
                $request->input($field) ?: ''
            );
        }

        \DB::commit();

        return true;
    }

    public static function getOptionGroupFiled($optionGroupName)
    {
        return isset(self::$configOptionGroup[$optionGroupName]) ? self::$configOptionGroup[$optionGroupName] : [];
    }
}
