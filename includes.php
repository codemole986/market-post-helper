<?php

require_once(dirname(__FILE__) . '/api.php');
require_once(dirname(__FILE__) . '/plugins/simple_html_dom.php');
require_once(dirname(__FILE__) . '/plugins/quandl/Quandl.php');

if ( !class_exists( 'Intrinio_Shortcode' ) ) {
    class Intrinio_Shortcode
    {
    	static $templates_path;
        static $debug = false;
        static $quandl_zacks = array(
            'ZEE' => array('Consensus Earnings Estimates', 'ZEE/{TICKER}_A'),
            'ZES' => array('Consensus Earnings Surprises', 'ZES/{TICKER}'),
            'ZEA' => array('Earnings Announcements', 'ZEA/{TICKER}'),
            'ZSE' => array('Sales Estimates', 'ZSE/{TICKER}_A'),
            'ZDIV' => array('Dividend Announcement and History', 'ZDIV/{TICKER}_H'),
            'ZAR' => array('Analyst Ratings and Target Prices', 'ZAR/{TICKER}'),
            'ZSS' => array('Sales Surprises', 'ZSS/{TICKER}'),
            'ZET' => array('Consensus Earnings Estimates Trends', 'ZET/{TICKER}_A'),
        );

        static $intrinio_item_list = 'Other Revenue:otherrevenue
Total Revenue:totalrevenue
Operating Cost of Revenue:operatingcostofrevenue
Other Cost of Revenue:othercostofrevenue
Total Cost of Revenue:totalcostofrevenue
Total Gross Profit:totalgrossprofit
Selling, General & Admin Expense:sgaexpense
Marketing Expense:marketingexpense
Research & Development Expense:rdexpense
Exploration Expense:explorationexpense
Depreciation Expense:depreciationexpense
Amortization Expense:amortizationexpense
Depletion Expense:depletionexpense
Other Operating Expenses / (Income):otheroperatingexpenses
Impairment Charge:impairmentexpense
Restructuring Charge:restructuringcharge
Other Special Charges / (Income):otherspecialcharges
Total Operating Expenses:totaloperatingexpenses
Total Operating Income:totaloperatingincome
Interest Expense:totalinterestexpense
Interest & Investment Income:totalinterestincome
Other Income / (Expense), net:otherincome
Total Other Income / (Expense), net:totalotherincome
Total Pre-Tax Income:totalpretaxincome';
        
        static $default = array(
            'intrinio_api_username'     => '',
            'intrinio_api_password'   => '',
            'intrinio_finviz_items' => array(),
            'intrinio_quandl_items' => array(),
            'intrinio_featured_path' => '',
            'intrinio_quandl_apikey' => '',
            'intrinio_item_list' => '',
            'intrinio_title_list' => '',
            'intrinio_stockta_rsi_blue_col' => '#008000',
            'intrinio_stockta_rsi_blue_text' => 'The current RSI is {VALUE} suggesting a bullish price action.',
            'intrinio_stockta_rsi_red_col' => '#ff0000',
            'intrinio_stockta_rsi_red_text' => 'The current RSI is {VALUE} suggesting a bearish price action.',
            'intrinio_stockta_ema_blue_col' => '#008000',
            'intrinio_stockta_ema_blue_text' => "Both the long term and short term EMA's of {VALUE1} and {VALUE2} are lower than the current trading price suggesting a bullish pattern in both short term and long term analysis.",
            'intrinio_stockta_ema_red_col' => '#ff0000',
            'intrinio_stockta_ema_red_text' => "The 5 day EMA {VALUE1} is lower than the last trade price suggesting a short term bullishh pattern building up. Long term, the 50 day EMA {VALUE2} is also higher than then current trading price depicting a bullush trend overall.",
            'intrinio_stockta_ema_blue_red_text' => "Currently, the 5 day EMA {VALUE1} is lower than the last trade price suggesting a short term bullish pattern building up. Long term, the 50 day EMA {VALUE2} is higher than current trading price depicting a bearish pattern.",
            'intrinio_stockta_ema_red_blue_text' => "Currently, the 5 day EMA {VALUE1} is higher than the last trade price suggesting a short term bearish pattern building up. Long term, the 50 day EMA {VALUE2} is lower than current trading price depicting a bullush signal.",
            'intrinio_disclaimer' => 'This is Default Discalimer. Please update this from the Intrinio Plugin setting page',
        );

        public static function get_market_watch_items() {
            $items = array(
                'name' => array('Company Name', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'ticker' => array('Ticker', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'sales_mrq' => array('Sales mrq', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'rev_grth' => array('Revenue Growth', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'grth_dec' => array('Grow/Decline', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'sales_grth' => array('Sales Growth', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'cost_gsold' => array('Cost of Goods Sold', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'gross_income' => array('Gross Income', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'total_do_shares' => array('Total Diluted Outstanding Shares', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'eps' => array('EPS', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/income/quarter'),
                'eps_forecast' => array('Next Quarter Analyst EPS Forecast', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/analystestimates'),
                'an_recommend' => array('Analyst Recommendation', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/analystestimates'),
                'num_an' => array('Number of analysts', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/analystestimates'),
                'avg_prc_tgt' => array('Average Price Target', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/analystestimates'),
                'next_fi_estm' => array('Next Fiscal Year Estimate', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/analystestimates'),
                'median_pe' => array('Median PE on Next FY Estimate', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/analystestimates'),
                'cash_mrq' => array('Cash mrq', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/balance-sheet/quarter'),
                'debt_mrq' => array('Debt mrq', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/balance-sheet/quarter'),
                'grth_fail' => array('Growing/Falling', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/balance-sheet/quarter'),
                'total_assets' => array('Total Assets', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/balance-sheet/quarter'),
                'total_liab' => array('Total Liabilities', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/balance-sheet/quarter'),
                'free_cash_flow' => array('Free Cash Flow', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/cash-flow/quarter'),
                'net_change_cash' => array('Net Change in Cash', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/cash-flow/quarter'),
                'net_op_cash' => array('Net Operating Cash Flow', 'http://www.marketwatch.com/investing/stock/{{TICKER}}/financials/cash-flow/quarter'),
            );
            return $items;
        }

        public static function get_barchart_items() {
            $items = array(
                'rsi-14' => array('RSI 14 day', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'stochastic-20' => array('Stochastic 20 day', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                
                'bull-bear' => array('Bullish/Bearish', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'pos-neg' => array('Positive/Negative', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'high-low' => array('Hight/Low', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),

                'strong-weak' => array('Strong/Weak', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'interest' => array('Interest/Lack of interest', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),

                '52week-high-low' => array('52week High/Low', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/cheat-sheet'),
                'Fib38%-high-low' => array('Fib 38% High/Low', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/cheat-sheet'),

                'support' => array('Support/Resistance', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'below-above' => array('Below/Above', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),

                '200day-ma' => array('200-day MA', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                '20day-change' => array('20-day $ Change', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'out-under' => array('Out/Under Performing', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'stock-s-52%' => array('Stock52% - S&P52%', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'more-less' => array('More/Less', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/comparison'),
                'hv-20d' => array('HV-20d', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
                'atr-ma-20d' => array('ATR20d/20dMA', 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis'),
            );
            return $items;
        }

    	public static function on_activate() {
            

    		self::$default['intrinio_item_list'] = self::$intrinio_item_list;
            self::$default['intrinio_featured_path'] = plugin_dir_path( __FILE__ ) . "featured_images";

            foreach (self::$default as $key => $value) {
                $opt = get_option($key);
                if (!$opt) {
                    update_option( $key, $value );
                }
            }
    	}

        public static function init() {
        	
        	self::$templates_path = plugin_dir_path( __FILE__ ) . "templates";

            foreach (self::$default as $opt => $def) {
                register_setting( 'intrinio-shortcode-group', $opt );
            }
            
        }
 
        public static function get_templates() {
        	$path = self::$templates_path;

        	if (!(file_exists($path) && is_dir($path))) {
        		mkdir($path, 0755, true);
        	}

        	$files = array();
        	foreach ( glob( $path . "/*.txt" ) as $file ) {
			    $files[] = array(self::make_good_name(basename($file)), $file);
			}

			return $files;
        }

        public static function unlink_files($files) {
        	if (!$files) return;
        	$path = self::$templates_path;
        	foreach($files AS $f) {
        		$file = $path . '/' . $f;
        		if (file_exists($file)) unlink($file);
        	}
        }

        public static function make_good_name($name) {
        	$name = str_replace(['-', '_'], ' ', $name);
        	$name = preg_replace('/\s+/', ' ',$name);

        	return ucfirst($name);
        }

        public static function render_mph_bulk_publish_page($reqData) {

            $titles = get_option('intrinio_title_list');
            $titles = explode("\n", str_replace("\r\n", "\n", $titles));

            $tpls = self::get_templates(); // array([NAME, PATH], [NAME, PATH], [NAME, PATH], ...)
            $msg_bulk_option = '';

            if (isset($reqData['action_type'])) {

                $data = Intrinio_Helper::make_data_safe($reqData);

                if (in_array($reqData['action_type'], array('update', 'preload'))) {
                    $tickers = $data['ticker_list'];
                    $tickers = explode("\n", str_replace("\r\n", "\n", $tickers));

                    $shouldSkip = false;
                    if ($data['bulk-publish-group'] != '') {
                        $key = $data['bulk-publish-group'];
                        $groups = get_option('mph_bulk_groups');
                        if (!$groups || !isset($groups[$key])) {
                            $shouldSkip = true;
                        } else {
                            $groupTitles = $groups[$key]['titles'];
                            $groupTpls = $groups[$key]['tpls']; 

                            $newTitles = array();
                            $newTpls = array();
                            for ($i=0; $i<count($titles); $i++) {
                                if (in_array($titles[$i], $groupTitles))    $newTitles[] = $titles[$i];
                            }
                            for ($i=0; $i<count($tpls); $i++) {
                                if (in_array(basename($tpls[$i][1]), $groupTpls))   $newTpls[] = $tpls[$i];
                            }

                            $titles = $newTitles;
                            $tpls = $newTpls;

                            if (count($tpls) < 1)   $shouldSkip = true;
                        }
                    }
                }

                if ($reqData['action_type'] == 'preload') {
                    
                    $preloadResult = array();

                    $pattern = get_shortcode_regex();
                    for ($i=0; $i<count($tickers); $i++) {
                        $ticker = $tickers[$i];
                        if ($ticker == '') continue;

                        $titleInd = rand(0, count($titles) - 1);
                        $tplInd = rand(0, count($tpls) - 1);

                        $tpl = Intrinio_Helper::make_string_safe(file_get_contents($tpls[$tplInd][1]));
                        $tpl = wpautop(str_replace(["\r\n","\n"], "\r\n\r\n", $tpl));

                        $cont = Intrinio_Helper::replace_ticker($tpl, $ticker);
                        $title = Intrinio_Helper::replace_ticker($titles[$titleInd], $ticker);


                        list($title, $content) = self::process_content($title, $cont, $pattern);

                        $title = do_shortcode($title);
                        $content = do_shortcode($content);

                        $preloadResult[] = array(
                            'i' => $ticker,
                            't' => $title,
                            'c' => $content,
                        );

                    }

                } else if ($reqData['action_type'] == 'preload_update') {

                    if (has_action('mph_remote_publish_preloaded_proceed')) {
                        $posts = array();
                        foreach ($data['preloadTitle'] as $ind => $value) {
                            $title = $value;
                            $cont = $data['preloadCont' . $ind];

                            $posts[] = array(
                                'post_title' => $title,
                                'post_content' => $cont,
                                'post_excerpt' => '',
                            );
                        }
                        do_action('mph_remote_publish_preloaded_proceed', $posts);
                    } else {

                        foreach ($data['preloadTitle'] as $ind => $value) {
                            $title = $value;
                            $cont = $data['preloadCont' . $ind];

                            $post = array(
                                'post_title' => $title,
                                'post_content' => $cont,
                                'post_status' => 'publish', // $post['post_status'],
                            );
                            wp_insert_post($post, true);
                        }
                    }

                } else if ($reqData['action_type'] == 'update') {

                    if (!$shouldSkip) {

                        if (has_action('mph_remote_publish_proceed')) {
                            do_action('mph_remote_publish_proceed', $tickers, $titles, $tpls);
                        } else {

                            for ($i=0; $i<count($tickers); $i++) {
                                $ticker = $tickers[$i];
                                if ($ticker == '') continue;

                                $titleInd = rand(0, count($titles) - 1);
                                $tplInd = rand(0, count($tpls) - 1);

                                $tpl = Intrinio_Helper::make_string_safe(file_get_contents($tpls[$tplInd][1]));
                                $tpl = wpautop(str_replace(["\r\n","\n"], "\r\n\r\n", $tpl));

                                $cont = Intrinio_Helper::replace_ticker($tpl, $ticker);
                                $title = Intrinio_Helper::replace_ticker($titles[$titleInd], $ticker);

                                $post = array(
                                    'post_title' => $title,
                                    'post_content' => $cont,
                                    'post_status' => 'publish', // $post['post_status'],
                                );

                                wp_insert_post($post, true);
                            }
                        }

                        $msg_bulk_option = 'Bulk posts made with following titles and templates:';
                        $msg_bulk_option .= "\n" . 'Titles: ' . "\n" . implode("\n", $titles);
                        $msg_bulk_option .= "\n" . 'Templates: ';
                        foreach ($tpls as $tpl) {
                            $msg_bulk_option .= "\n" . $tpl[0];
                        }
                    }
                }
                
            }

            include (dirname(__FILE__) . '/view_bulk_publish.tpl');
        }

        public static function render_settings_page() {
            // Update options
            if (isset($_REQUEST['action_type']) && ($_REQUEST['action_type'] == 'update')) {
                
                foreach (self::$default as $opt => $def) {
                    if (isset($_REQUEST[$opt])) {
                        update_option($opt, Intrinio_Helper::make_data_safe($_REQUEST[$opt]));
                    }
                }
            }
            
            // Bulk publish option
            if (isset($_REQUEST['action_type']) && ($_REQUEST['action_type'] == 'bulk')) {
                $data = Intrinio_Helper::make_data_safe($_REQUEST);

                $groups = get_option('mph_bulk_groups');
                if (!$groups)   $groups = array();

                if ($data['action_code'] == 'save') {
                    
                    $grp = array(
                        'label' => $data['bulk_group_label'],
                        'titles' => isset($data['bulk_titles'])?$data['bulk_titles']:array(),
                        'tpls' => isset($data['bulk_tpls'])?$data['bulk_tpls']:array(),
                    );
                    $key = $data['action_id'];
                    if ($key == '') $key = uniqid();
                    
                    $groups[$key] = $grp;
                    
                } else if ($data['action_code'] == 'delete') {
                    $key = $data['action_id'];
                    if(isset($groups[$key]))    unset($groups[$key]);
                }

                update_option('mph_bulk_groups', $groups);
            }


        	// Delete template files
            if (isset($_REQUEST['action_type']) && ($_REQUEST['action_type'] == 'template')) {

				self::unlink_files($_POST['delete_files']);

                // Upload template files
                $path = self::$templates_path;

                if (!(file_exists($path) && is_dir($path))) {
                    mkdir($path, 0755, true);
                }

                $isUploadOk = true;
                $target_file = Intrinio_Helper::find_valid_filename($path, basename($_FILES["template_file"]["name"]));
                

                $file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
                $file_size = filesize($_FILES["template_file"]["tmp_name"]);
                if ($file_size === false) {
                    $isUploadOk = false;
                }
                if ($file_type != "txt") {
                    $errors[] = "Only txt files are allowed to upload.";
                    $isUploadOk = false;    
                }
                if ($isUploadOk) {
                    if (move_uploaded_file($_FILES["template_file"]["tmp_name"], $path . '/' . $target_file)) {
                        // Good upload works
                    } else {
                        $errors[] = "Sorry, there was an error uploading your file.";
                        $isUploadOk = false;
                    }
                }

			}

			
			// Load template files
			$files = self::get_templates(); 
			include (dirname(__FILE__) . '/view_settings.tpl');
		}

        /** 
        *
        * Parse value from Market Watch
        *
        */
        public static function parse_market_watch_value($item, $html) {
            $parsed = 'N/A';
            try {

                if ($item == 'name') {
                    $parsed = strip_tags($html->find('#instrumentname', 0)->innertext);
                } else if ($item == 'ticker') {
                    $parsed = strip_tags($html->find('#instrumentticker', 0)->innertext);
                } else if ($item == 'sales_mrq') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $html->find('.partialSum', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'rev_grth') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $html->find('.partialSum', 0);

                    $b = doubleval(strip_tags($row->find('td', -2)->innertext));
                    $a = doubleval(strip_tags($row->find('td', 1)->innertext));
                    $parsed = round(($b-$a)/$b, 2) . '%';
                } else if ($item == 'grth_dec') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $html->find('.partialSum', 0);

                    $b = doubleval(strip_tags($row->find('td', -2)->innertext));
                    $a = doubleval(strip_tags($row->find('td', -3)->innertext));
                    $parsed = ($b > $a)?"grow":"decline";
                } else if ($item == 'sales_grth') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $html->find('.partialSum', 0);

                    $b = doubleval(strip_tags($row->find('td', -2)->innertext));
                    $a = doubleval(strip_tags($row->find('td', -3)->innertext));
                    $parsed = round(($b-$a)/$b, 2) . '%';
                } else if ($item == 'cost_gsold') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $html->find('.mainRow', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'gross_income') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $html->find('.partialSum', 1);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'total_do_shares') {
                    $row = $html->find('.mainRow', -1);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'eps') {
                    $row = $html->find('.mainRow', -2);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'eps_forecast') {
                    $row = $html->find('.estimates tr', 2);
                    $parsed = strip_tags($row->find('td', 2)->innertext);
                } else if ($item == 'an_recommend') {
                    $td = $html->find('.snapshot tr .recommendation', 0);
                    $parsed = strip_tags($td->innertext);
                } else if ($item == 'num_an') {
                    $row = $html->find('.snapshot tr', 1);
                    $td = $row->find('td', 1);
                    $parsed = strip_tags($td->innertext);
                } else if ($item == 'avg_prc_tgt') {
                    $row = $html->find('.snapshot tr', 0);
                    $td = $row->find('td', -1);
                    $parsed = strip_tags($td->innertext);
                } else if ($item == 'next_fi_estm') {
                    $row = $html->find('.snapshot tr', -2);
                    $td = $row->find('td', -1);
                    $parsed = strip_tags($td->innertext);
                } else if ($item == 'median_pe') {
                    $row = $html->find('.snapshot tr', -1);
                    $td = $row->find('td', -1);
                    $parsed = strip_tags($td->innertext);
                } else if ($item == 'cash_mrq') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $table->find('.rowLevel-2', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'debt_mrq') {
                    $table = $html->find('.crDataTable', 2);
                    $row = $table->find('.mainRow', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);
                } else if ($item == 'grth_fail') {
                    $table = $html->find('.crDataTable', 2);
                    $row = $table->find('.partialSum', 0);

                    $b = doubleval(strip_tags($row->find('td', -2)->innertext));
                    $a = doubleval(strip_tags($row->find('td', -3)->innertext));
                    $parsed = ($b > $a)?"growing":"falling";
                } else if ($item == 'total_assets') {
                    $table = $html->find('.crDataTable', 1);
                    $row = $table->find('.totalRow', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);                
                } else if ($item == 'total_liab') {
                    $table = $html->find('.crDataTable', 2);
                    $row = $table->find('.totalRow', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);                
                } else if ($item == 'free_cash_flow') {
                    $row = $html->find('.mainRow', -1);
                    $parsed = strip_tags($row->find('td', -2)->innertext);                
                } else if ($item == 'net_change_cash') {
                    $row = $html->find('.mainRow', -2);
                    $parsed = strip_tags($row->find('td', -2)->innertext);                
                } else if ($item == 'net_op_cash') {
                    $table = $html->find('.crDataTable', 0);
                    $row = $table->find('.totalRow', 0);
                    $parsed = strip_tags($row->find('td', -2)->innertext);                
                }
            } catch (Exception $e) {
                $parsed = 'N/A';
            }

            return trim($parsed);
        }

        /** 
        *
        * Parse value from Market Watch
        *
        */
        public static function parse_barchart_value($item, $html, $table, $flag = 0) {

            $parsed = 'N/A';
            try {
                if ($item == 'rsi-14') {
                    $k1 = '14-Day';
                    $k2 = 'Relative Strength';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $parsed = $table[$k1][$k2];
                } else if ($item == 'stochastic-20') {
                    $k1 = '20-Day';
                    $k2 = 'Stochastic %K';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $parsed = $table[$k1][$k2];
                } else if ($item == 'hv-20d') {
                    $k1 = '20-Day';
                    $k2 = 'Historic Volatility';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $parsed = $table[$k1][$k2];
                } else if ($item == 'pcn-100d') {
                    $k1 = '100-Day';
                    $k2 = 'Percent Change';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $parsed = doubleval($table[$k1][$k2]);
                } else if (in_array($item, array('bull-bear','pos-neg','high-low'))) {
                    $labels = array(
                        'bull-bear' => array('bullish', 'bearish'),
                        'pos-neg' => array('positive', 'negative'),
                        'high-low' => array('high', 'low'),
                    );

                    $v1 = 0; $v2=0;

                    $k1 = '50-Day';
                    $k2 = 'Moving Average';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v1 = doubleval($table[$k1][$k2]);

                    $k1 = '200-Day';
                    $k2 = 'Moving Average';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v2 = doubleval($table[$k1][$k2]);

                    if($v1 > $v2) {
                        $parsed = $labels[$item][0];
                    } else {
                        $parsed = $labels[$item][1];
                    }

                } else if (in_array($item, array('strong-weak','interest'))) {
                    $labels = array(
                        'strong-weak' => array('strong', 'weak'),
                        'interest' => array('interest', 'lack of interest'),
                    );

                    $v1 = 0; $v2=0;

                    $k1 = '20-Day';
                    $k2 = 'Average Volume';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v1 = doubleval($table[$k1][$k2]);

                    $k1 = '100-Day';
                    $k2 = 'Average Volume';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v2 = doubleval($table[$k1][$k2]);

                    if($v1 > $v2) {
                        $parsed = $labels[$item][0];
                    } else {
                        $parsed = $labels[$item][1];
                    }

                } else if (in_array($item, array('support','below-above'))) {
                    $labels = array(
                        'support' => array('support', 'resistance'),
                        'below-above' => array('below', 'above'),
                    );

                    $v1 = doubleval($html->find('.last-change', 0));
                    $v2=0;

                    $k1 = '200-Day';
                    $k2 = 'Moving Average';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v2 = doubleval($table[$k1][$k2]);

                    if($v1 > $v2) {
                        $parsed = $labels[$item][0];
                    } else {
                        $parsed = $labels[$item][1];
                    }
                } else if ($item == '200day-ma') {
                    $k1 = '200-Day';
                    $k2 = 'Moving Average';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $parsed = '$' . $table[$k1][$k2];
                } else if ($item == '20day-change') {
                    $k1 = '20-Day';
                    $k2 = 'Price Change';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $parsed = $table[$k1][$k2];
                } else if (in_array($item, array('more-less'))) {
                    $labels = array(
                        'more-less' => array('more', 'less'),
                    );

                    $v1 = 0;
                    $k1 = '36-Month Beta';
                    $k2 = '1';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v1 = doubleval($table[$k1][$k2]);

                    $v2 = 1;

                    if($v1 > $v2) {
                        $parsed = $labels[$item][0];
                    } else {
                        $parsed = $labels[$item][1];
                    }
                } else if ($item == 'atr-ma-20d') {

                    $v1 = 1; $v2 = 1;

                    $k1 = '20-Day';
                    $k2 = 'Moving Average';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v1 = doubleval($table[$k1][$k2]);

                    $k1 = '20-Day';
                    $k2 = 'Average True Range';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v2 = doubleval($table[$k1][$k2]);

                    $parsed = round($v2 * 100 / $v1, 2);

                } else if ($item == '52week-high-low') {

                    $v = '52-Week Low';
                    if ($flag == 'high') {
                        $v = '52-Week High';
                    }

                    foreach ($table as $row) {
                        if ($row['labelSupportResistance'] == $v) {
                            $parsed = '$' . $row['value'];
                            break;
                        }
                    }


                } else if ($item == 'Fib38%-high-low') {

                    $v = '38.2% Retracement From 52 Week Low';
                    if ($flag == 'high') {
                        $v = '38.2% Retracement From 52 Week High';
                    }

                    foreach ($table as $row) {
                        if ($row['labelTurningPoints'] == $v) {
                            $parsed = '$' . $row['value'];
                            break;
                        }
                    }

                } else if ($item == 'out-under') {

                    $v1 = 0;
                    $v2 = doubleval($flag);

                    $k1 = '100-Day';
                    $k2 = 'Percent Change';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v1 = doubleval($table[$k1][$k2]);
                    if ($v1 > $v2) {
                        $parsed = 'outperforming';
                    } else {
                        $parsed = 'underperforming';
                    }
                } else if ($item == 'stock-s-52%') {

                    $v1 = 0;
                    $v2 = doubleval($flag);

                    $k1 = '100-Day';
                    $k2 = 'Percent Change';
                    if (isset($table[$k1]) && isset($table[$k1][$k2])) $v1 = doubleval($table[$k1][$k2]);
                    
                    $parsed = abs($v1 - $v2);
                }


            } catch (Exception $e) {
                $parsed = 'N/A';
            }

            return trim($parsed);
        }


        /** 
        *
        * Process shortcode
        *
        */
        public static function process_content($title, $content, $pattern) {

            $sepa = '<|-|>';

            $intr_reqs = array();
            $intr_TICKERs = array();
            $intr_result = array();

            $finviz_TICKERs = array();
            $finviz_result = array();

            $quandl_TICKERs = array();
            $quandl_result = array();

            $market_TICKERs = array();
            $market_result = array();

            $barchart_TICKERs = array();
            $barchart_result = array();

            $path = plugin_dir_path( __FILE__ ) . "tmp";
            if (!(file_exists($path) && is_dir($path))) {
                mkdir($path, 0755, true);
            }

            $my_codes = array('intr_code', 'finviz_code', 'quandl_code', 'intr_chart', 'intr_bto', 'intr_summary', 'rsi_code', 'ema_code', 'market_watch', 'barchart');
            $allowed_attributes = array('ticker', 'item', 'value', 'src', 'content', 'color');

            $full_content = $title . '<br>' . $content;

            if (   preg_match_all( '/'. $pattern .'/s', $full_content, $matches )
                && array_key_exists( 2, $matches )
               )
            {
                $marketwatch_items = self::get_market_watch_items();
                $barchart_items = self::get_barchart_items();

                /////////////////////////////////////////////////////////////////////////
                // Extract shortcode set
                //
                $matched = $matches[0];
                for ($ind=0; $ind < count($matched); $ind++) {

                    $my_code = $matches[2][$ind];
                    $m = $matched[$ind];

                    if (!in_array($my_code, $my_codes)) continue;
                    
                    $params = explode(" ", substr($m, strlen($my_code) + 2, strlen($m)-strlen($my_code)-3));
                    $m_obj = array();
                    foreach ($params as $p) {
                        $inp = explode("=", $p);
                        $m_obj[$inp[0]] = isset($inp[1])?trim($inp[1]):"";
                    }

                    if ($m_obj['ticker'] == '') continue;
                    if (($my_codes == 'intr_code') && ($m_obj['item'] == ''))   continue;

                    $intr_reqs[] = array(
                        'code' => $my_code,
                        'origin' => $m,
                        'obj' => $m_obj,
                    );

                    if ($my_code == 'intr_code') {
                        if (!isset($intr_TICKERs[$m_obj['ticker']]))    $intr_TICKERs[$m_obj['ticker']] = array();
                        if (!in_array($m_obj['item'], $intr_TICKERs[$m_obj['ticker']])) {
                            $intr_TICKERs[$m_obj['ticker']][] = $m_obj['item']; 
                        }
                    } else if ($my_code == 'finviz_code') {
                        if (!isset($finviz_TICKERs[$m_obj['ticker']]))  $finviz_TICKERs[$m_obj['ticker']] = array();
                        if (!in_array($m_obj['item'], $finviz_TICKERs[$m_obj['ticker']])) {
                            $finviz_TICKERs[$m_obj['ticker']][] = $m_obj['item'];     
                        }
                    } else if ($my_code == 'quandl_code') {
                        if (!isset($quandl_TICKERs[$m_obj['ticker']]))  $quandl_TICKERs[$m_obj['ticker']] = array();
                        if (!in_array($m_obj['item'], $quandl_TICKERs[$m_obj['ticker']])) {
                            $quandl_TICKERs[$m_obj['ticker']][] = $m_obj['item']; 
                        }
                    } else if ($my_code == 'market_watch') {
                        if (isset($marketwatch_items[$m_obj['item']])) {
                            if (!isset($market_TICKERs[$m_obj['ticker']]))  $market_TICKERs[$m_obj['ticker']] = array();
                            $sub_key = $marketwatch_items[$m_obj['item']][1];
                            if (!isset($market_TICKERs[$m_obj['ticker']][$sub_key]))  $market_TICKERs[$m_obj['ticker']][$sub_key] = array();
                            if (!in_array($m_obj['item'], $market_TICKERs[$m_obj['ticker']][$sub_key])) {
                                $market_TICKERs[$m_obj['ticker']][$sub_key][] = $m_obj['item']; 
                            }
                        }
                    } else if ($my_code == 'barchart') {
                        if (isset($barchart_items[$m_obj['item']])) {
                            if (!isset($barchart_TICKERs[$m_obj['ticker']]))  $barchart_TICKERs[$m_obj['ticker']] = array();
                            $sub_key = $barchart_items[$m_obj['item']][1];
                            if (!isset($barchart_TICKERs[$m_obj['ticker']][$sub_key]))  $barchart_TICKERs[$m_obj['ticker']][$sub_key] = array();
                            if (!in_array($m_obj['item'], $barchart_TICKERs[$m_obj['ticker']][$sub_key])) {
                                $barchart_TICKERs[$m_obj['ticker']][$sub_key][] = $m_obj['item']; 
                            }
                        }
                    }
                }

                /////////////////////////////////////////////////////////////////////////////
                // Load Data via API
                //
                $config = array(
                    'user' => get_option('intrinio_api_username'),
                    'password' => get_option('intrinio_api_password'),
                );
                $api = new Intrinio_API($config);

                foreach ($intr_TICKERs as $t => $i) {
                    $results = $api->call('data_point?identifier=' . $t . '&item=' . implode(',', $i));

                    $filtered = array();
                    if (isset($results['data'])) {
                        $filtered = $results['data'];
                    }  else {
                        $filtered = [$results];
                    }
                    
                    foreach ($filtered as $data) {
                        $key = $data['identifier'] . $sepa . $data['item'];
                        $intr_result[$key] = $data['value'];
                    }   
                }

                /////////////////////////////////////////////////////////////////////////////
                // Scrap from Finviz
                //
                ob_start();
                foreach ($finviz_TICKERs as $t => $i) {
                    $url = 'http://finviz.com/screener.ashx?v=152&t={{TICKER}}&c=';
                    $url = str_replace(['{{TICKER}}'], $t, $url);

                    $i[] = 0;
                    sort($i);
                    $url .= implode(',', $i);

                    $html = file_get_html($url);

                    if ($html) {
                        $ind = 0;
                        foreach($html->find('td.screener-body-table-nw') as $td) {
                            $key = $t . $sepa . $i[$ind];
                            $finviz_result[$key] = strip_tags($td->innertext);
                            $ind++;
                        }
                    }
                }
                ob_end_clean();

                /////////////////////////////////////////////////////////////////////////////
                // Scrap from Market Watch
                //

                ob_start();
                foreach ($market_TICKERs as $t => $iset) {
                    foreach ($iset as $urlkey => $i) {
                        $url = str_replace(['{{TICKER}}'], $t, $urlkey);    
                        $html = file_get_html($url);
                        if ($html) {
                            foreach ($i as $item) {
                                $key = $t . $sepa . $item;
                                $market_result[$key] = self::parse_market_watch_value($item, $html);
                            }    
                        }
                    }
                }
                ob_end_clean();

                /////////////////////////////////////////////////////////////////////////////
                // Scrap from Barchart
                //
                ob_start();
                $caches = array();
                $checkLater = array('52week-high-low', 'Fib38%-high-low', 'out-under', 'stock-s-52%');
                $checkLaterDetails = array();

                foreach ($barchart_TICKERs as $t => $iset) {
                    foreach ($iset as $urlkey => $i) {
                        $url = str_replace(['{{TICKER}}'], $t, $urlkey);    
                        $html = file_get_html($url);

                        $table = array();
                        if ($html) {
                            if (strpos($url, 'cheat-sheet') !== false) {
                                $table = json_decode($html->find('cheat-sheet', 0)->attr['data-cheat-sheet-data'], true);
                            } else {
                                $table = Intrinio_Helper::parseHtmlTable($html);    
                            }
                            
                            $caches[$url] = array($html, $table);

                            foreach ($i as $item) {
                                if (in_array($item, $checkLater)) {
                                    $checkLaterDetails[] = array($t, $item, $url);  
                                    continue;
                                }
                                $key = $t . $sepa . $item;
                                $barchart_result[$key] = self::parse_barchart_value($item, $html, $table);
                            }    
                        }
                    }
                }

                foreach ($checkLaterDetails as $d) {
                    $t = $d[0];
                    $item = $d[1];
                    $url = $d[2];

                    $key = $t . $sepa . $item;
                    if (isset($barchart_result[$key])) continue;

                    if (in_array($item, array('52week-high-low', 'Fib38%-high-low'))) {

                        $moreUrl = str_replace('{{TICKER}}', $t, 'https://www.barchart.com/stocks/quotes/{{TICKER}}/technical-analysis');
                        if (!isset($caches[$moreUrl])) {
                            $html = file_get_html($moreUrl);
                            $table = array();
                            if ($html) {
                                $table = Intrinio_Helper::parseHtmlTable($html);
                                $caches[$moreUrl] = array($html, $table);
                            }
                        } else {
                            $html = $caches[$moreUrl][0];
                            $table = $caches[$moreUrl][1];
                        }

                        $sub = self::parse_barchart_value('high-low', $html, $table);

                        $html = $caches[$url][0];
                        $table = $caches[$url][1];
                        $barchart_result[$key] = self::parse_barchart_value($item, $html, $table, $sub);
                    } else if (in_array($item, array('out-under', 'stock-s-52%'))) {

                        $moreUrl = 'https://www.barchart.com/stocks/quotes/SPY/technical-analysis';
                        if (!isset($caches[$moreUrl])) {
                            $html = file_get_html($moreUrl);
                            $table = array();
                            if ($html) {
                                $table = Intrinio_Helper::parseHtmlTable($html);
                                $caches[$moreUrl] = array($html, $table);
                            }
                        } else {
                            $html = $caches[$moreUrl][0];
                            $table = $caches[$moreUrl][1];
                        }

                        $sub = self::parse_barchart_value('pcn-100d', $html, $table);

                        $html = $caches[$url][0];
                        $table = $caches[$url][1];
                        $barchart_result[$key] = self::parse_barchart_value($item, $html, $table, $sub);
                    }
                }

                ob_end_clean();

                /*
                print_r($finviz_TICKERs);
                echo $url;
                print_r($finviz_result);
                exit;
                */
                

                /////////////////////////////////////////////////////////////////////////////
                // Scrap from Qunadl API
                //
                ob_start();
                $api_key = get_option('intrinio_quandl_apikey');
                if ($api_key) {

                }

                $quandl = new Quandl($api_key);
                $quandl->format = "json";
                foreach ($quandl_TICKERs as $t => $is) {
                    foreach ($is as $i) {
                        $inkey = explode('|', $i);
                        $data = $quandl->getSymbol(str_replace('{TICKER}', $t, self::$quandl_zacks[$inkey[0]][1]));
                        $data = json_decode($data, true);

                        $data_filtered = array();
                        if ($data['dataset']) {
                            $ind = 0;
                            $infoset = $data['dataset']['data'][0];
                            foreach ($data['dataset']['column_names'] as $key) {
                                $data_filtered[$key] = $infoset[$ind];
                                if (!(strpos(strtolower($key), 'date') === false)) {
                                    ///////////////////////////////////////////////////////////////////////
                                    // Let's check if date value is in good format Y-m-d
                                    //
                                    if (!(strtotime($infoset[$ind]) === false)) {
                                        $data_filtered[$key] = date("Y-m-d", strtotime($infoset[$ind]));
                                    }
                                    //
                                }
                                $ind++;
                            }

                        }
                        $key = $t . $sepa . $inkey[0];
                        $quandl_result[$key] = $data_filtered;
                    }
                }
                ob_end_clean();

                /////////////////////////////////////////////////////////////////////////////
                // Update the short code with the values
                //

                $rsi_blue_color = strtolower(get_option('intrinio_stockta_rsi_blue_col'));
                $rsi_red_color = strtolower(get_option('intrinio_stockta_rsi_red_col'));
                $ema_blue_color = strtolower(get_option('intrinio_stockta_ema_blue_col'));
                $ema_red_color = strtolower(get_option('intrinio_stockta_ema_red_col'));

                $already_done = array();
                foreach ($intr_reqs as $intr) {
                    $my_code = $intr['code'];
                    $m = $intr['origin'];
                    $m_obj = $intr['obj'];

                    if (in_array($m, $already_done)) continue;
                    $already_done[] = $m;

                    if ($my_code == 'intr_code') {
                        
                        $key = $m_obj['ticker'] . $sepa . $m_obj['item'];
                        if (isset($intr_result[$key])) { // Only if info is fetched, then update    
                            $m_obj['value'] = $intr_result[$key];
                        }

                    } else if ($my_code == 'finviz_code') {
                        
                        $key = $m_obj['ticker'] . $sepa . $m_obj['item'];
                        if (isset($finviz_result[$key])) { // Only if info is fetched, then update    
                            $m_obj['value'] = '"' . $finviz_result[$key] . '"';
                        }
                    } else if ($my_code == 'market_watch') {
                        
                        $key = $m_obj['ticker'] . $sepa . $m_obj['item'];
                        if (isset($market_result[$key])) { // Only if info is fetched, then update    
                            $m_obj['value'] = '"' . $market_result[$key] . '"';
                        }
                    } else if ($my_code == 'barchart') {
                        
                        $key = $m_obj['ticker'] . $sepa . $m_obj['item'];
                        if (isset($barchart_result[$key])) { // Only if info is fetched, then update    
                            $m_obj['value'] = '"' . $barchart_result[$key] . '"';
                        }
                    } else if ($my_code == 'quandl_code') {
                        $inkey = explode('|', $m_obj['item']);
                        $key = $m_obj['ticker'] . $sepa . $inkey[0];
                        if (isset($quandl_result[$key])) { // Only if info is fetched, then update    
                            if ($inkey[1] == 'all') {
                                // TO DO
                            } else {
                                $m_obj['value'] = '"' . $quandl_result[$key][$inkey[1]] . '"';
                            }
                        }
                    } else if ($my_code == 'intr_chart') {
                        
                        $url = 'http://stockcharts.com/c-sc/sc?s={{TICKER}}&p=D&b=5&g=0&i=0&r=1485176794965';
                        $url = str_replace(['{{TICKER}}'], $m_obj['ticker'], $url);
                        $target_file = Intrinio_Helper::find_valid_filename($path, $m_obj['ticker'] . '.png');

                        $img_cont = file_get_contents($url);
                        file_put_contents($path . '/' . $target_file, $img_cont);

                        if (file_exists($path . '/' . $target_file)) {
                            $s_id = Intrinio_Helper::insert_attachment($post->ID, $path . '/' . $target_file);
                            $m_obj['src'] = '"' . wp_get_attachment_url($s_id) . '"';
                            unlink($path . '/' . $target_file);
                        }

                    } else if ($my_code == 'intr_bto') {

                        $extracted = self::get_from_barchart($m_obj['ticker'], 'bto');
                        if ($extracted != '') {
                            $m_obj['content'] = '"' . base64_encode($extracted) . '"';
                        }
                    } else if ($my_code == 'intr_summary') {

                        $extracted = self::get_from_barchart($m_obj['ticker'], 'summary');
                        if ($extracted != '') {
                            $m_obj['content'] = '"' . base64_encode($extracted) . '"';
                        }
                    } else if ($my_code == 'rsi_code') {

                        ob_start();
                        $url = 'http://www.stockta.com/cgi-bin/analysis.pl?symb={{TICKER}}&table=rsi&mode=table';
                        $url = str_replace(['{{TICKER}}'], $m_obj['ticker'], $url);
                        $html = file_get_html($url);

                        if ($html) {
                            $ind = 0;
                            foreach($html->find('.borderTd font') as $td) {
                                $m_obj['color'] = 'unknown';
                                $m_obj['value'] = strip_tags($td->innertext);
                                if ($rsi_blue_color == strtolower($td->color)) {
                                    $m_obj['color'] = 'blue';
                                } else if ($rsi_red_color == strtolower($td->color)) {
                                    $m_obj['color'] = 'red';
                                }

                                break;
                            }
                        }
                        ob_end_clean();

                    } else if ($my_code == 'ema_code') {

                        ob_start();
                        $url = 'http://www.stockta.com/cgi-bin/analysis.pl?symb={{TICKER}}&table=ema&mode=table';
                        $url = str_replace(['{{TICKER}}'], $m_obj['ticker'], $url);
                        $html = file_get_html($url);

                        if ($html) {
                            $ind = 0;

                            
                            foreach($html->find('.borderTd font') as $td) {
                                if ($ind == 0) {
                                    $m_obj['color'] = 'unknown';
                                    if ($ema_blue_color == strtolower($td->color)) {
                                        $m_obj['color'] = 'blue';
                                    } else if ($ema_red_color == strtolower($td->color)) {
                                        $m_obj['color'] = 'red';
                                    }
                                    $m_obj['value'] = strip_tags($td->innertext);    
                                } else if ($ind == 3) {
                                    $new_col = 'unknown';
                                    if ($ema_blue_color == strtolower($td->color)) {
                                        $new_col = 'blue';
                                    } else if ($ema_red_color == strtolower($td->color)) {
                                        $new_col = 'red';
                                    }
                                    if ($m_obj['color'] != $new_col) $m_obj['color'] = $m_obj['color'] . '_' . $new_col;
                                    $m_obj['value'] .= '|' . strip_tags($td->innertext);    
                                }

                                $ind ++;
                            }
                        }
                        ob_end_clean();

                    }

                    $new_intr = "[" . $my_code;
                    foreach ($m_obj as $key => $value) {
                        if (in_array($key, $allowed_attributes)) {
                            $new_intr .= " " . $key . "=" . $value;
                        }
                    }
                    $new_intr .= "]";

                    // $new_intr = $extracted;
                    $content = str_replace($m, $new_intr, $content);
                    $title = str_replace($m, $new_intr, $title);
                }
            }

            return [$title, $content];
        }

		/** 
		*
		* Process shortcode
		*
		*/
		public static function process_intr_shortcode($post, $pattern) {

            /////////////////////////////////////////////////////////////////////////////////
            // Process Content
            //			
            list($post->post_title, $post->post_content) = self::process_content($post->post_title, $post->post_content, $pattern);


            /////////////////////////////////////////////////////////////////////////////////
            // Set post tags
            //
            $tags = array();
            $tags_prev = wp_get_post_tags( $post->ID );

            foreach ($tags_prev as $t) {
                $t->name = Intrinio_Helper::replace_comma_tag($t->name);
                $tags[] = $t->name;
                wp_update_term($t->term_id, 'post_tag', array(
                  'name' => $t->name
                ));
                // wp_remove_object_terms( $post->ID, $t->slug, 'post_tag' );
            }
            

            $new_tags = array();
            preg_match_all('/>(.*?)\((.*?):(.*?)\)</', $post->post_content, $matches );
            foreach ($matches[0] as $match) {
                $match = substr($match, 1, strlen($match)-2);
                $match = strip_tags($match);

                $new_tags[] = trim($match);

                $match = explode(':', $match);
                $k1 = explode('(', $match[0]);
                $new_tags[] = trim($k1[count($k1)-1]);

                $k2 = explode(')', $match[1]);
                $new_tags[] = trim($k2[0]);
            }

            for ($i=0; $i<count($new_tags); $i++) {
                $new_tags[$i] = Intrinio_Helper::replace_comma_tag($new_tags[$i]);
            }

            $tags = array_unique(array_merge($tags, $new_tags));

            wp_set_post_tags($post->ID, $tags);
            //

            //////////////////////////////////////////////////////////////////////////////////
            // Save post meta for disclaimer
            //
            if(isset($_POST['intrinio_post_disclaimer'])) {
                update_post_meta($post->ID, 'intrinio_post_disclaimer', $_POST['intrinio_post_disclaimer']);
            }

            //////////////////////////////////////////////////////////////////////////////////
            // Set post author randomly
            //
            $is_update = true;
            $termid = get_post_meta($post->ID, '_intrinio_is_created', true);
            if ($termid == '') {
                $is_update = false;
                $termid = 'updated';
            }
            update_post_meta($post->ID, '_intrinio_is_created', $termid);
            if (!$is_update) { // let's set author only first time
                $users = get_users(['role__not_in' => ['subscriber']]);
                if(count($users) > 0) {
                    $random_author = rand(0, count($users)-1);    
                    if ($users[$random_author]) {
                        $post->post_author = $users[$random_author]->ID;
                    }
                }
            }

            /////////////////////////////////////////////////////////////////////////////////
            // Set featured image
            //
            $featured = get_the_post_thumbnail($post->ID);
            if (!$is_update && ($featured == '')) {
                $featured_path = get_option('intrinio_featured_path');
                $files = array();
                foreach ( glob( $featured_path . "/*.*" ) as $file ) {
                    if(file_exists($file) && !is_dir($file))    $files[] = $file;
                }
                if (count($files) > 0) {
                    $ind = rand(0, count($files)-1);    
                    if ($files[$ind]) {
                        $attach_id = Intrinio_Helper::insert_attachment($post->ID, $files[$ind]);
                        set_post_thumbnail( $post->ID, $attach_id ); 
                    }
                }
            }
            //
            
            
		    return $post;
		}

		public static function get_from_barchart($ticker, $type) {

			$extracted = '';

			if ($type == 'bto') {

				$url = 'https://www.barchart.com/stocks/quotes/{{TICKER}}';
				$url = str_replace(['{{TICKER}}'], $ticker, $url);

                if (self::$debug) $url = 'http://192.168.0.41/barchart-quote.php';

				$html_cont = file_get_contents($url);

				$search_for = '<div class="technical-opinion-description">';
				$pos = strpos($html_cont, $search_for);

				$my_pos = $pos;
				if ($pos === false) {
					$extracted = '';
				} else {
					
					$pos = $pos + strlen($search_for);
					$my_pos = $pos;

					$next_pos = strpos($html_cont, '<p class="see-more-button">', $pos + 1);
					$extracted = substr($html_cont, $my_pos, $next_pos-$pos);

					// Strip html tags
					$extracted = str_replace(["\r\n", "\n"], " ", strip_tags($extracted));
				}

			} else if ($type == 'summary') {

				$url = 'https://www.barchart.com/stocks/quotes/{{TICKER}}/profile';
				$url = str_replace(['{{TICKER}}'], $ticker, $url);

                if (self::$debug) $url = 'http://192.168.0.41/barchart-summary.php';

				$html_cont = file_get_contents($url);

				// $search_for = '<div class="business-description">';
				$search_for = '<div class="text-block description">';
				$pos = strpos($html_cont, $search_for);

				$my_pos = $pos;
				if ($pos === false) {
					$extracted = '';
				} else {
					
					$pos = $pos + strlen($search_for);
					$my_pos = $pos;

					$next_pos = strpos($html_cont, '</div>', $pos + 1);
					$extracted = substr($html_cont, $my_pos, $next_pos-$pos);

					$extracted = str_replace('<h4>Description:</h4>', '', $extracted);
					$extracted = str_replace(["\r\n", "\n"], " ", strip_tags($extracted));
				}

            } else if ($type == 'news') {
                $url = 'https://www.barchart.com/stocks/quotes/{{TICKER}}/news';
                $url = str_replace(['{{TICKER}}'], $ticker, $url);

                if (self::$debug) $url = 'http://192.168.0.41/barchart-news.php';

                $html_cont = file_get_contents($url);

                $limit = 5;
                $count = 0;
                $ids = array();
                preg_match_all('/<a href="#\/news\/(.*?)\//', $html_cont, $matches );

                foreach ($matches[1] as $value) {
                    $segment = explode("/", $value);
                    if ($segment[0]) $ids[] = $segment[0];
                    $count ++;
                    if ($count > $limit) break;
                }

                if (count($ids) > 0) {
                    $news_url = 'https://core-api.barchart.com/v1/news/story?id=' . implode(',', $ids) . '&fields=id%2Ctitle%2Cauthor%2Cfeed%2CfeedName%2Cpublished%2Cimage%2Ccontent%2Csymbols%2Crelated%2CsourceId%2Cmedia%2Cthumbnail';

                    $extracted = file_get_contents($news_url);
                }

                if ($extracted == '')   {
                    $extracted = '{
                      "count": 0,
                      "total": 0,
                      "data": []
                    }';
                }

                header('Content-Type: application/json');

			}

			return $extracted;

		}

        public static function import_finviz_items($for_output = true) {
            ob_start();
            $url = 'http://finviz.com/screener.ashx?v=152&t=AAPL&c=0';
            $html = file_get_html($url);

            $items = array();

            if ($html) {
                foreach($html->find('td.filters-cells') as $td) {

                    $item_text = '';
                    $item_url = '';

                    foreach ($td->find('.screener-combo-title') as $sp) {
                        $item_text = $sp->innertext;
                    }

                    $pp = '';
                    foreach ($td->find('input[type=checkbox]') as $sp) {
                        $pp = html_entity_decode($sp->onclick);
                    }

                    $pp = explode('&', $pp);
                    foreach ($pp AS $p) {
                        $seg = explode('=', $p);
                        if ((count($seg) == 2) && ($seg[0] == 'c')) {
                            $item_url = explode(',', $seg[1]);
                            $item_url = str_replace(['\''], '', $item_url[1]);
                        }
                    }

                    if ($item_url)  $items[] = [$item_text, $item_url];
                }

                update_option( 'intrinio_finviz_items', $items );    
            }
            ob_end_clean();

            if ($for_output) {
                header('Content-Type: application/json');
                return Intrinio_Helper::json_encode($items);    
            } else {
                return $items;
            }
            
        }

        public static function import_quandl_items() {

            $quandl_items = array();

            update_option( 'intrinio_quandl_apikey', $_REQUEST['key'] );    

            $api_key = get_option('intrinio_quandl_apikey');
            if (!$api_key) return;

            $quandl = new Quandl($api_key);
            $quandl->format = "json";
            foreach (self::$quandl_zacks as $key => $value) {
                $data = $quandl->getSymbol(str_replace('{TICKER}', '
                    ', $value[1]));
                $data = json_decode($data, true);
                if ($data['dataset']) {
                    $quandl_items[$key] = $data['dataset']['column_names'];
                }
            }
            update_option( 'intrinio_quandl_items', $quandl_items );    
            return;
        }

        public static function intrinio_preload($title, $content, $pattern) {
            list($title, $content) = self::process_content($title, $content, $pattern);

            $title = do_shortcode($title);
            $content = do_shortcode($content);

            header('Content-Type: application/json');
            echo Intrinio_Helper::json_encode(['title' => $title, 'content' => $content]); 
        }

    }
}


if ( !class_exists( 'Intrinio_Helper' ) ) {
	class Intrinio_Helper {
        
        public static function replace_ticker($content, $ticker)
        {
             $look_for = " ticker=";
             $tickers = array();

             $ps = explode($look_for, $content);
             for ($i=1; $i<count($ps); $i++) {
                $ts = preg_split("/[\s,-,\]]+/", $ps[$i]);
                if ($ts[0] != '') {
                    $tickers[] = $look_for . $ts[0];
                }
             }

             $newCont = str_replace($tickers, $look_for . $ticker, $content);
             return $newCont;
        }

		public static function find_valid_filename($dir, $fname)
		{
		    $parts = explode(".", $fname);
		    if ($parts) {
		        $ext = end($parts);
		    } else {
		        // error_log(__METHOD__."(): Invalid extension from filename " . $fname);
		        return false;
		    }

		    $pre_name = str_replace(".".$ext, "", $fname);

		    // replace all special characters as "_" to avoid any exception that might occur
		    // $pre_name = preg_replace("/[`!#$%^&'()+,;=@\[\]{}~ .]/", '_', $pre_name);
		    $pre_name = trim(preg_replace("/[^a-zA-Z0-9_\-]/", '_', $pre_name));
		    if (strlen($pre_name) >= 50) {
		        $pre_name = str_split($pre_name, 50)[0];
		    }

		    $check_name = trim(str_replace("_", "", $pre_name));
		    if ($check_name == '') $pre_name = 'unnamed';
		    /////////////////////////////////////////////////////

		    // $pre_name = $pre_name . time();
		    $img_ext = ['png', 'jpg', 'gif'];
		    $is_image = in_array($ext, $img_ext);

		    $ind = "";
		    $is_exist = true;
		    do {
		        
		        $fname = $pre_name.$ind.".".$ext;
		        if ( !file_exists($dir."/".$fname) ) {
		            $is_exist = false;
		        }

		        if ($ind === "") {
		            $ind = 0;
		        } else {
		            $ind++;
		        }

		    } while ($is_exist);

		    return $fname;
		}

		public static function json_encode($data_array) {
	        $json = json_encode($data_array);
	        if ( !$json ) {
	            array_walk_recursive($data_array, function(&$val) {
	                $temp = json_encode($val);
	                if (!$temp) {
	                    $val = self::make_string_safe($val); // need to take out bad utf-8 char existing in non-utf8 format string
	                }

	                $temp = json_encode($val);
	                if (!$temp) {
	                    $val = utf8_encode($val);
	                }
	            });
	            $json = json_encode($data_array);
	        }

	        return $json;
	    }

	    public static function make_string_safe($str) {
	    	if (function_exists('mb_convert_encoding')) {
	    		return mb_convert_encoding($str, 'utf-8', 'utf-8');
	    	} else {
                $str = iconv('ISO-8859-1','UTF-8', $str);
	    		return $str;
	    	}
	    }

	    public static function insert_attachment($post_id, $filename) {

	    	$wp_upload_dir = wp_upload_dir();
			$filetype = wp_check_filetype( basename( $filename ), null );

			// Get the path to the upload directory.
			$new_name = self::find_valid_filename($wp_upload_dir['path'], $post_id . '-' . basename($filename));
			copy($filename, $wp_upload_dir['path'] . '/' . $new_name);

			$filename = $wp_upload_dir['path'] . '/' . $new_name;

			// Prepare an array of post data for the attachment.
			$attachment = array(
				'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			// Insert the attachment.
			$attach_id =  wp_insert_attachment( $attachment, $filename, $post_id );

			// Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			// Generate the metadata for the attachment, and update the database record.
			$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			return $attach_id;		
		}

        public static function get_my_protocol() {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            return $protocol;
        }

        public static function replace_comma_tag($str) {
            return str_replace([', ', '&#44; ', ',', '&#44;'], ' ', $str);
        }

        public static function make_data_safe($data = array()) {
            if (is_array($data)) {
                foreach ($data as $key => $val) {
                    $data[$key] = self::make_data_safe($val);
                }
            } else {
                if (get_magic_quotes_gpc() || true) { // seems WP attaches slash no matter what...
                    $data = stripcslashes($data);
                    // $data = strip_special_chars($data);
                }
            }

            return $data;
        }

        public static function parseHtmlTable($html) {
            $results = array();

            $tables = $html->find('table');
            for ($i=0; $i<count($tables); $i++) {
                $tbl = $tables[$i];
                $trs = $tbl->find('tr');
                $keys = array();
                for ($j=0; $j<count($trs); $j++) { 
                    $tr = $trs[$j];
                    $ths = $tr->find('th');
                    for ($p=1; $p<count($ths); $p++) {
                        $keys[$p] = $ths[$p]->innertext;
                    }

                    $tds = $tr->find('td');
                    if (count($tds) > 0) {
                        $rowKey = html_entity_decode($tds[0]->innertext);
                        $rowKey = trim($rowKey);
                        if (!$rowKey) $rowKey = $j;
                        for ($p=1; $p<count($tds); $p++) {
                            $k = $p;
                            if (isset($keys[$p])) $k = $keys[$p];
                            if (!isset($results[$rowKey])) $results[$rowKey] = array();
                            $results[$rowKey][$k] = html_entity_decode($tds[$p]->innertext);
                        }
                    }
                }
            }

            return $results;
        }
	}
}