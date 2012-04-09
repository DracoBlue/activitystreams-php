<?php
abstract class HttpResourceService
{
    abstract public function getResourceNamePluralized();
    abstract public function getResourceNameSingularized();
    
    public function getAuthenticatedApplicationId()
    {
        if (isset($_SERVER['PHP_AUTH_PW']) && isset($_SERVER['PHP_AUTH_USER']))
        {
            return $_SERVER['PHP_AUTH_USER'];
        }

        throw new Exception('No authentification information provided!');
    }
}