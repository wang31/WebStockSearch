<?php
	header("Content-type: text/xml");
?>
<?php 
			if(isset($_GET['symbol'])) {
				$QuoteQuery = "http://query.yahooapis.com/v1/public/yql?q=Select%20Name%2C%20Symbol%2C%20LastTradePriceOnly%2C%20Change%2C%20ChangeinPercent%2C%20PreviousClose%2C%20DaysLow%2C%20DaysHigh%2C%20Open%2C%20YearLow%2C%20YearHigh%2C%20Bid%2C%20Ask%2C%20AverageDailyVolume%2C%20OneyrTargetPrice%2C%20MarketCapitalization%2C%20Volume%2C%20Open%2C%20YearLow%20from%20yahoo.finance.quotes%20where%20symbol%3D%22".htmlspecialchars($_GET['symbol'])."%22&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
				$file = file_get_contents($QuoteQuery);
				$xml = simplexml_load_string($file);
				$return = "<result>";
				if($xml != null) {
					if($xml->children() != null && $xml->children()->children() != null && $xml->children()->children()->children() != null) {
						$data = $xml->children()->children()->children();
						if($data->Change != ""){
							$return .= "<Name>".htmlspecialchars($data->Name)."</Name>";
							$return .= "<Symbol>".htmlspecialchars($data->Symbol)."</Symbol>";
							$return .= "<Quote>";
							if($data->Change != ""){
								$return .= "<ChangeType>".substr($data->Change, 0, 1)."</ChangeType>";
								$return .= "<Change>".number_format((float)substr($data->Change, 1), 2)."</Change>";
							}
							else{
								$return .= "<ChangeType></ChangeType>";
								$return .= "<Change></Change>";	
							}
							if($data->ChangeinPercent != "")
								$return .= "<ChangeInPercent>".substr($data->ChangeinPercent, 1)."</ChangeInPercent>";
							else
								$return .= "<ChangeInPercent></ChangeInPercent>";
							if($data->LastTradePriceOnly != "")
								$return .= "<LastTradePriceOnly>".number_format((float)$data->LastTradePriceOnly, 2)."</LastTradePriceOnly>";
							else
								$return .= "<LastTradePriceOnly></LastTradePriceOnly>";
							if($data->PreviousClose != "")
								$return .= "<PreviousClose>".number_format((float)$data->PreviousClose, 2)."</PreviousClose>";
							else
								$return .= "<PreviousClose></PreviousClose>";
							if($data->DaysLow != "")
								$return .= "<DaysLow>".number_format((float)$data->DaysLow, 2)."</DaysLow>";
							else
								$return .= "<DaysLow></DaysLow>";
							if($data->DaysHigh != "")
								$return .= "<DaysHigh>".number_format((float)$data->DaysHigh, 2)."</DaysHigh>";
							else
								$return .= "<DaysHigh></DaysHigh>";
							if($data->Open != "")
								$return .= "<Open>".number_format((float)$data->Open, 2)."</Open>";
							else
								$return .= "<Open></Open>";
							if($data->YearLow != "")
								$return .= "<YearLow>".number_format((float)$data->YearLow, 2)."</YearLow>";
							else
								$return .= "<YearLow></YearLow>";
							if($data->YearHigh != "")
								$return .= "<YearHigh>".number_format((float)$data->YearHigh, 2)."</YearHigh>";
							else
								$return .= "<YearHigh></YearHigh>";
							if($data->Bid != "")
								$return .= "<Bid>".number_format((float)$data->Bid, 2)."</Bid>";
							else
								$return .= "<Bid></Bid>";
							if($data->Volume != "")
								$return .= "<Volume>".number_format((float)$data->Volume)."</Volume>";
							else
								$return .= "<Volume></Volume>";
							if($data->Ask != "")
								$return .= "<Ask>".number_format((float)$data->Ask, 2)."</Ask>";
							else
								$return .= "<Ask></Ask>";
							if($data->AverageDailyVolume != "")
								$return .= "<AverageDailyVolume>".number_format((float)$data->AverageDailyVolume)."</AverageDailyVolume>";
							else
								$return .= "<AverageDailyVolume></AverageDailyVolume>";
							if($data->OneyrTargetPrice != "")
								$return .= "<OneYearTargetPrice>".number_format((float)$data->OneyrTargetPrice, 2)."</OneYearTargetPrice>";
							else
								$return .= "<OneYearTargetPrice></OneYearTargetPrice>";
							if($data->MarketCapitalization != "")
								$return .= "<MarketCapitalization>".number_format((float)substr($data->MarketCapitalization, 0 , -1), 1).substr($data->MarketCapitalization, -1)."</MarketCapitalization>";
							else
								$return .= "<MarketCapitalization></MarketCapitalization>";
							$return .= "</Quote>";
						}
					}	
				}
				

				$NewsQuery = "http://feeds.finance.yahoo.com/rss/2.0/headline?s=".htmlspecialchars($_GET['symbol'])."&region=US&lang=en-US";
				$file = file_get_contents($NewsQuery);
				$xml = simplexml_load_string($file);
				if($xml != null){
					$return .= "<News>";
					$data = $xml->children();
					foreach($data->children() as $tag){
						if($tag->getName() == "item"){
							$elements = $tag->children();
							$return .= "<Item>";
							//$return .= "<Title>".htmlentities($elements->title, ENT_QUOTES, 'UTF-8')."</Title>";
							$return .= "<Title>".htmlspecialchars($elements->title, ENT_QUOTES, 'UTF-8')."</Title>";
							$return .= "<Link>".htmlspecialchars($elements->link)."</Link>";
							$return .= "</Item>";
						}
					}
					$return .= "</News>";
				}
				$return .= "<StockChartImageURL>http://chart.finance.yahoo.com/t?s=".htmlspecialchars($_GET['symbol'])."&amp;lang=enUS&amp;width=300&amp;height=180</StockChartImageURL>";
			    $return .= "</result>";
			    print $return;
			}
		?>