<?php
/**
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:Mp3Handler Documentation
 *
 * @author Conrad Irwin
 * @copyright Copyright © 2010 Conrad.Irwin
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 3.0
 * @version 0.1
 */
$wgExtensionCredits['media'][] = array(
	'name'           => 'Mp3Handler',
	'author'         => 'Conrad Irwin',
	'url'            => 'http://mediawiki.org/wiki/Extension:Mp3Handler',
	'description'    => 'Handler for MP3 files and flash player, based on Extension:OggHandler and Extension:Mp3'
);

$wgMediaHandlers['audio/mp3'] = 'Mp3Handler';
$wgFileExtensions[] = 'mp3';

# Default $wgScriptPath/extensions/Mp3Handler/mp3player.swf
$wgMp3playerPath = false;

# Default $wgScriptPath/extensions/Mp3Handler/download.png
$wgMp3DownloadIcon = false;

# $1 is replaced by the url to the raw image, false disables the download 
# button.
$wgMp3DownloadPath = "$1";

class Mp3Handler extends MediaHandler {

	// All these compulsary methods just return default values because there are no parameters.
	function getParamMap() { return array(); }
	function validateParam( $name, $value ) { return false; }
	function makeParamString( $params ) { return ''; }
	function parseParamString( $string ) { return array(); }
	function normaliseParams( $file, &$params ) { return true; }
	function getImageSize( $file, $path ) { return false; }

	// Prevent "no higher resolution" message.
	function mustRender( $file ) { return true; }

	function doTransform ( $file, $dstPath, $dstUrl, $params, $flags = 0 ) {
		return new Mp3Output( $file->getUrl () );
	}
}

class Mp3Output extends MediaTransformOutput {

	function __construct( $path ){
		$this->path = $path;
	}

	function toHtml( $options = array() ) {
		global $wgMp3playerPath, $wgMp3DownloadPath, $wgMp3DownloadIcon, $wgScriptPath;

		if ( $wgMp3playerPath === false ) {
			$wgMp3playerPath = $wgScriptPath . "/extensions/Mp3Handler/mp3player.swf";
		}
		if ( $wgMp3DownloadIcon === false ) {
			$wgMp3DownloadIcon = $wgScriptPath . "/extensions/Mp3Handler/download.png";
		}

		if ( $wgMp3DownloadPath === false ) {
			$download = "";
		} else {
			$down_url = str_replace( '$1', $this->path, $wgMp3DownloadPath );
			$download = <<<HTML
<a href="$down_url" title="Download">
	<img src="$wgMp3DownloadIcon" alt="Download" style="vertical-align: top; margin: 0">
</a>
HTML;
		}
		$path = $this->path;
		$url = "$wgMp3playerPath?mp3=".urlencode($path)."&amp;showinfo=1&amp;showvolume=1&amp;showslider=1&amp;width=250&amp;height=16&amp;showstop=0";
		return <<<HTML
<span class="mp3Wrapper">
<object style="vertical-align: top;" type="application/x-shockwave-flash" data="$url" width="250" height="16">
	<param name="wmode" value="transparent" />
	<param name="movie" value="$url" />
</object>
$download
</span>
HTML;
	}
}
