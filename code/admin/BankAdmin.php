<?php
/**
 * Bank admin - administers Bank models
 * @author  Elliot Sawyer <elliot.sawyer@gmail.com>
 * @license MIT https://github.com/silverstripe-elliot/nzbankvalidator/blob/master/LICENSE
 */
class BankAdmin extends ModelAdmin {
	public static $menu_title = 'Bank Identifier';
	public static $url_segment = 'bank-identifier';
	public static $managed_models = array(
		'Bank'
	);
}