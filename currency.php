<?php

/**
 * This class contains some functions to manipulate currencies.
 * It gets information from the servers of European Central Bank.
 * To get list of available currencies, please use get_currency_list () method.
 * After __construct check $XML_parsed - if false, no currencies loaded (no exception because of default value of BGN).
 * @author Stamo Petkov
 * @version 1.0.0
 * @name CURRENCY
 */
class CURRENCY {
        
        private $source_url = "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";
        private $XML;
        private $base_currency;
        private $exchange_rates = array ();
        private $rates_table = '';
        public $date = "";
        public $error = '';
        public $XML_parsed = false;
        
        public function __construct() {
            $this->XML=@simplexml_load_file($this->source_url);
            if ($this->XML) {
                $this->XML_parsed = true;
                $this->base_currency = "EUR";
                $this->date = $this->XML->Cube->Cube["time"];
                $date = explode("-", $this->date);
                $this->date = $date[2] . '.' . $date[1] . '.' . $date[0] . ' г. ';
                $this->exchange_rates[$this->base_currency] = '1.0000';
                foreach($this->XML->Cube->Cube->Cube as $rate){
                    $this->exchange_rates[(string)$rate["currency"]] = $rate["rate"];
                }
            } else {
                $this->XML_parsed = false;
                $this->base_currency = "EUR";
                $this->exchange_rates[$this->base_currency] = '1.0000';
                $this->exchange_rates['BGN'] = '1.9558';
            }
        }

        /**
        * Sets given currency as base. Base currency is used for displaying rates table and convertions. All calculations are performed according to base currency!
        *
        * @param String $currency Currency code. EUR by default
        * @Throw exception if currency code is not in currency list
        * @access Public
        */
        public function set_base_currency ($currency = 'EUR') {
            if (!array_key_exists($currency, $this->exchange_rates)) {
                $this->error = 'Unknown cerrency.';
                throw new Exception('Unknown cerrency.');
            }
            $this->base_currency = $currency;
            $factor = $this->exchange_rates[$this->base_currency];
            foreach ($this->exchange_rates as $curr => $rate) {
                $new_rate = (float)$rate / (float)$factor;
                $this->exchange_rates[$curr] = number_format($new_rate, 4, '.', '');
            }
        }
        /**
        * Exchange the givven ammount from one currency to the other
        *
        * @param String $ammount The ammount to be exchanged
        * @param String $from Currency of the ammount (three letter code)
        * @param String $to Currency to witch we wish to exchange. Base currency if not specified.
        * @return Float - the exchanged ammount on success
		* @Throw exception if currency code is not in currency list
        * @access Public
        */
        public function exchange ($ammount, $from, $to = '') {
            if ($to == '') $to = $this->base_currency;
            if (!array_key_exists($from, $this->exchange_rates) || !array_key_exists($to, $this->exchange_rates)) {
                    $this->error = 'Unknown cerrency.';
					throw new Exception('Unknown cerrency.');
            }
            else {
                $converted = (float)$ammount * ((float)$this->exchange_rates[$to] / (float)$this->exchange_rates[$from]);
                return number_format($converted, 2, '.', '');
            }
        }

        /**
        * Gets the cross rate between two currencies
        *
        * @param String $from first Currency (three letter code)
        * @param String $to second Currency (three letter code)
        * @return Float - the cross rate on success
		* @Throw exception if currency code is not in currency list
        * @access Public
        */
        public function cross_rate ($from, $to) {
            if (!array_key_exists($from, $this->exchange_rates) || !array_key_exists($from, $this->exchange_rates)) {
                    $this->error = 'Unknown cerrency.';
					throw new Exception('Unknown cerrency.');
            }
            else {
                $converted = (float)$this->exchange_rates[$to] / (float)$this->exchange_rates[$from];
                return number_format($converted, 4, '.', '');
            }
        }

        /**
        * Gets the rates table based on Base currency
        *
        * @param Array $visible list of Currencies to be included in the table. All currencies by default.
        * @return String HTML formated table with exchange rates
		* @Can be styled with CSS selector .rates_table
        * @access Public
		* define("_TABLE_TITLE", "Reference rates of European Central Bank");
		* define("_BASE_CURRENCY_REFERENCE", "All rates are for 1 ");
		* define("_CURRENCY_STRING", "Currency");
		* define("_RATE_STRING", "Rate");
        */
        public function get_rates_table ($visible = array('all')) {
			define("_TABLE_TITLE", "Референтни курсове на Европейската Централна Банка");
			define("_BASE_CURRENCY_REFERENCE", "Всички курсове са за 1 ");
			define("_CURRENCY_STRING", "Валута");
			define("_RATE_STRING", "Курс");
            $this->rates_table = '<p align="center"><b>' . _TABLE_TITLE . '</b></p>';
            $this->rates_table .= '<p align="center">' . _BASE_CURRENCY_REFERENCE . $this->base_currency . '</p>';
            $this->rates_table .= '<table class="rates_table"><tr><th>' . _CURRENCY_STRING . '</th><th>' . _RATE_STRING . '</th></tr>';
            if ($visible[0] == 'all') {
                foreach ($this->exchange_rates as $curr => $rate) {
                    $this->rates_table .= ('<tr><td>' . $curr . '</td><td align="right">' . $rate . '</td></tr>');
                }
            } else {
                for ($i = 0; $i < sizeof($visible); $i++) {
                    if (array_key_exists($visible[$i], $this->exchange_rates))
                        $this->rates_table .= ('<tr><td>' . $visible[$i] . '</td><td align="right">' . 
                                $this->exchange_rates[$visible[$i]] . '</td></tr>');
                }
            }
            $this->rates_table .= '</table>';
            return $this->rates_table;
        }

        /**
        * Gets the list of currencies
        *
        * @return Array list of all available currencies 
        * @access Public
        */
        public function get_currency_list () {
            $list = array ();
            foreach ($this->exchange_rates as $curr => $rate) {
                array_push($list, $curr);
            }
            return $list;
        }

}
?>