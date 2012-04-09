<?php
abstract class HttpResourceService
{
    abstract public function getResourceNamePluralized();
    abstract public function getResourceNameSingularized();
    
    public function getAuthenticatedApplicationId()
    {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
        {
            $application_service = Services::get('Application');
            try
            {
                $application = $application_service->getApplicationByIdAndSecret($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                return $application['id'];
            }
            catch (Exception $exception)
            {
                throw new Exception('Invalid application id and secret for authentication provided!');
            }
        }

        throw new Exception('No authentification information provided!');
    }
}