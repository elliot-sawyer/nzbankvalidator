<?php
/**
 * Bank model - describes a Bank from data obtained from PaymentsNZ
 * @author  Elliot Sawyer <elliot.sawyer@gmail.com>
 * @license MIT https://github.com/silverstripe-elliot/nzbankvalidator/blob/master/LICENSE
 */

class Bank extends DataObject {
	private static $indexes = array(
		'BankNumber' => true,
		'BranchNumber' => true
	);

	static $summary_fields = array(
		'BankNumber' => 'BankNumber',
		"BranchNumber" => 'BranchNumber',
		'BankName' => 'BankName',
		'City' => 'City'
	);

	/**
	 * Schema of Payments NZ information
	 * Public Info: http://www.paymentsnz.co.nz/clearing-systems/bulk-electronic-clearing-system
	 * Public Data: http://www.paymentsnz.co.nz/cms_show_download.php?id=83
	 * Public Schema: http://www.paymentsnz.co.nz/cms_show_download.php?id=25
	 * @var array
	 */
	static $db = array(
		'BankNumber' => 'VarChar(2)',
		'BranchNumber' => 'VarChar(4)',
		'NationalClearingCode' => 'VarChar(6)',	//Required if BIC Plus Indication Flag is set to Y (Combines bank and branch numbers)
		'BIC' => 'VarChar(11)',	//Either 8 or 11 character SWIFT BIC reference, otherwise blank
		'BankName' => 'VarChar(70)',	//full name of bank
		'BranchInformation' => 'VarChar(70)', //branch name
		'City' => 'VarChar(70)', //city/town where branch is located
		'PhysicalAddress1' => 'VarChar(35)',	//The actual location, ie: floor number, building name, street number and street name
		'PhysicalAddress2' => 'VarChar(35)',
		'PhysicalAddress3' => 'VarChar(35)',
		'PhysicalAddress4' => 'VarChar(35)',
		'PostCode' => 'VarChar(15)', //post code of physical address
		'Location' => 'VarChar(90)', //not used
		'CountryName' => 'VarChar(70)', //default New Zealand
		'POBNumber' => 'VarChar(35)', //po box number
		'POBLocation1' => 'VarChar(20)', //po box address excludeing number
		'POBLocation2' => 'VarChar(35)',
		'POBLocation3' => 'VarChar(35)',
		'POBPostCode' => 'VarChar(15)',
		'POBCountry' => 'VarChar(70)',	//post cost of post office box
		'STD' => 'VarChar(4)', //Area code
		'Phone' => 'VarChar(14)', //Phone number excluding Area Code
		'Fax' => 'VarChar(14)', //Fax number excluding Area Code
		'Retail' => 'VarChar(1', //R = if retail branch. Otherwise blank. (This field is no longer in use)
		'BICPlusIndicator' => 'VarChar(1)', //Y = include on BIC Plus File,N or Null = do not include on BIC Plus File
		'LatestStatus' => 'VarChar(1)' //A = Added/new record,M = Modified,U = Update/no change,C = Closed
	);

	/*
	* Getter for the six digit Bank prefix
	* This is *not* the same as NationalClearingCode, which can be empty
	 */
	public function getPrefix() {
		return $this->BankNumber.$this->BranchNumber;
	}
	/*
	* Identifies a bank branch by the first six digits of the account number
	* This is not the same as bank account validation, which validates against a checksum
	* See information in the NZBankAccount class for information on doing this.
	* 
	* @param accountNumber - an NZ Bank account number. All non-digits will be stripped
	*                         but you should attempt to clean up as much as possible
	*                        Only the first six numbers are used, the rest are discarded
	* @return the Bank object or false if it couldn't be identified
	 */
	public static function identify($accountNumber) {
		$bankAccount = preg_replace("/[^0-9]/", '', $accountNumber);
		$bankNumber = substr($bankAccount, 0, 2);
		$branchNumber = substr($bankAccount, 2, 4);

		$bank = Bank::get()->filter(array(
			'BankNumber' => $bankNumber,
			'BranchNumber' => $branchNumber,
		))->First();
		return ($bank && $bank->ID) ? $bank : false;
	}

	/*
	* "Prettify" the bank account information prior to displaying it
	* @return String: A normalized bank account number in the following format:
	*         BankNumber-BranchNumber-Account-Suffix
	*	      - OR -
	* 		  null if bank cannot be identified
	*
	 */
	public static function prettify($accountNumber) {
		$bankAccount = preg_replace("/[^0-9]/", '', $accountNumber);
		if(preg_match("/^(\d{2})(\d{4})(\d{7})(\d+)$/", $bankAccount, $matches)) {
			
			if($bank = self::identify($bankAccount)) {
				array_shift($matches);
				return implode('-', $matches);
			}			
		}
		return null;
	}
}