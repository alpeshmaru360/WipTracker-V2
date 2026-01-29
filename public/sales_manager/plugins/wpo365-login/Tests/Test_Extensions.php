<?php

namespace Wpo\Tests;

use \Wpo\Core\Extensions_Helpers;

// Prevent public access to this script
defined('ABSPATH') or die();

if (!class_exists('\Wpo\Tests\Test_Extensions')) {

    class Test_Extensions
    {

        private $extensions = [];

        public function __construct()
        {
            $extensions = Extensions_Helpers::get_extensions();

            foreach ($extensions as $slug => $extension) {

                if ($extension['activated']) {
                    $this->extensions[$slug] = $extension;
                }
            }
        }

        public function test_wpo365_premium()
        {
            return $this->get_test_result_for_extensions('wpo365-login-premium/wpo365-login.php', 'WPO365 | SYNC', 24.1);
        }

        public function test_wpo365_sync_5y()
        {
            return $this->get_test_result_for_extensions('wpo365-sync-5y/wpo365-sync-5y.php', 'WPO365 | SYNC | 5Y', 24.1);
        }

        public function test_wpo365_intranet_5y()
        {
            return $this->get_test_result_for_extensions('wpo365-intranet-5y/wpo365-intranet-5y.php', 'WPO365 | SYNC', 24.2);
        }

        public function test_wpo365_customers()
        {
            return $this->get_test_result_for_extensions('wpo365-customers/wpo365-customers.php', 'WPO365 | CUSTOMERS', 24.1);
        }

        public function test_wpo365_intranet()
        {
            return $this->get_test_result_for_extensions('wpo365-login-intranet/wpo365-login.php', 'WPO365 | INTRANET', 24.2);
        }

        public function test_wpo365_profile_plus()
        {
            return $this->get_test_result_for_extensions('wpo365-login-plus/wpo365-login.php', 'WPO365 | PROFILE+', 24.0);
        }

        public function test_wpo365_mail()
        {
            return $this->get_test_result_for_extensions('wpo365-mail/wpo365-mail.php', 'WPO365 | MAIL', 24.0);
        }

        public function test_wpo365_login_plus()
        {
            return $this->get_test_result_for_extensions('wpo365-login-professional/wpo365-login.php', 'WPO365 | LOGIN+', 24.0);
        }

        public function test_wpo365_avatar()
        {
            return $this->get_test_result_for_extensions('wpo365-avatar/wpo365-avatar.php', 'WPO365 | AVATAR', 24.0);
        }

        public function test_wpo365_custom_user_fields()
        {
            return $this->get_test_result_for_extensions('wpo365-custom-fields/wpo365-custom-fields.php', 'WPO365 | CUSTOM USER FIELDS', 24.0);
        }

        public function test_wpo365_groups()
        {
            return $this->get_test_result_for_extensions('wpo365-groups/wpo365-groups.php', 'WPO365 | GROUPS', 24.0);
        }

        public function test_wpo365_apps()
        {
            return $this->get_test_result_for_extensions('wpo365-apps/wpo365-apps.php', 'WPO365 | APPS', 24.1);
        }

        public function test_wpo365_documents()
        {
            return $this->get_test_result_for_extensions('wpo365-documents/wpo365-documents.php', 'WPO365 | DOCUMENTS', 2.0);
        }

        public function test_wpo365_roles_access()
        {
            return $this->get_test_result_for_extensions('wpo365-roles-access/wpo365-roles-access.php', 'WPO365 | ROLES + ACCESS', 24.0);
        }

        public function test_wpo365_scim()
        {
            return $this->get_test_result_for_extensions('wpo365-scim/wpo365-scim.php', 'WPO365 | SCIM', 24.1);
        }

        private function get_test_result_for_extensions($slug, $title, $version)
        {
            $test_result = new Test_Result("Latest version $title is installed", Test_Result::CAPABILITY_EXTENSIONS, Test_Result::SEVERITY_CRITICAL);
            $test_result->passed = true;

            if (!array_key_exists($slug, $this->extensions)) {
                return;
            }

            if ($this->extensions[$slug]['version'] < $version) {
                $test_result->passed = false;
                $test_result->message = "There is a newer version available for the <em>$title</em> plugin. Please update now.";
                $test_result->more_info = 'https://docs.wpo365.com/article/13-update-the-wpo365-plugin-to-the-latest-version';
            }

            return $test_result;
        }
    }
}
