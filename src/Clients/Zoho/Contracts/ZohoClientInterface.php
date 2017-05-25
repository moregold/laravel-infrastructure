<?php namespace Moregold\Infrastructure\Clients\Zoho\Contracts;

interface ZohoClientInterface
{
    public function postRequest($data = [], $method);
    public function requestAccessToken();
    public function formatXmlData($data = []);
    public function parseResponse($response = '');
    public function insertAppointmentRecord(&$appointment, $xml_data = [], $zoho_data = []);
    public function updateAppointmentRecord(&$appointment, $xml_data = [], $zoho_data = []);
    public function deleteAppointmentRecord(&$appointment);
}
