##New Zealand Bank Validator
Maintained by Elliot Sawyer (elliot@silverstripe).

This is a module to validate New Zealand bank account numbers, by checking the first six digits against known banks and branches known to PaymentsNZ.

It does not do IRD modulus checks yet, but the work has been started in code/helpers/NZBankAccount.php

##Disclaimer
This project is not officially maintained by Silverstripe LTD. The information provided by PaymentsNZ cannot be independently verified, and is only as good as PaymentsNZ say it is.

##Deployment
1. Clone the project into your webroot (same as /cms and /framework)
2. Make sure your assets directory is writeable
3. Run a dev/build flush=1
4. Run the task /dev/tasks/DownloadDataFromPaymentsNZTask
5. Bank ID information can be Created/Retrieved/Updated/Deleted in the CMS "Bank identifier" section.

##Usage

```
$account = '12-3140-98798787-001';
$bank = Bank::identify($account);
Debug::dump($bank);
/* Output
...
            [ClassName] => Bank
            [Created] => 2014-09-07 20:48:30
            [LastEdited] => 2014-09-07 20:48:30
            [BankNumber] => 12
            [BranchNumber] => 3140
            [NationalClearingCode] => 123140
            [BankName] => ASB Bank
            [BranchInformation] => Lambton Quay
            [City] => Wellington Central
            [PhysicalAddress1] => 174-180 Lambton Quay
            [CountryName] => New Zealand
            [POBNumber] => P O Box 35
            [POBLocation1] => Shortland Street
            [POBLocation2] => Auckland
            [POBPostCode] => 1140
            [POBCountry] => New Zealand
            [STD] => (04)
            [Phone] => 499-0864
            [Fax] => 495-2102
            [Retail] => R
            [BICPlusIndicator] => Y
            [LatestStatus] => A
            [ID] => 1906
            [RecordClassName] => Bank
...
*/
```
