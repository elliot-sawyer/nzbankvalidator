<?php
/**
 * Bank admin - administers Bank models
 * @author  Elliot Sawyer <elliot.sawyer@gmail.com>
 * @license MIT https://github.com/silverstripe-elliot/nzbankvalidator/blob/master/LICENSE
 */
class NZBankAccount extends Object{
	//these algorithm weights are determined by IRD
	//http://www.ird.govt.nz/resources/3/b/3baac88049703941b60dbf37e0942771/rwt-nrwt-spec-2012.pdf
	public static $algorithm_weight_factors = [
		'a' => [0,0,6,3,7,9,0,0,10,5,8,4,2,1,0,0,0,0,11],

		'b' => [0,0,0,0,0,0,0,0,10,5,8,4,2,1,0,0,0,0,11],

		'c' => [3,7,0,0,0,0,9,1,10,5,3,4,2,1,0,0,0,0,11],

		'd' => [0,0,0,0,0,0,0,7, 6,5,4,3,2,1,0,0,0,0,11],

		'e' => [0,0,0,0,0,0,0,0, 0,0,5,8,4,2,0,0,0,1,11],

		'f' => [0,0,0,0,0,0,0,1, 7,3,1,7,3,1,0,0,0,0,10],

		'g' => [0,0,0,0,0,0,0,1, 3,7,1,3,7,1,0,3,7,1,10],

		'x' => [0,0,0,0,0,0,0,0, 0,0,0,0,0,0,0,0,0,0, 1]
	];

	/**
	 * Array of bank ID's, which map to an array (
	 * 		[algorithm] => specific ranges (comma delimited)
	 * These numbers are derived by the IRD here:
	 * 	http://www.ird.govt.nz/resources/3/b/3baac88049703941b60dbf37e0942771/rwt-nrwt-spec-2012.pdf
	 * @var array
	 */
	public static $bank_ids = [
		'01' => [' ' => '0001-0999,1100-1199,1800-1899'],
		'02' => [' ' => '0001-0999,1200-1299'],
		'03' => [' ' => '0001-0999,1300-1399,1500-1599,1700-1799,1900-1999'],
		'06' => [' ' => '0001-0999,1400-1499'],
		'08' => ['d' => '6500-6599'],
		'09' => ['e' => '0000'],
		'11' => [' ' => '5000-6499,6600-8999'],
		'12' => [' ' => '3000-3299,3400-3499,3600-3699'],
		'13' => [' ' => '4900-4999'],
		'14' => [' ' => '4700-4799'],
		'15' => [' ' => '3900-3999'],
		'16' => [' ' => '4400-4499'],
		'17' => [' ' => '3300-3399'],
		'18' => [' ' => '3500-3599'],
		'19' => [' ' => '4600-4649'],
		'20' => [' ' => '4100-4199'],
		'21' => [' ' => '4800-4899'],
		'22' => [' ' => '4000-4049'],
		'23' => [' ' => '3700-3799'],
		'24' => [' ' => '4300-4349'],
		'25' => ['f' => '2500-2599'],
		'26' => ['g' => '2600-2699'],
		'27' => [' ' => '3800-3849'],
		'28' => ['g' => '2100-2149'],
		'29' => ['g' => '2150-2299'],
		'30' => [' ' => '2900-2949'],
		'31' => ['x' => '2800-2849'],
		'33' => ['f' => '6700-6799'],
		'35' => [' ' => '2400-2499'],
		'38' => [' ' => '9000-9499'],
	];

	/*** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
	 * TEST TEST TEST - UNFINISHED - TEST TEST TEST
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
	 * 
	 * Validate a NZ bank account number against IRD specifications
	 * Requires a bank ID, branch, account, and suffix
	 * Components are to be delimited by non-numerics (space, dash, etc)
	 * @param  [type] $accountNumber [description]
	 * @return boolean true if bank account is mathematically valid
	 *                 false if modulus/checksum fails
	 */
	public static function validate($accountNumber) {
		$accountNumber = trim($accountNumber);
		//delimiter is anything non-numeric
		$parts = preg_split('/[^0-9]/', $accountNumber)
		if(count($parts) === 4) {

			//IRD requires components to be zero-padded on left to max length
			$bankID = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
			$bankBranch = str_pad($parts[1], 4, '0', STR_PAD_LEFT);
			$bankAccount = str_pad($parts[2], 8, '0', STR_PAD_LEFT);
			$bankSuffix  = str_pad($parts[3], 4, '0', STR_PAD_LEFT);

			//step 1 - identify the bank ID
			if(!empty(self::$bank_ids[$bankID])) {

				//bank ID
				$bank = self::$bank_ids[$bankID];

				//algorithm used by bank
				$algorithm = key($bank);

				//valid ranges
				$ranges = $bank[$algorithm];
				$ranges = explode(',', $ranges);
				$in_range = false;
				foreach($ranges as $range) {

				}
			}
		}

		return false;
	}

	/*** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
	 * TEST TEST TEST - UNFINISHED - TEST TEST TEST
	 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * 
	*/
	private static function within_range($number, $minimum, $maximum = null) {
		$number = (int) $number;
		$minimum = (int) $minimum;
		if($maximum === null && $number == $minimum) {
			return true;
		} else {
			$maximum = (int) $maximum;

			if( ($number >= $minimum) && $number <= $maximum) {
				return true;
			}
		}
		return false;
	}
}