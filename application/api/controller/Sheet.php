<?php
namespace app\api\controller;

class Sheet extends Base
{
	function googleSheets(){
		$client = new \Google_Client();
	    $client->setApplicationName('Google Sheets API PHP');
	    $client->setScopes(\Google_Service_Sheets::SPREADSHEETS);
	    $client->setAuthConfig(diskPath('public/json/google_sheets_key.json'));
	    $client->setAccessType('offline');

		$service = new \Google_Service_Sheets($client);
		$spreadsheetId = '117DYC4Iv9XZ_qkyHyLJ1dm8vZXkgyQJfoV0tSgoiki4';

		// read data
		try{
			$range = 'test!A1:B';
			$response = $service->spreadsheets_values->get($spreadsheetId, $range);
			$values = $response->getValues();
			echo '<pre>', print_r($values, 1);
		}catch(\Exception $e){
			echo "权限不足，请共享至: sheetsphp@develop-277604.iam.gserviceaccount.com";
		}
	

		// update data
		// $range = 'test!A2:B2';
		// $values = [
		// 	["test1", 11]
		// ];
		// $body = new \Google_Service_Sheets_ValueRange([
		// 	'values' => $values
		// ]);
		// $params = [
		// 	'valueInputOption' => 'RAW'
		// ];
		// $result = $service->spreadsheets_values->update(
		// 	$spreadsheetId,
		// 	$range,
		// 	$body,
		// 	$params
		// );

		// add data
		// $range = 'test';
		// $values = [
		// 	["new", 10]
		// ];
		// $body = new \Google_Service_Sheets_ValueRange([
		// 	'values' => $values
		// ]);
		// $params = [
		// 	'valueInputOption' => 'RAW'
		// ];
		// $insert = [
		// 	'insertDataOption' => "INSERT_ROWS"
		// ];
		// $result = $service->spreadsheets_values->append(
		// 	$spreadsheetId,
		// 	$range,
		// 	$body,
		// 	$params,
		// 	$insert
		// );
	}
}