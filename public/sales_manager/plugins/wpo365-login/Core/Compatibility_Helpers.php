<?php

namespace Wpo\Core;

use \Wpo\Core\Wpmu_Helpers;
use \Wpo\Services\Log_Service;
use \Wpo\Services\Options_Service;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Core\Compatibility_Helpers')) {

    class Compatibility_Helpers
    {
        /**
         * Writes the compatibility warning as an error to the log and remembers it for 24 hours
         * to prevent flooding the log with the same error over and again.
         * 
         * @since   20.0
         * 
         * @param   string   $warning 
         * 
         * @return  void 
         */
        public static function compat_warning($warning)
        {
            $compat_warnings = Wpmu_Helpers::mu_get_transient('wpo365_compat_warnings');

            if (empty($compat_warnings) || !is_array($compat_warnings) || !in_array($warning, $compat_warnings)) {
                Log_Service::write_log('ERROR', $warning);

                if (is_array($compat_warnings)) {
                    $compat_warnings[] = $warning;
                } else {
                    $compat_warnings = array($warning);
                }

                // Transient shall block the repetition of this warning for 24 hours.
                Wpmu_Helpers::mu_set_transient('wpo365_compat_warnings', $compat_warnings, 86400);
            }
        }

        /**
         * Reduces the key of the extra_user_fields array by removing the name part for custom 
         * WordPress usermeta that was introduced with version 20.
         *  
         * @since   20.0
         * 
         * @param   array   $extra_user_fields  The array of extra user fields that will be updated
         *
         * @return  void 
         */
        public static function update_user_field_key($extra_user_fields)
        {
            if (!class_exists('\Wpo\Services\User_Details_Service') || method_exists('\Wpo\Services\User_Details_Service', 'parse_user_field_key')) {
                return $extra_user_fields;
            }

            // Iterate over the configured graph fields and identify any supported expandable properties
            $extra_user_fields = array_map(function ($kv_pair) {
                $marker_pos = WordPress_Helpers::stripos($kv_pair['key'], ';#');

                if ($marker_pos > 0) {
                    $kv_pair['key'] = substr($kv_pair['key'], 0, $marker_pos);
                }

                return $kv_pair;
            }, $extra_user_fields);

            $compat_warning = sprintf(
                '%s -> The administrator configured <em>Azure AD user attributes to WordPress user meta mappings</em> on the plugin\'s <strong>User sync</strong> page. These mappings have been recently upgraded to allow administrators to specify their own name for the usermeta key. This new feature, however, breaks existing functionality. To remain compatible you should update your premium WPO365 extension and optionally update the existing mappings.',
                __METHOD__
            );

            self::compat_warning($compat_warning);

            return $extra_user_fields;
        }
    }
}
