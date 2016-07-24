<?php namespace Moregold\Infrastructure\Clients\User\Contracts;

interface UserClientInterface
{
    public function verifyAuthenticated($access_token = null);
    public function getCurrentUser($access_token = null);
    public function getUsersInfo($ids = []);
    public function getUserInfo($id = null);
    public function requestAccess();
}
