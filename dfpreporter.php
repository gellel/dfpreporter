<?php

/** PHP set method error handlers. **/
ini_set('display_errors', true);
/** PHP set initialisation errors handlers. **/
ini_set('display_startup_errors', true);
/** PHP set error handlers. **/
error_reporting(E_ALL);

/** PHP define constant path for file storage. **/
$FILE_PATH = dirname(__FILE__) . DIRECTORY_SEPARATOR;

/** PHP define server timezone. **/
$SERVER_TIME = date_default_timezone_set('Australia/Sydney');

/** PHP define current time. **/
$CURRENT_TIME = date('H', time());

/** PHP define offset time. **/
$OFFSET_TIME = strval(intval($CURRENT_TIME) - 1);

/** PHP define storage file base name. **/
$FILE_NAME = isset($OPT['fname']) ? $OPT['fname'] : 'DFP';

/** PHP define storage file base extension. **/
$FILE_EXT = isset($OPT['fext']) ? $OPT['fext'] : '.csv.gz';

/** DFP service id. **/
$DFP_ID = 'INSERT_DFP_ID_HERE';

/** HipChat hosted domain. **/
$HIP_SERVER = 'INSERT_DOMAIN_NAME_HERE';

/** HipChat room id. **/
$HIP_ROOM = 'INSERT_ROOM_ID_HERE';

/** HipChat authentication token. **/
$HIP_AUTH = 'INSERT_GENERATED_AUTH_TOKEN_HERE';

/** HipChat notifications endpoint. **/
$HIP_END = 'https://' .  $HIP_SERVER . '.hipchat.com/v2/room/' . $HIP_ROOM . '/notification?auth_token=' . $HIP_AUTH;

/** PHP composer directory. **/
require __DIR__ . '/vendor/autoload.php';

/** PHP composer modules. **/
use DateTime;
use DateTimeZone;
use Google\AdsApi\Common\OAuth2TokenBuilder;
use Google\AdsApi\Dfp\DfpServices;
use Google\AdsApi\Dfp\DfpSession;
use Google\AdsApi\Dfp\DfpSessionBuilder;
use Google\AdsApi\Dfp\Util\v201702\DfpDateTimes;
use Google\AdsApi\Dfp\Util\v201702\ReportDownloader;
use Google\AdsApi\Dfp\Util\v201702\StatementBuilder;
use Google\AdsApi\Dfp\v201702\Column;
use Google\AdsApi\Dfp\v201702\DateRangeType;
use Google\AdsApi\Dfp\v201702\Dimension;
use Google\AdsApi\Dfp\v201702\ExportFormat;
use Google\AdsApi\Dfp\v201702\NetworkService;
use Google\AdsApi\Dfp\v201702\ReportJob;
use Google\AdsApi\Dfp\v201702\ReportQuery;
use Google\AdsApi\Dfp\v201702\ReportQueryAdUnitView;
use Google\AdsApi\Dfp\v201702\ReportService;
use Google\AdsApi\Dfp\v201702\LineItemService;
use Google\AdsApi\Dfp\v201702\PauseLineItems as PauseLineItemsAction;

class Google {

	public static function Service () {
		/* @return: @type: @object. */
		return (new DfpServices());
	}

	public static function OAuth2 () {
		/* @return: @type: @object. */
		return (new DfpSessionBuilder())->fromFile()->
			withOAuth2Credential((new OAuth2TokenBuilder())
				->fromFile()->build())->build();
	}
}

class Dimensions {

	public static function Advertiser () {
		/* @return: @type: @array. */
		return array(
			Dimension::ADVERTISER_NAME);
	}

	public static function Line () {
		/* @return: @type: @array. */
		return array(
			Dimension::LINE_ITEM_ID, 
			Dimension::LINE_ITEM_NAME);
	}

	public static function Get () {
		/* @return: @type: @array. */
		return array_merge(
			self::Advertiser(),
			self::Line());
	}
}

class Columns {

	public static function Total () {
		/* @return: @type: @array. */
		return array(
			Column::TOTAL_CODE_SERVED_COUNT, 
			Column::TOTAL_LINE_ITEM_LEVEL_IMPRESSIONS);
	}

	public static function Viewership () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_VIEWERSHIP_TOTAL_ERROR_COUNT, 
	    	Column::VIDEO_VIEWERSHIP_TOTAL_ERROR_RATE);
	}

	public static function VAST000 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_2_ERROR_COUNT);
	}

	public static function VAST100 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_100_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_101_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_102_COUNT);
	}

	public static function VAST200 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_200_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_201_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_202_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_203_COUNT);	
	}

	public static function VAST300 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_300_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_301_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_302_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_303_COUNT);
	}

	public static function VAST400 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_400_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_401_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_402_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_403_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_405_COUNT);
	}

	public static function VAST500 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_500_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_501_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_502_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_503_COUNT);
	}

	public static function VAST600 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_600_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_601_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_602_COUNT,
			Column::VIDEO_ERRORS_VAST_3_ERROR_603_COUNT,
			Column::VIDEO_ERRORS_VAST_3_ERROR_604_COUNT);
	}

	public static function VAST900 () {
		/* @return: @type: @array. */
		return array(
			Column::VIDEO_ERRORS_VAST_3_ERROR_900_COUNT, 
			Column::VIDEO_ERRORS_VAST_3_ERROR_901_COUNT);
	}

	public static function Get () {
		/* @return: @type: @array. */
		return array_merge(
			self::Total(), 
			self::Viewership(),
			self::VAST000(), 
			self::VAST100(), 
			self::VAST200(), 
			self::VAST300(), 
			self::VAST400(), 
			self::VAST500(), 
			self::VAST600(), 
			self::VAST900());
	}
}

class Metrics {

	public static function Served (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::TOTAL_CODE_SERVED_COUNT, $line) ? 
			intval($line[Column::TOTAL_CODE_SERVED_COUNT]) : 0;
	}

	public static function Impressions (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::TOTAL_LINE_ITEM_LEVEL_IMPRESSIONS, $line) ? 
			intval($line[Column::TOTAL_LINE_ITEM_LEVEL_IMPRESSIONS]) : 0;
	}

	public static function Errors (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_VIEWERSHIP_TOTAL_ERROR_COUNT, $line) ? 
			intval($line[Column::VIDEO_VIEWERSHIP_TOTAL_ERROR_COUNT]) : 0;
	}

	public static function Rate (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_VIEWERSHIP_TOTAL_ERROR_RATE, $line) ? 
			floatval($line[Column::VIDEO_VIEWERSHIP_TOTAL_ERROR_RATE]) : 0.0;
	}

	public static function VAST000 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_2_ERROR_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_2_ERROR_COUNT]) : 0;
	}

	public static function VAST100 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_100_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_100_COUNT]) : 0;
	}

	public static function VAST101 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_101_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_101_COUNT]) : 0;
	}

	public static function VAST102 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_102_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_102_COUNT]) : 0;
	}

	public static function VAST200 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_200_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_200_COUNT]) : 0;
	}

	public static function VAST201 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_201_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_201_COUNT]) : 0;
	}

	public static function VAST202 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_202_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_202_COUNT]) : 0;
	}

	public static function VAST203 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_203_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_203_COUNT]) : 0;
	}

	public static function VAST300 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_300_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_300_COUNT]) : 0;
	}

	public static function VAST301 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_301_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_301_COUNT]) : 0;
	}

	public static function VAST302 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_302_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_302_COUNT]) : 0;
	}

	public static function VAST303 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_303_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_303_COUNT]) : 0;
	}

	public static function VAST400 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_400_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_400_COUNT]) : 0;
	}

	public static function VAST401 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_401_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_401_COUNT]) : 0;
	}

	public static function VAST402 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_402_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_402_COUNT]) : 0;
	}

	public static function VAST403 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_403_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_403_COUNT]) : 0;
	}

	public static function VAST405 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_405_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_405_COUNT]) : 0;
	}

	public static function VAST500 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_500_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_500_COUNT]) : 0;
	}

	public static function VAST501 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_501_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_501_COUNT]) : 0;
	}

	public static function VAST502 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_502_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_502_COUNT]) : 0;
	}

	public static function VAST503 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_503_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_503_COUNT]) : 0;
	}

	public static function VAST600 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_600_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_600_COUNT]) : 0;
	}

	public static function VAST601 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_601_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_601_COUNT]) : 0;
	}

	public static function VAST602 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_602_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_602_COUNT]) : 0;
	}

	public static function VAST603 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_603_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_603_COUNT]) : 0;
	}

	public static function VAST604 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_604_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_604_COUNT]) : 0;
	}

	public static function VAST900 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_900_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_900_COUNT]) : 0;
	}

	public static function VAST901 (array $line) {
		/* @return: @type: @number. */
		return array_key_exists(Column::VIDEO_ERRORS_VAST_3_ERROR_901_COUNT, $line) ? 
			intval($line[Column::VIDEO_ERRORS_VAST_3_ERROR_901_COUNT]) : 0;
	}
}

class VAST {

	public static function Passing (array $line) {
		/* @return: @type: @number. */
		return array_sum(array(
			Metrics::VAST200($line), Metrics::VAST201($line), Metrics::VAST202($line), Metrics::VAST203($line),
			Metrics::VAST600($line), Metrics::VAST601($line), Metrics::VAST602($line), Metrics::VAST603($line), Metrics::VAST604($line)));
	}

	public static function Failing (array $line) {
		/* @return: @type: @number. */
		return array_sum(array(
			Metrics::VAST000($line),
			Metrics::VAST100($line), Metrics::VAST101($line), Metrics::VAST102($line),
			Metrics::VAST300($line), Metrics::VAST301($line), Metrics::VAST302($line), Metrics::VAST303($line),
			Metrics::VAST400($line), Metrics::VAST401($line), Metrics::VAST402($line), Metrics::VAST403($line), Metrics::VAST405($line),
			Metrics::VAST500($line), Metrics::VAST501($line), Metrics::VAST502($line), Metrics::VAST503($line),
			Metrics::VAST900($line), Metrics::VAST901($line)));
	}

	public static function Sum (array $line) {
		/* @return: @type: @number. */
		return self::Failing($line) + self::Passing($line);
	}
}

class Percentage {

	public static function Increase ($a, $b) {
		/* set base number. */
		$previous = is_numeric($a) ? $a : 0;
		/* set base number. */
		$current = is_numeric($b) ? $b : 0;
		/* set differential number. */
		$increase = $current - $previous;
		/* @return: @type: @number. */
		return $increase > 0 && $previous > 0 ? ($increase / $previous) * 100 : 0.0;
	}

	public static function Decrease ($a, $b) {
		/* set base number. */
		$previous = is_numeric($a) ? $a : 0;
		/* set base number. */
		$current = is_numeric($b) ? $b : 0;
		/* set differential number. */
		$decrease = $previous - $current;
		/* @return: @type: @number. */
		return $decrease > 0 && $previous > 0 ? ($decrease / $previous) * 100 : 0.0;
	}

	public static function Sum ($a, $b) {
		/* set base number. */
		$portion = is_numeric($a) ? $a : 0;
		/* set base number. */
		$sum = is_numeric($b) ? $b : 0;
		/* @return: @type: @number. */
		return $portion && $sum ? ($portion / $sum) * 100 : 0.0;
	}
}

class Compare {

	public static function Served (array $a, array $b) {
		/* @return: @type: @number. */
		return abs(Metrics::Served($a) - Metrics::Served($b));
	}

	public static function Impressions (array $a, array $b) {
		/* @return: @type: @number. */
		return abs(Metrics::Impressions($a) - Metrics::Impressions($b));
	}

	public static function Errors (array $a, array $b) {
		/* @return: @type: @number. */
		return abs(Metrics::Errors($a) - Metrics::Errors($b));
	}

	public static function Passing (array $a, array $b) {
		/* @return: @type: @number. */
		return abs(VAST::Passing($a) - VAST::Passing($b));
	}

	public static function Failing (array $a, array $b) {
		/* @return: @type: @number. */
		return abs(VAST::Failing($a) - VAST::Failing($b));
	}

	public static function Sum (array $a, array $b) {
		/* @return: @type: number. */
		return abs(VAST::Sum($a) - VAST::Sum($b));
	}

	public static function VAST000 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST000($a) - Metrics::VAST000($b));
	}

	public static function VAST100 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST100($a) - Metrics::VAST100($b));
	}

	public static function VAST101 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST101($a) - Metrics::VAST101($b));
	}

	public static function VAST102 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST102($a) - Metrics::VAST102($b));
	}

	public static function VAST200 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST200($a) - Metrics::VAST200($b));
	}

	public static function VAST201 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST201($a) - Metrics::VAST201($b));
	}

	public static function VAST202 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST202($a) - Metrics::VAST202($b));
	}

	public static function VAST203 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST203($a) - Metrics::VAST203($b));
	}

	public static function VAST300 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST300($a) - Metrics::VAST300($b));
	}

	public static function VAST301 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST301($a) - Metrics::VAST301($b));
	}

	public static function VAST302 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST302($a) - Metrics::VAST302($b));
	}

	public static function VAST303 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST303($a) - Metrics::VAST303($b));
	}

	public static function VAST400 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST400($a) - Metrics::VAST400($b));
	}

	public static function VAST401 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST401($a) - Metrics::VAST401($b));
	}

	public static function VAST402 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST402($a) - Metrics::VAST402($b));
	}

	public static function VAST403 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST403($a) - Metrics::VAST403($b));
	}

	public static function VAST405 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST405($a) - Metrics::VAST405($b));
	}

	public static function VAST500 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST500($a) - Metrics::VAST500($b));
	}

	public static function VAST501 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST501($a) - Metrics::VAST501($b));
	}

	public static function VAST502 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST502($a) - Metrics::VAST502($b));
	}

	public static function VAST503 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST503($a) - Metrics::VAST503($b));
	}

	public static function VAST600 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST600($a) - Metrics::VAST600($b));
	}

	public static function VAST601 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST601($a) - Metrics::VAST601($b));
	}

	public static function VAST602 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST602($a) - Metrics::VAST602($b));
	}

	public static function VAST603 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST603($a) - Metrics::VAST603($b));
	}

	public static function VAST604 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST604($a) - Metrics::VAST604($b));
	}

	public static function VAST900 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST900($a) - Metrics::VAST900($b));
	}

	public static function VAST901 (array $a, array $b) {
		/* @return: @type: number. */
		return abs(Metrics::VAST901($a) - Metrics::VAST901($b));
	}
}

class Difference {

	public static function PercentageIncreaseOfImpressions (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Increase(Metrics::Impressions($a), Metrics::Impressions($b)), 2);
	}

	public static function PercentageIncreaseOfErrors (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Increase(VAST::Sum($a), VAST::Sum($b)), 2);
	}
}

class Composition {

	public static function PercentageOfImpressionsInHistoricalImpressions (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Impressions($a, $b), Metrics::Impressions($b)), 2);
	}

	public static function PercentageOfPassingErrorsInErrors (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Passing($a, $b), Compare::Sum($a, $b)), 2);
	}

	public static function PercentageOfPassingErrorsInImpressions (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Passing($a, $b), Metrics::Impressions($b)), 2);
	}

	public static function PercentageOfFailingErrorsInImpressions (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Failing($a, $b), Metrics::Impressions($b)), 2);
	}

	public static function PercentageOfFailingErrorsInErrors (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Failing($a, $b), Compare::Sum($a, $b)), 2);
	}

	public static function PercentageOfErrorsInImpressions (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Sum($a, $b), Compare::Impressions($a, $b)), 2);
	}

	public static function PercentageOfErrorsInHistoricalErrors (array $a, array $b) {
		/* @return: @type: @number. */
		return round(Percentage::Sum(Compare::Sum($a, $b), VAST::Sum($b)), 2);
	}
}

class Line {

	public static function Keys (array $positions) {
		/* set associated array container. */
		$keys = array();
		/* iterate for position keys. substitute substrings containing column and dimension. */
		for ($i = 0; $i < count($positions); $i++) $keys[preg_replace('/Column|Dimension|\./', '', $positions[$i])] = $i;
		/* @return: @type: @array. */
		return $keys;
	}

	public static function Match (array $positions, array $line) {
		/* set associated array container. */
		$keys = array();
		/* iterate for positions keys. set array key as positions key and assign value. */
		foreach ($positions as $key => $value) $keys[$key] = $line[$value];	
		/* @return: @type: @array. */
		return $keys;
	}

	public static function Pause ($id = null) {
		/* @return: @type: @boolean. */
		if (!$id) return false;
		/* set line service. */
		$lineItemService = Google::Service()->get(
			Google::OAuth2(), LineItemService::class);
		/* set statement service. */
		$statementBuilder = (new StatementBuilder());
		/* set statement query. */
		$statementBuilder->where('id = :id')->orderBy('id ASC')->limit(1)->
			WithBindVariableValue('id', strval($id));
		/* pause line on server. */
		$result = $lineItemService->performLineItemAction(
			(new PauseLineItemsAction()), $statementBuilder->toStatement());
		/* @return: @type: @boolean. */
		return $result->getNumChanges() > 0 ? true : false;
	}

	public static function Get ($id = null) {
		/* @return: @type: @boolean. */
		if (!$id) return array();
		/* set line service. */
		$lineItemService = Google::Service()->get(Google::OAuth2(), 
			LineItemService::class);
		/* set statement service. */
		$statementBuilder = (new StatementBuilder());
		/* set statement query. */
		$statementBuilder->where('id = :id')->orderBy('id ASC')->limit(1)->
			WithBindVariableValue('id', strval($id));
		/* get line from server. */
		$lineItemService = $lineItemService->getLineItemsByStatement(
			$statementBuilder->toStatement())->getResults();
		/* @return: @type: @array. */
		return count($lineItemService) ? $lineItemService[0] : array();
	}
}

class Report {

	public static function Run ($directory = null, $name = null) {
		/* @return: @type: @boolean. */
		if (!(file_exists($directory) && $name)) return false;
		/* set report service. */
		$reportService = Google::Service()->get(
			Google::OAuth2(), ReportService::class);
		/* set statement service. */
		$statementBuilder = (new StatementBuilder());
		/* set query service. */
		$reportQuery = (new ReportQuery());
		/* set query dimensions. */
		$reportQuery->setDimensions(
			Dimensions::Get());
		/* set query columns. */
	    $reportQuery->setColumns(
	    	Columns::Get());
	    /* set query to sql. */
    	$reportQuery->setStatement(
    		$statementBuilder->toStatement());
    	/* set query organisation type. */
    	$reportQuery->setAdUnitView(
    		ReportQueryAdUnitView::HIERARCHICAL);
    	/* set query date. */
	    $reportQuery->setDateRangeType(
	    	DateRangeType::TODAY);
	    /* set report service. */
	    $reportJob = (new ReportJob());
	    /* set report query. */
	    $reportJob->setReportQuery($reportQuery);
	    /* run report server side. */
	    $reportJob = $reportService->runReportJob($reportJob);
	    /* set report downloader service. */
		$reportDownloader = (new ReportDownloader(
			$reportService, $reportJob->getId()));
		/* pause processor until report finishes. */
		$reportDownloader->waitForReportToFinish();
		/* get file from server. */
		$reportDownloader->downloadReport(
			ExportFormat::CSV_DUMP, ($directory . $name));
		/* @return: @type: @boolean. */
		return file_exists($directory . $name);
	}

	public static function Match ($directory = null, $name = null) {
		/* @return: @type: @array. */
		if (!file_exists($directory . $name)) return array();
		/* get file from directory. */
		$file = Report::Read($directory, $name);
		/* set array object titles. */
		$positions = Line::Keys($file[0]);
		/* set array object contents. */
		$contents = array_slice($file, 1);
		/* set organised lines container. */
		$ordered = array();
		/* iterater for lines in content. set line as organised line. set key in content array as line id. */ 
		for ($i = 0; $i < count($contents); $i++) $ordered[($contents[$i][$positions[Dimension::LINE_ITEM_ID]])] = Line::Match($positions, $contents[$i]);
		/* @return: @type: @array. */
		return $ordered;
	}

	public static function Read ($directory = null, $name = null) {
		/* @return: @type: @array. */
		if (!(file_exists($directory) && file_exists($directory . $name))) return array();
		/* get zip file. */
		$f = gzopen($directory . $name, 'r');
		/* set array to contain lines. */
		$file = array();
		/* iterate for file. set file array index as line index. */
		while (!feof($f)) $file[] = fgetcsv($f, 0, ',');
		/* close file. */
		fclose($f);
		/* @return: @type: @array. */
		return $file;
	}

	public static function Get ($directory = null, $name = null) {
		/* @return: @type: @array. */
		return self::Run($directory, $name) ? self::Read($directory, $name) : array();
	}
}

class Set {

	public static function Algorithm (array $s) {

		$MIN = 0;

		$MAX = 1;

		$PERCENTAGE_OF_HOURLY_IMPRESSIONS_IN_HISTORICAL_IMPRESSIONS = $s['PERCENTAGE_OF_HOURLY_IMPRESSIONS_IN_HISTORICAL_IMPRESSIONS'];

		$PERCENTAGE_OF_PASSING_VAST_ERRORS_IN_ERRORS = $s['PERCENTAGE_OF_PASSING_VAST_ERRORS_IN_ERRORS'];

		$PERCENTAGE_OF_FAILING_VAST_ERRORS_IN_ERRORS = $s['PERCENTAGE_OF_FAILING_VAST_ERRORS_IN_ERRORS'];

		$PERCENTAGE_OF_ERRORS_IN_IMPRESSIONS = $s['PERCENTAGE_OF_ERRORS_IN_IMPRESSIONS'];

		$PREVIOUS_IMPRESSIONS = $s['PREVIOUS_IMPRESSIONS']; 

		$PREVIOUS_PASSING_VAST_ERRORS = $s['PREVIOUS_PASSING_VAST_ERRORS'];

		$PREVIOUS_FAILING_VAST_ERRORS = $s['PREVIOUS_FAILING_VAST_ERRORS'];


		/* soften the variable error sum. passing errors offset the distribution. */
		$ALPHA = abs($PERCENTAGE_OF_FAILING_VAST_ERRORS_IN_ERRORS - $PERCENTAGE_OF_PASSING_VAST_ERRORS_IN_ERRORS);

		/* measure the distribution sum against the measured percentage of errors in impressions. */
		/* sums greater than one suggest the percentage is significant portion of critical errors for the sum of errors. */ 
		$BETA = round(($PERCENTAGE_OF_ERRORS_IN_IMPRESSIONS && $ALPHA ?
				$PERCENTAGE_OF_ERRORS_IN_IMPRESSIONS / $ALPHA : 0.01), 2);

		/* soften the constant error sum. passing errors offset the distribution. */
		$LAMBDA = abs($PREVIOUS_FAILING_VAST_ERRORS - $PREVIOUS_PASSING_VAST_ERRORS);

		/* measure the distribution of errors for impressions against the previous set. 
		/* sums greater than one suggest the percentage is a significant portion of critical errors for the sum of errors. */
		$GAMMA = round(($PREVIOUS_IMPRESSIONS && $LAMBDA ?
			(Percentage::Sum($LAMBDA, $PREVIOUS_IMPRESSIONS) * 0.01) : 0.01), 2);

		/* measure the distance between error distribution sums. */
		/* similar distributions suggest a trend for continued error presences. */
		$SIGMA = round($BETA * $GAMMA, 2);


		$s['ALPHA'] = $ALPHA;
		$s['BETA'] = $BETA;
		$s['GAMMA'] = $GAMMA;
		$s['SIGMA'] = $SIGMA;


		$s['SHOULD_LINE_PAUSE'] = ($LAMBDA || $PREVIOUS_IMPRESSIONS) ? max($MIN, min($MAX, round($SIGMA))) :
			max($MIN, min($MAX, round($BETA))); 

		/* @return: @type: @array. */
		return $s;
	}

	public static function Compare (array $x, array $y) {
		/* set formatted array container. */
		$comparisons = array();
		/* iterate for keys in array. */
		foreach ($y as $key => $value) {
			/* set default previous array. */
			$a = array_key_exists($key, $x) ? $x[$key] : array();
			/* set default current array .*/
			$b = array_key_exists($key, $y) ? $y[$key] : array();

			/* construct set. */
			$c = array(

				'LINE_ITEM_NAME' => $b[Dimension::LINE_ITEM_NAME],

				'PREVIOUS_IMPRESSIONS' => Metrics::Impressions($a),
				'PREVIOUS_ERRORS' => VAST::Sum($a),
				'PREVIOUS_PASSING_VAST_ERRORS' => VAST::Passing($a),
				'PREVIOUS_FAILING_VAST_ERRORS' => VAST::Failing($a),

				'CURRENT_IMPRESSIONS' => Metrics::Impressions($b),
				'CURRENT_ERRORS' => VAST::Sum($b),
				'CURRENT_PASSING_VAST_ERRORS' => VAST::Passing($b),
				'CURRENT_FAILING_VAST_ERRORS' => VAST::Failing($b),

				'HOURLY_IMPRESSIONS' => Compare::Impressions($a, $b),
				'HOURLY_ERRORS' => Compare::Sum($a, $b),
				'HOURLY_PASSING_VAST_ERRORS' => Compare::Passing($a, $b),
				'HOURLY_FAILING_VAST_ERRORS' => Compare::Failing($a, $b),

				'HOURLY_INCREASE_IN_IMPRESSIONS_PERCENTAGE' => Difference::PercentageIncreaseOfImpressions($a, $b),
				'HOURLY_INCREASE_IN_ERRORS_PERCENTAGE' => Difference::PercentageIncreaseOfErrors($a, $b),

				'PERCENTAGE_OF_ERRORS_IN_IMPRESSIONS' => Composition::PercentageOfErrorsInImpressions($a, $b),
				'PERCENTAGE_OF_PASSING_ERRORS_IN_IMPRESSIONS' => Composition::PercentageOfPassingErrorsInImpressions($a, $b),
				'PERCENTAGE_OF_FAILING_ERRORS_IN_IMPRESSIONS' => Composition::PercentageOfFailingErrorsInImpressions($a, $b),

				'PERCENTAGE_OF_PASSING_VAST_ERRORS_IN_ERRORS' => Composition::PercentageOfPassingErrorsInErrors($a, $b),
				'PERCENTAGE_OF_FAILING_VAST_ERRORS_IN_ERRORS' => Composition::PercentageOfFailingErrorsInErrors($a, $b),
				'PERCENTAGE_OF_HOURLY_ERRORS_IN_HISTORICAL_ERRORS' => Composition::PercentageOfErrorsInHistoricalErrors($a, $b),

				'PERCENTAGE_OF_HOURLY_IMPRESSIONS_IN_HISTORICAL_IMPRESSIONS' => Composition::PercentageOfImpressionsInHistoricalImpressions($a, $b)
			);

			/* set pause condition for line. */
			if ($c['HOURLY_IMPRESSIONS'] > 0 && $c['HOURLY_ERRORS'] > 0) 
				$comparisons[$key] = self::Algorithm($c);
		}
		/* @return: @type: @array. */
		return $comparisons;
	}

	public static function Concerns (array $a) {
		/* set formatted array container. */
		$comparisons = array();
		/* iterate for keys in array. */
		foreach ($a as $key => $value) {
			/* test key set for pause condition and boolean sum integer defined. */
			if (array_key_exists('SHOULD_LINE_PAUSE', $value) && $value['SHOULD_LINE_PAUSE']) {
				$comparisons[$key] = $value;
			}
		}
		/* @return: @type: @array. */
		return $comparisons;
	}

	public static function Get () {
		/* fetch latest report. */
		Report::Run($GLOBALS['FILE_PATH'], $GLOBALS['CURRENT_TIME'] . '_' . 'HOUR' . '_' . 'DFP' . $GLOBALS['FILE_EXT']);

		/* set previous report to filtered report. */
		$previous = Report::Match($GLOBALS['FILE_PATH'], $GLOBALS['OFFSET_TIME'] . '_' . 'HOUR' . '_' . 'DFP' . $GLOBALS['FILE_EXT']);
		/* set current report to filtered report. */
		$current = Report::Match($GLOBALS['FILE_PATH'], $GLOBALS['CURRENT_TIME'] . '_' . 'HOUR' . '_' . 'DFP' . $GLOBALS['FILE_EXT']);

		/* @return: @type: @array. */
		return self::Compare($previous, $current);
	}
}


class Message {

	public static function Errors (array $a) {

		$length = count($a);

		$table = '<table>';

		$headers = '<tr><th>DFP LINE ID</th><th>IMPRESSIONS</th><th>ERRORS</th></tr>';

		$body = '';

		foreach ($a as $key => $value) {
			$body = $body . '<tr>'. 
				'<td>' . 
					'<a href="https://www.google.com/dfp/' . $GLOBALS['DFP_ID'] . '#delivery/LineItemDetail/lineItemId=' . $key . '">' .
						$key . 
					'</a>' . 
				'</td>' . 
				'<td>' . 
					$value['HOURLY_IMPRESSIONS'] . 
				'</td>' . 
				'<td>' . 
					$value['HOURLY_ERRORS'] . 
				'</td>' . 
			'</tr>';
		}

		$table = $table . $headers . $body . '</table>';

		$message = $table . '<br />' . 
			'<strong>' . 
				strval($length) . 
			'</strong>' . ' ' . 'lines are' . ' ' . '<strong>' . 'erroring.' . '</strong>';

		/* @return: @type: @string. */
		return $message;
	}
}

class CURL {

	public static function Send ($url, $method, array $headers, array $body) {
		/* @return: @type: @string. */
		if (!(is_string($url) && is_string($method))) return '';
		/* set curl. */
		$request = curl_init($url);
		/* set request body array as json string. */
		$content = json_encode($body);
		/* set http header request type as json. */
		$ctype = 'Content-Type: application/json';
		/* set http header request content length. */
		$cleng = 'Content-Length:' . ' ' . strlen($content);
		/* set curl options. */
		curl_setopt($request, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($request, CURLOPT_POSTFIELDS, $content);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($request, CURLOPT_HTTPHEADER, array($ctype, $cleng));
		curl_setopt($request, CURLOPT_TIMEOUT, 5);
		curl_setopt($request, CURLOPT_CONNECTTIMEOUT, 5);
		/* set response from server. */
		$result = curl_exec($request);
		/* close curl connection. */
		curl_close($request);
		/* @return: @type: @string. */
		return $result;
	}
}


function ErrorPrint (array $a) {

	foreach ($a as $key => $value) {
		echo '<h3>' . 'LINE_ID' . ':' . ' ' . $key . '</h3>';

		echo '<h4>' . 'LINE_TIME' . ':' . ' ' . $GLOBALS['OFFSET_TIME'] . '-' . $GLOBALS['CURRENT_TIME'] . '</h4>';

		foreach ($value as $prop => $attr) {
			echo '<p>' . '<strong>' . $prop . '</strong>' . ':' . ' ' . strval($attr) . '.' . '</p>';
		}

		echo '<br />';
		echo '<br />';
	}
}

/* entire set; including passing. */
$d = Set::Get();

/* subset of entire set; only failing. */
$p = Set::Concerns($d);

echo '<style>body { font-family: sans-serif; }</style>';

ErrorPrint($p);

echo '<hr />';

echo Message::Errors($p);

$z = CURL::Send($HIP_END, 'POST', array(), array(
		'from' => '(DFP BOT)',
		'message_format'=> 'html',
		'color' => 'red',
		'notify' => 'true',
		'message' => 
			"<strong>" . 
				round(Percentage::Sum(count($p), count($d))) . "%" . 
			"</strong>" . 
			" " . "of DFP lines are" . " " . 
			"<strong>" . 
				"erroring" . 
			"</strong>" . 
			" " . "this hour" . " " . "(" . $OFFSET_TIME . "-" . $CURRENT_TIME . ")" . ".")
);


?>