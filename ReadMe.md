Currency Exchange Class
======================

Description
--------

author: [Stamo Petkov] (http://www.stamopetkov.eu)
mail: stamo.petkov@gmail.com
version: 1.0.0
name: CURRENCY

This class contains some functions to manipulate currencies. It gets information from the servers of European Central Bank. To get list of available currencies, please use get_currency_list () method.

Constructor
------------

When called, constructor tries to connect to ECB server and download current currency rates. On success it will parse the XML file and will set $XML_parsed public property to "true". Please check this property to find whether or not the XML file was successfully downloaded and parsed. The reason why the constructor won’t throw an exception if not succeeded to initialise rate table, is that Bulgarian Lev (BGN) is "strictly tied" to Euro (EUR) so it will always have value. First this class was intended to be used in Bulgaria, so even if we don't have full rates table, it will still provide some basic functionality for currency conversion between EUR and BGN.

Public Properties
----------

public $date 
You can check this property to get current rates date. Please, note that the reference rates are usually updated by 3 p.m. C.E.T. They are based on a regular daily concertation procedure between central banks across Europe and worldwide, which normally takes place at 2.15 p.m. CET. 

public $error 
This property is not longer needed, but I decided to leave it for now. If error happens it will contain the error message. But methods are throwing exceptions which contain same error messages.

public $XML_parsed 
As I mentioned above, this property will show whether downloading and parsing of the XML file was successful. 

Methods
-----------

There are several public methods that provide the main functionality of the class. They all use rates table that was created during the construction of the class. All rates in this table are calculated towards base currency (EUR by default). 

set_base_currency ($currency = 'EUR') 
This method sets given currency as base. Base currency is used for displaying rates table and conversions. All calculations are performed according to base currency!
parameters:
String $currency Currency code. EUR by default
exceptions: 
Throws exception if currency code is not in currency list

exchange ($ammount, $from, $to = '')
This method performs currency exchange on the given amount from one currency to the other.
Parameters:
String $ammount The am mount to be exchanged
String $from Currency of the amount (three letter code)
String $to Currency to witch we wish to exchange. Base currency if not specified.
Return:
Float - the exchanged amount on success
Exceptions:
Throws exception if currency code is not in currency list

cross_rate ($from, $to)
This method gets the cross rate between two currencies
Parameters:
String $from first Currency (three letter code)
String $to second Currency (three letter code)
Return:
Float - the cross rate on success
Exceptions:
Throws exception if currency code is not in currency list

get_rates_table ($visible = array('all')) 
This method generates the html rates table based on Base currency. You can perform some basic styling using "rates_table" class. I know that the HTML code could be better and this is my first TODO.
Parameters:
Array $visible list of Currencies to be included in the table. All currencies by default. In general you want need to show all currencies provided by ECB, so you can choose which to show by submitting an array with currency codes.
Return:
String HTML formatted table with exchange rates. Can be styled with CSS selector .rates_table
Constants:
You must set several string constants in order to view text in proper language. Here is a list of predefined constants for english language.
* define("_TABLE_TITLE", "Reference rates of European Central Bank");
* define("_BASE_CURRENCY_REFERENCE", "All rates are for 1 ");
* define("_CURRENCY_STRING", "Currency");
* define("_RATE_STRING", "Rate");

get_currency_list ()
This method gets the list of available currencies. If you are not sure which code to use, call this method and it will return a list of currency codes.
Return: Array list of all available currencies

Legal
------------

This class is completely open source. Feel free to modify it and use it as you desire. Please, keep in mind that I take no responsibility if something goes wrong. You use this class on your own risk! 
