<?php
// base directory
$base = "public_html/index.php";
// log file
$log = "/var/log/ee.related.log";
// set the request to a page that uses the related module so EE will handle all the loading for us
// there's probably a better way to do this.
$_SERVER['PATH_INFO'] = "/path/to/template";
$_SERVER['REQUEST_URI'] = $_SERVER['PATH_INFO'];

ob_start();
chdir(dirname($base));
//Main CI index.php file
require($base);
$output = ob_get_contents();
ob_end_clean();
$ee =& get_instance();
$related = new Related();
$related->field_id = 34;
$related->_checkCache();
$query = $ee->db->query("SELECT GET_LOCK('cache',0) as locked");
$cache = $query->row();
$result = "";
if($cache->locked == 1) {
	if(!empty($related->deleted) || !empty($related->added) || !empty($related->expired)) {
		try {
			$success = $related->_generateCache();
			if($success) {
				$result = date('[D m d H:i:s]',time()) . " DELETED ". count($related->deleted) . " entries\n";
				$result .= date('[D m d H:i:s]',time()) . " UPDATED ". count($related->expired) . " entries\n";
				$result .= date('[D m d H:i:s]',time()) . " ADDED ". count($related->added) . " entries\n";
			} else {
				throw new Exception('Cache failed!');
			}
		} catch (Exception $e) {
	   		$result = date('[D m d H:i:s]',time()) . ' ERROR: '.  $e->getMessage(). "\n";
		}
	} else {
		echo "Cache up to date\n";
	}
	$query = $ee->db->query("SELECT RELEASE_LOCK('cache')");
} else {
	$result = date('[D m d H:i:s]',time()) . ' ERROR: Cache is already processing'. "\n";
}
$fp = fopen($log, 'a');
fwrite($fp, $result);
fclose($fp);
?>
