<?php
/**
 * Task for downloading Bank information from data obtained from PaymentsNZ
 * This parses the file and creates a Bank model from each line
 * The data is no longer tab-delimited and depends on the db column lengths
 * defined on your model. See notes on Bank::$db for more information
 * @author  Elliot Sawyer <elliot.sawyer@gmail.com>
 * @license MIT https://github.com/silverstripe-elliot/nzbankvalidator/blob/master/LICENSE
 */
class DownloadDataFromPaymentsNZTask extends BuildTask {
	public $title = 'Download bank branch information from PaymentsNZ';
	public $description = 'This information is publicly available from Payments NZ. Load NZ bank information into the database so the application can identify bank account numbers.';

	public function run($request) {
		//obtain previously saved result
		$file = File::find('assets/payments/payments.txt');
		if(!$file) {

			//download payments information from paymentsnz website
			$svc = RestfulService::create('http://www.paymentsnz.co.nz/cms_show_download.php', 3600);
			$svc->setQueryString(array('id' => '83'));
			$payments = $svc->request('/');

			if($payments->getStatusCode() === 200) {
				$data = $payments->getBody();
				$folder = Folder::find_or_make('/payments/');
				file_put_contents($folder->getFullPath()."payments.txt", $data);

				$file = new File();
				$file->Title = 'Payments NZ bank branch information';
				$file->Filename = $folder->getFullPath()."payments.txt";
				$file->ParentID = $folder->ID;
				$file->write();
			}
		}
		$contents = file($file->getFullPath());
		$count = 0;

		//processed schema from Bank class
		$schema = [];

		$existingBanks = Bank::get()->Sort(['BankNumber ASC', 'BranchNumber ASC'])->map('Prefix', 'ID')->toArray();

		/*
		* Bank file isn't tab-delimited anymore.
		* Get string lengths from Bank class's schema for later use
		* [ColumnName] => Size
		*/
		foreach(Bank::$db as $column => $field) {
			//from schema definition, these are always Varchar(##)
			//size is thus anything in field without a number
			$this->schema[$column] = preg_replace('/[^0-9]/', '', $field);

			//in case someone removed the number on varchar field
			if(empty($this->schema[$column])) {
				$this->schema[$column] = Bank::create()->dbObject($column)->size;
			}

			//if it's still empty, throw an error
			if(empty($this->schema[$column])) {
				throw new Exception('Your Varchar field on '.$column.' needs a length');
			}

		}
		foreach($contents as $id => $bank) {
			if($id === 0) continue;	//skip header

			//we need to parse each line by known lengths
			$bankChars = str_split($bank);
			$bankDetails = null;

			//use array_splice to remove $size characters from the array
			//join and trim the result to obtain the token
			foreach($this->schema as $column => $size) {
				$bankDetails[$column] = trim(
					join(
						array_splice(
							$bankChars, 0, $size
						)
					)
				);
			}

			$prefix = $bankDetails['BankNumber'].$bankDetails['BranchNumber'];

			if(empty($existingBanks[$prefix])) {
				$bank = Bank::create($bankDetails);
				$bank->write();
				$count++;
			}
		}

		debug::dump($count." records written");
	}
}