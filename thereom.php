<?php

function O ($PERCENTAGE_OF_HOURLY_ERRORS_IN_HISTORICAL_ERRORS, 
	$PERCENTAGE_OF_HOURLY_IMPRESSIONS_IN_HISTORICAL_IMPRESSIONS, 
	$PERCENTAGE_OF_PASSING_VAST_ERRORS_IN_ERRORS, 
	$PERCENTAGE_OF_FAILING_VAST_ERRORS_IN_ERRORS) {

	$MIN = -1;

	$MAX = 1;

	/* calculated sums that are greater than one suggests that the significance in errors 
	 * is being skewed by a dramatic rise in impressions.
	 * floating point numbers that are below one indicate a rise in errors not being
	 * modified or multipled by rises in impressions. */

	$a = $PERCENTAGE_OF_HOURLY_IMPRESSIONS_IN_HISTORICAL_IMPRESSIONS / $PERCENTAGE_OF_HOURLY_ERRORS_IN_HISTORICAL_ERRORS;

	/* set the scale vector for error sums. passing vast errors should indicate greater stability in the line.
	 * higher error percentage sums seeingly have rarer occurrences of passing errors. 
	 * floating point numbers that are greater than one suggests that the rise in errors 
	 * is being skewed by a greater sum of passing errors. */

	$b = max($MIN, min($MAX, 
		($PERCENTAGE_OF_PASSING_VAST_ERRORS_IN_ERRORS / $PERCENTAGE_OF_FAILING_VAST_ERRORS_IN_ERRORS)));

	/* set the scale offset for errors as a result of an increase in impressions.
	 * lower floating point numbers are to scale detection back. */

	$c = ($b + $a) * $b;

	/* @return: @type: @number. */
	return $c;
}

function S ($PERCENTAGE_OF_ERRORS_IN_IMPRESSIONS, 
	$PERCENTAGE_OF_PASSING_ERRORS_IN_IMPRESSIONS, 
	$PERCENTAGE_OF_FAILING_ERRORS_IN_IMPRESSIONS) {

	$MIN = -1;

	$MAX = 1;

	$a = max($MIN, min($MAX,
		($PERCENTAGE_OF_PASSING_ERRORS_IN_IMPRESSIONS / $PERCENTAGE_OF_FAILING_ERRORS_IN_IMPRESSIONS)));

	$b = (abs($PERCENTAGE_OF_PASSING_ERRORS_IN_IMPRESSIONS - $PERCENTAGE_OF_FAILING_ERRORS_IN_IMPRESSIONS) * $a) / 100;

	return $b;
}

echo O(100, 100, 0, 100);

echo '<br/>';

echo O(1.1, 10, 10, 90);

?>