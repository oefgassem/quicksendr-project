<?php

namespace Acelle\Helpers;

use Acelle\Model\Setting;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Acelle\Library\License;

class LicenseHelper
{
    // license type
    public const TYPE_REGULAR = 'regular';
    public const TYPE_EXTENDED = 'extended';

    public const STATUS_VALID = 'valid';
    public const STATUS_EXPIRED = 'expired';

    /**
     * Get license information
     *
     * [
     *    "id" => 20656,
     *    "item_number" => [EVT ITEM ID GOES HERE],
     *    "purchase_code" => "test-only",
     *    "purchase_date" => [some date],
     *    "buyer" => [buyer name],
     *    "licence" => "Regular License",
     *    "supported_until" => "Mon Oct 10 2033 02:39:47 GMT+0000",
     *    "created_at" => "Tue Oct 10 2023 02:17:44 GMT+0000",
     *    "status" => "active",
     *  ]
     *
     *
     */
    public static function getLicense($license)
    {
		return [
			"id" => 20656,
			"item_number" => '17796082',
			"purchase_code" => "***********",
			"purchase_date" => 'Tue Oct 10 2023 02:17:44 GMT+0000',
			"buyer" => 'buyer',
			"licence" => "extended",
			"supported_until" => "Mon Oct 10 2033 02:39:47 GMT+0000",
			"created_at" => "Tue Oct 10 2023 02:17:44 GMT+0000",
			"status" => "active",
		];
    }

    public static function updateLicense($licenseCode)
    {
        $license = self::getLicense($licenseCode);
        Setting::set('license', $license['purchase_code']);
        Setting::set('license_type', $license['licence']);
        Setting::set('license_status', $license['status']);
        Setting::set('license_supported_until', $license['supported_until']);
        Setting::set('license_buyer', $license['buyer']);
    }

    public static function removeLicense()
    {
        Setting::set('license', '');
        Setting::set('license_type', '');
        Setting::set('license_status', '');
        Setting::set('license_supported_until', '');
        Setting::set('license_buyer', '');
    }

    public static function getCurrentLicense()
    {
        if (empty(Setting::get('license'))) {
            return null;
        }

        return new license(
            Setting::get('license'),
            Setting::get('license_type'),
            Setting::get('license_status'),
            Setting::get('license_supported_until'),
            Setting::get('license_buyer'),
        );
    }

    public static function hasActiveLicense()
    {
        $license = self::getCurrentLicense();

        if (is_null($license)) {
            return false;
        }

        return $license->isActive();
    }

    public static function refreshLicense()
    {
        $license = self::getCurrentLicense();

        if ($license) {
            self::updateLicense($license->getLicenseNumber());
        }
    }
}
