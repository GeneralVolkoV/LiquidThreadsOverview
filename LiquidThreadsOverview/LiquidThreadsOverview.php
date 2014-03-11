<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/LiquidThreadsOverview/LiquidThreadsOverview.php" );
EOT;
	exit( 1 );
}
 
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'LiquidThreadsOverview',
	'author' => 'VolkoV',
	'url' => 'http://www.garetien.de',
	'descriptionmsg' => 'lto-desc',
	'version' => '0.1.0',
);
 
$dir = dirname(__FILE__) . '/';
 
$wgAutoloadClasses['SpecialLiquidThreadsOverview'] = $dir . 'SpecialLiquidThreadsOverview.php';
$wgExtensionMessagesFiles['LiquidThreadsOverview'] = $dir . 'LiquidThreadsOverview.i18n.php'; 
$wgExtensionMessagesFiles['LiquidThreadsOverviewAlias'] = $dir . 'LiquidThreadsOverview.alias.php'; 
$wgSpecialPages['LiquidThreadsOverview'] = 'SpecialLiquidThreadsOverview';

$wgSpecialPageGroups['LiquidThreadsOverview'] = 'wiki';