<?php
set_time_limit(0);
define('INDEXLOCATION', dirname(__FILE__) . '/index/');
define('DOCUMENTLOCATION', dirname(__FILE__) . '/documents/');

include_once './classes/indexer.class.php';
include_once './classes/searcher.class.php';
include_once './classes/index.class.php';
include_once './classes/documentstore.class.php';

$index = new index();
$docstore = new documentstore();
$indexer = new indexer($index, $docstore);
$search = new searcher($index, $docstore);

$indexer->index(array('Setting the AuthzUserAuthoritative directive explicitly to Off allows for user authorization to be passed on to lower level modules (as defined in the modules.c files) if there is no user matching the supplied userID.'));
$indexer->index(array('The Allow directive affects which hosts can access an area of the server. Access can be controlled by hostname, IP address, IP address range, or by other characteristics of the client request captured in environment variables.'));
$indexer->index(array('This directive allows access to the server to be restricted based on hostname, IP address, or environment variables. The arguments for the Deny directive are identical to the arguments for the Allow directive.'));
$indexer->index(array('The Order directive, along with the Allow and Deny directives, controls a three-pass access control system. The first pass processes either all Allow or all Deny directives, as specified by the Order directive. The second pass parses the rest of the directives (Deny or Allow). The third pass applies to all requests which do not match either of the first two.'));
$indexer->index(array('The AuthDBDUserPWQuery specifies an SQL query to look up a password for a specified user.  The users ID will be passed as a single string parameter when the SQL query is executed.  It may be referenced within the query statement using a %s format specifier.'));
$indexer->index(array('The AuthDBDUserRealmQuery specifies an SQL query to look up a password for a specified user and realm. The users ID and the realm, in that order, will be passed as string parameters when the SQL query is executed.  They may be referenced within the query statement using %s format specifiers.'));
?>
