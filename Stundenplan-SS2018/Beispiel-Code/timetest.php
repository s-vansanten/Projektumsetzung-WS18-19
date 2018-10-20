<?php
	$year = '2018';
	$week = '42';
	echo("Montag: ");
	echo(date("d-m-Y", strtotime("{$year}-W{$week}-1")));
	echo("<br />\n");
	echo("Sonntag: ");
	echo(date("d-m-Y", strtotime("{$year}-W{$week}-7")));
?>