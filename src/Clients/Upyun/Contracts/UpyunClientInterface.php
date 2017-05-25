<?php namespace Moregold\Infrastructure\Clients\Upyun\Contracts;

interface UpyunClientInterface
{
    public function uploadImage($path = '', $file = '');
    public function deleteImage($path = '');
    public function request($request_method = '', $path = '');
}
