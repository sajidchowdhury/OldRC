<?php 

// Start the session if it's not already started
if(!isset($_SESSION)){
	session_start();
}

// Set the session timestamp when the user logs in
if(!isset($_SESSION['login_timestamp'])) {
	$_SESSION['login_timestamp'] = time();
}

date_default_timezone_set('Asia/Dhaka');

// Check whether the session variable SESS_MEMBER_ID is present or not
if(!isset($_SESSION['NEWERP_SESS_MEMBER_ID'])) {
	header("location: https://erp.remotecenter.com.bd");
	exit();
}

// Check if the user has been inactive for 20 minutes
if (time() - $_SESSION['login_timestamp'] > 3600) {
	// Destroy the session and redirect to the login page
	session_destroy();
	header("location: https://erp.remotecenter.com.bd");
	exit();
}

// Update the login timestamp
$_SESSION['login_timestamp'] = time();
