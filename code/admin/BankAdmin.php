<?php
class BankAdmin extends ModelAdmin {
	public static $menu_title = 'Bank Identifier';
	public static $url_segment = 'bank-identifier';
	public static $managed_models = array(
		'Bank'
	);
}