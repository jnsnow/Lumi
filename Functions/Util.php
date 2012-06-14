<?php /*DEL*/

/**
 * VERBOSITY declares the level of log messages you'd like to see.
 * 
 * 0 - Input and Output messages only.
 * 1 - Status messages and IO.
 * 2 - Detailed status messages and IO.
 * 3 - Debugging output and all messages. 
 */
define("VERBOSITY", "3");

/**
 * Produces a timestamped log message on the console.
 * 
 * @param string $str    The string to echo to the console.
 * @param string $prefix A small prefix to be echoed, the default is "--"
 * @param int $level     The verbosity level of this message, see VERBOSITY.
 * @return void          None.
 */
function _log($str, $prefix = "--", $level = 0) {

  if (VERBOSITY < $level)
    return;
  echo "[" . ts() . "] " . trim($prefix . " " . $str) . "\n";
}

/**
 * dlog sends a debug-level message to the console log.
 * 
 * @param String $str The message to print.
 */
function dlog($str) {

  _log($str, "--", 3);
}

/**
 * ilog sends an information-level message to the console log.
 * 
 * @param String $str The message to print.
 */
function ilog($str) {

  _log($str, "--", 2);
}

/**
 * slog sends a status-level message to the console log.
 * 
 * @param String $str The message to print.
 */
function slog($str) {

  _log($str, "--", 1);
}

/**
 * strip removes all color and style information from an IRC message.
 * 
 * @param String $str The string to remove the color information from.
 * @return String A copy of your string with all style codes removed from it.
 */
function strip($str) {

  return preg_replace(array(
              '/(\x03\d{0,2})/',
              '/(\x02)/',
              '/(\x0F)/',
              '/(\x1F)/',
              '/(\x16)/'), '', $str);
}

/**
 * ts() returns a timestamp for use with the console log.
 * 
 * @return string The timestamp.
 */
function ts() {

  $mtime = explode(' ', microtime());
  $msec = intval(substr($mtime[0], 2, -2));
  $sec = intval($mtime[1]);
  return date('H:i:s', $sec) . '.' . str_pad('' . $msec, 6, '0', STR_PAD_LEFT);
}
