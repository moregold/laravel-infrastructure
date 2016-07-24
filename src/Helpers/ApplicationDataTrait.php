<?php namespace Moregold\Infrastructure\Helpers;

use Illuminate\Support\Facades\Config;

trait ApplicationDataTrait
{
    /**
     * Get the login URL for a specific application by name
     *
     * @return string URL to application login page
     */
    public function getLoginUrl($application_name = 'marketing')
    {
        return $this->getBaseUrl($application_name).'/login';
    }

    /**
     * Get the password reset URL for a specific application by name
     *
     * @return string URL to application password reset page
     */
    public function getPasswordResetUrl($application_name = 'marketing')
    {
        return $this->getBaseUrl($application_name).'/password/reset';
    }

    /**
     * Get the base URL for a specific application by name
     *
     * @return string URL to application
     */
    public function getBaseUrl($application_name = 'marketing')
    {
        if ($application_name && Config::get('services.'.$application_name.'.base_url')) {
            return Config::get('services.'.$application_name.'.base_url');
        } else {
            return Config::get('services.marketing.base_url');
        }
    }

}
