<?php 

class MimeTypeHelper {


	public static function from_extension($extension, $default='application/octet-stream') {
		
		switch(strtolower(str_replace('.', '', $extension))) {
		
			case 'ez': $mimetype = 'application/andrew-inset'; break;
			case 'hqx': $mimetype = 'application/mac-binhex40'; break;
			case 'cpt': $mimetype = 'application/mac-compactpro'; break;
			case 'doc': $mimetype = 'application/msword'; break;
			case 'bin': $mimetype = 'application/octet-stream'; break;
			case 'dms': $mimetype = 'application/octet-stream'; break;
			case 'lha': $mimetype = 'application/octet-stream'; break;
			case 'lzh': $mimetype = 'application/octet-stream'; break;
			case 'exe': $mimetype = 'application/octet-stream'; break;
			case 'class': $mimetype = 'application/octet-stream'; break;
			case 'so': $mimetype = 'application/octet-stream'; break;
			case 'dll': $mimetype = 'application/octet-stream'; break;
			case 'oda': $mimetype = 'application/oda'; break;
			case 'pdf': $mimetype = 'application/pdf'; break;
			case 'ai': $mimetype = 'application/postscript'; break;
			case 'eps': $mimetype = 'application/postscript'; break;
			case 'ps': $mimetype = 'application/postscript'; break;
			case 'smi': $mimetype = 'application/smil'; break;
			case 'smil': $mimetype = 'application/smil'; break;
			case 'wbxml': $mimetype = 'application/vnd.wap.wbxml'; break;
			case 'wmlc': $mimetype = 'application/vnd.wap.wmlc'; break;
			case 'wmlsc': $mimetype = 'application/vnd.wap.wmlscriptc'; break;
			case 'bcpio': $mimetype = 'application/x-bcpio'; break;
			case 'vcd': $mimetype = 'application/x-cdlink'; break;
			case 'pgn': $mimetype = 'application/x-chess-pgn'; break;
			case 'cpio': $mimetype = 'application/x-cpio'; break;
			case 'csh': $mimetype = 'application/x-csh'; break;
			case 'dcr': $mimetype = 'application/x-director'; break;
			case 'dir': $mimetype = 'application/x-director'; break;
			case 'dxr': $mimetype = 'application/x-director'; break;
			case 'dvi': $mimetype = 'application/x-dvi'; break;
			case 'spl': $mimetype = 'application/x-futuresplash'; break;
			case 'gtar': $mimetype = 'application/x-gtar'; break;
			case 'hdf': $mimetype = 'application/x-hdf'; break;
			case 'skp': $mimetype = 'application/x-koan'; break;
			case 'skd': $mimetype = 'application/x-koan'; break;
			case 'skt': $mimetype = 'application/x-koan'; break;
			case 'skm': $mimetype = 'application/x-koan'; break;
			case 'latex': $mimetype = 'application/x-latex'; break;
			case 'nc': $mimetype = 'application/x-netcdf'; break;
			case 'cdf': $mimetype = 'application/x-netcdf'; break;
			case 'sh': $mimetype = 'application/x-sh'; break;
			case 'shar': $mimetype = 'application/x-shar'; break;
			case 'swf': $mimetype = 'application/x-shockwave-flash'; break;
			case 'sit': $mimetype = 'application/x-stuffit'; break;
			case 'sv4cpio': $mimetype = 'application/x-sv4cpio'; break;
			case 'sv4crc': $mimetype = 'application/x-sv4crc'; break;
			case 'tar': $mimetype = 'application/x-tar'; break;
			case 'tcl': $mimetype = 'application/x-tcl'; break;
			case 'tex': $mimetype = 'application/x-tex'; break;
			case 'texinfo': $mimetype = 'application/x-texinfo'; break;
			case 'texi': $mimetype = 'application/x-texinfo'; break;
			case 't': $mimetype = 'application/x-troff'; break;
			case 'tr': $mimetype = 'application/x-troff'; break;
			case 'roff': $mimetype = 'application/x-troff'; break;
			case 'man': $mimetype = 'application/x-troff-man'; break;
			case 'me': $mimetype = 'application/x-troff-me'; break;
			case 'ms': $mimetype = 'application/x-troff-ms'; break;
			case 'ustar': $mimetype = 'application/x-ustar'; break;
			case 'src': $mimetype = 'application/x-wais-source'; break;
			case 'xhtml': $mimetype = 'application/xhtml+xml'; break;
			case 'xht': $mimetype = 'application/xhtml+xml'; break;
			case 'zip': $mimetype = 'application/zip'; break;
			case 'au': $mimetype = 'audio/basic'; break;
			case 'snd': $mimetype = 'audio/basic'; break;
			case 'mid': $mimetype = 'audio/midi'; break;
			case 'midi': $mimetype = 'audio/midi'; break;
			case 'kar': $mimetype = 'audio/midi'; break;
			case 'mpga': $mimetype = 'audio/mpeg'; break;
			case 'mp2': $mimetype = 'audio/mpeg'; break;
			case 'mp3': $mimetype = 'audio/mpeg'; break;
			case 'aif': $mimetype = 'audio/x-aiff'; break;
			case 'aiff': $mimetype = 'audio/x-aiff'; break;
			case 'aifc': $mimetype = 'audio/x-aiff'; break;
			case 'm3u': $mimetype = 'audio/x-mpegurl'; break;
			case 'ram': $mimetype = 'audio/x-pn-realaudio'; break;
			case 'rm': $mimetype = 'audio/x-pn-realaudio'; break;
			case 'rpm': $mimetype = 'audio/x-pn-realaudio-plugin'; break;
			case 'ra': $mimetype = 'audio/x-realaudio'; break;
			case 'wav': $mimetype = 'audio/x-wav'; break;
			case 'pdb': $mimetype = 'chemical/x-pdb'; break;
			case 'xyz': $mimetype = 'chemical/x-xyz'; break;
			case 'bmp': $mimetype = 'image/bmp'; break;
			case 'gif': $mimetype = 'image/gif'; break;
			case 'ief': $mimetype = 'image/ief'; break;
			case 'jpeg': $mimetype = 'image/jpeg'; break;
			case 'jpg': $mimetype = 'image/jpeg'; break;
			case 'jpe': $mimetype = 'image/jpeg'; break;
			case 'png': $mimetype = 'image/png'; break;
			case 'tiff': $mimetype = 'image/tiff'; break;
			case 'tif': $mimetype = 'image/tif'; break;
			case 'ico': $mimetype = 'image/x-icon'; break;
			case 'djvu': $mimetype = 'image/vnd.djvu'; break;
			case 'djv': $mimetype = 'image/vnd.djvu'; break;
			case 'wbmp': $mimetype = 'image/vnd.wap.wbmp'; break;
			case 'ras': $mimetype = 'image/x-cmu-raster'; break;
			case 'pnm': $mimetype = 'image/x-portable-anymap'; break;
			case 'pbm': $mimetype = 'image/x-portable-bitmap'; break;
			case 'pgm': $mimetype = 'image/x-portable-graymap'; break;
			case 'ppm': $mimetype = 'image/x-portable-pixmap'; break;
			case 'rgb': $mimetype = 'image/x-rgb'; break;
			case 'xbm': $mimetype = 'image/x-xbitmap'; break;
			case 'xpm': $mimetype = 'image/x-xpixmap'; break;
			case 'xwd': $mimetype = 'image/x-windowdump'; break;
			case 'igs': $mimetype = 'model/iges'; break;
			case 'iges': $mimetype = 'model/iges'; break;
			case 'msh': $mimetype = 'model/mesh'; break;
			case 'mesh': $mimetype = 'model/mesh'; break;
			case 'silo': $mimetype = 'model/mesh'; break;
			case 'wrl': $mimetype = 'model/vrml'; break;
			case 'vrml': $mimetype = 'model/vrml'; break;
			case 'css': $mimetype = 'text/css'; break;
			case 'html': $mimetype = 'text/html'; break;
			case 'htm': $mimetype = 'text/html'; break;
			case 'asc': $mimetype = 'text/plain'; break;
			case 'txt': $mimetype = 'text/plain'; break;
			case 'js': $mimetype = 'text/javascript'; break;
			case 'rtx': $mimetype = 'text/richtext'; break;
			case 'rtf': $mimetype = 'text/rtf'; break;
			case 'sgml': $mimetype = 'text/sgml'; break;
			case 'sgm': $mimetype = 'text/sgml'; break;
			case 'tsv': $mimetype = 'text/tab-seperated-values'; break;
			case 'wml': $mimetype = 'text/vnd.wap.wml'; break;
			case 'wmls': $mimetype = 'text/vnd.wap.wmlscript'; break;
			case 'etx': $mimetype = 'text/x-setext'; break;
			case 'xml': $mimetype = 'text/xml'; break;
			case 'xsl': $mimetype = 'text/xsl'; break;
			case 'mpeg': $mimetype = 'video/mpeg'; break;
			case 'mpg': $mimetype = 'video/mpeg'; break;
			case 'mpe': $mimetype = 'video/mpeg'; break;
			case 'qt': $mimetype = 'video/quicktime'; break;
			case 'mov': $mimetype = 'video/quicktime'; break;
			case 'mxu': $mimetype = 'video/vnd.mpegurl'; break;
			case 'avi': $mimetype = 'video/x-msvideo'; break;
			case 'movie': $mimetype = 'video/x-sgi-movie'; break;
			case 'ice': $mimetype = 'x-conference-xcooltalk'; break;
			default: $mimetype = $default; break;
		}
		
		return $mimetype;
	}
	
	
	public static function from_filename($filename, $default='application/octet-stream') {
		
		$parts = explode('.', $filename);
		
		if(count($parts) > 0) {
			return MimeTypeHelper::from_extension(array_pop($parts), $default);
		} else {
			return $default;
		}
		
	}
	
	
	public static function get_extension($mimetype, $default=null) {
		
		switch(strtolower($mimetype)) {
		
			case 'application/andrew-inset': $extension = 'ez'; break;
			case 'application/mac-binhex40': $extension = 'hqx'; break;
			case 'application/mac-compactpro': $extension = 'cpt'; break;
			case 'application/msword': $extension = 'doc'; break;
			case 'application/octet-stream': $extension = 'bin'; break;
			case 'application/octet-stream': $extension = 'dms'; break;
			case 'application/octet-stream': $extension = 'lha'; break;
			case 'application/octet-stream': $extension = 'lzh'; break;
			case 'application/octet-stream': $extension = 'exe'; break;
			case 'application/octet-stream': $extension = 'class'; break;
			case 'application/octet-stream': $extension = 'so'; break;
			case 'application/octet-stream': $extension = 'dll'; break;
			case 'application/oda': $extension = 'oda'; break;
			case 'application/pdf': $extension = 'pdf'; break;
			case 'application/postscript': $extension = 'ai'; break;
			case 'application/postscript': $extension = 'eps'; break;
			case 'application/postscript': $extension = 'ps'; break;
			case 'application/smil': $extension = 'smi'; break;
			case 'application/smil': $extension = 'smil'; break;
			case 'application/vnd.wap.wbxml': $extension = 'wbxml'; break;
			case 'application/vnd.wap.wmlc': $extension = 'wmlc'; break;
			case 'application/vnd.wap.wmlscriptc': $extension = 'wmlsc'; break;
			case 'application/x-bcpio': $extension = 'bcpio'; break;
			case 'application/x-cdlink': $extension = 'vcd'; break;
			case 'application/x-chess-pgn': $extension = 'pgn'; break;
			case 'application/x-cpio': $extension = 'cpio'; break;
			case 'application/x-csh': $extension = 'csh'; break;
			case 'application/x-director': $extension = 'dcr'; break;
			case 'application/x-director': $extension = 'dir'; break;
			case 'application/x-director': $extension = 'dxr'; break;
			case 'application/x-dvi': $extension = 'dvi'; break;
			case 'application/x-futuresplash': $extension = 'spl'; break;
			case 'application/x-gtar': $extension = 'gtar'; break;
			case 'application/x-hdf': $extension = 'hdf'; break;
			case 'application/x-koan': $extension = 'skp'; break;
			case 'application/x-koan': $extension = 'skd'; break;
			case 'application/x-koan': $extension = 'skt'; break;
			case 'application/x-koan': $extension = 'skm'; break;
			case 'application/x-latex': $extension = 'latex'; break;
			case 'application/x-netcdf': $extension = 'nc'; break;
			case 'application/x-netcdf': $extension = 'cdf'; break;
			case 'application/x-sh': $extension = 'sh'; break;
			case 'application/x-shar': $extension = 'shar'; break;
			case 'application/x-shockwave-flash': $extension = 'swf'; break;
			case 'application/x-stuffit': $extension = 'sit'; break;
			case 'application/x-sv4cpio': $extension = 'sv4cpio'; break;
			case 'application/x-sv4crc': $extension = 'sv4crc'; break;
			case 'application/x-tar': $extension = 'tar'; break;
			case 'application/x-tcl': $extension = 'tcl'; break;
			case 'application/x-tex': $extension = 'tex'; break;
			case 'application/x-texinfo': $extension = 'texinfo'; break;
			case 'application/x-texinfo': $extension = 'texi'; break;
			case 'application/x-troff': $extension = 't'; break;
			case 'application/x-troff': $extension = 'tr'; break;
			case 'application/x-troff': $extension = 'roff'; break;
			case 'application/x-troff-man': $extension = 'man'; break;
			case 'application/x-troff-me': $extension = 'me'; break;
			case 'application/x-troff-ms': $extension = 'ms'; break;
			case 'application/x-ustar': $extension = 'ustar'; break;
			case 'application/x-wais-source': $extension = 'src'; break;
			case 'application/xhtml+xml': $extension = 'xhtml'; break;
			case 'application/xhtml+xml': $extension = 'xht'; break;
			case 'application/zip': $extension = 'zip'; break;
			case 'audio/basic': $extension = 'au'; break;
			case 'audio/basic': $extension = 'snd'; break;
			case 'audio/midi': $extension = 'mid'; break;
			case 'audio/midi': $extension = 'midi'; break;
			case 'audio/midi': $extension = 'kar'; break;
			case 'audio/mpeg': $extension = 'mpga'; break;
			case 'audio/mpeg': $extension = 'mp2'; break;
			case 'audio/mpeg': $extension = 'mp3'; break;
			case 'audio/x-aiff': $extension = 'aif'; break;
			case 'audio/x-aiff': $extension = 'aiff'; break;
			case 'audio/x-aiff': $extension = 'aifc'; break;
			case 'audio/x-mpegurl': $extension = 'm3u'; break;
			case 'audio/x-pn-realaudio': $extension = 'ram'; break;
			case 'audio/x-pn-realaudio': $extension = 'rm'; break;
			case 'audio/x-pn-realaudio-plugin': $extension = 'rpm'; break;
			case 'audio/x-realaudio': $extension = 'ra'; break;
			case 'audio/x-wav': $extension = 'wav'; break;
			case 'chemical/x-pdb': $extension = 'pdb'; break;
			case 'chemical/x-xyz': $extension = 'xyz'; break;
			case 'image/bmp': $extension = 'bmp'; break;
			case 'image/gif': $extension = 'gif'; break;
			case 'image/ief': $extension = 'ief'; break;
			case 'image/jpeg': $extension = 'jpeg'; break;
			case 'image/jpeg': $extension = 'jpg'; break;
			case 'image/jpeg': $extension = 'jpe'; break;
			case 'image/png': $extension = 'png'; break;
			case 'image/tiff': $extension = 'tiff'; break;
			case 'image/tif': $extension = 'tif'; break;
			case 'image/vnd.djvu': $extension = 'djvu'; break;
			case 'image/vnd.djvu': $extension = 'djv'; break;
			case 'image/vnd.wap.wbmp': $extension = 'wbmp'; break;
			case 'image/x-cmu-raster': $extension = 'ras'; break;
			case 'image/x-portable-anymap': $extension = 'pnm'; break;
			case 'image/x-portable-bitmap': $extension = 'pbm'; break;
			case 'image/x-portable-graymap': $extension = 'pgm'; break;
			case 'image/x-portable-pixmap': $extension = 'ppm'; break;
			case 'image/x-rgb': $extension = 'rgb'; break;
			case 'image/x-xbitmap': $extension = 'xbm'; break;
			case 'image/x-xpixmap': $extension = 'xpm'; break;
			case 'image/x-windowdump': $extension = 'xwd'; break;
			case 'image/x-icon': $extension = 'ico'; break;
			case 'model/iges': $extension = 'igs'; break;
			case 'model/iges': $extension = 'iges'; break;
			case 'model/mesh': $extension = 'msh'; break;
			case 'model/mesh': $extension = 'mesh'; break;
			case 'model/mesh': $extension = 'silo'; break;
			case 'model/vrml': $extension = 'wrl'; break;
			case 'model/vrml': $extension = 'vrml'; break;
			case 'text/css': $extension = 'css'; break;
			case 'text/html': $extension = 'html'; break;
			case 'text/html': $extension = 'htm'; break;
			case 'text/plain': $extension = 'asc'; break;
			case 'text/plain': $extension = 'txt'; break;
			case 'text/javascript': $extension = 'js'; break;
			case 'text/richtext': $extension = 'rtx'; break;
			case 'text/rtf': $extension = 'rtf'; break;
			case 'text/sgml': $extension = 'sgml'; break;
			case 'text/sgml': $extension = 'sgm'; break;
			case 'text/tab-seperated-values': $extension = 'tsv'; break;
			case 'text/vnd.wap.wml': $extension = 'wml'; break;
			case 'text/vnd.wap.wmlscript': $extension = 'wmls'; break;
			case 'text/x-setext': $extension = 'etx'; break;
			case 'text/xml': $extension = 'xml'; break;
			case 'text/xsl': $extension = 'xsl'; break;
			case 'video/mpeg': $extension = 'mpeg'; break;
			case 'video/mpeg': $extension = 'mpg'; break;
			case 'video/mpeg': $extension = 'mpe'; break;
			case 'video/quicktime': $extension = 'qt'; break;
			case 'video/quicktime': $extension = 'mov'; break;
			case 'video/vnd.mpegurl': $extension = 'mxu'; break;
			case 'video/x-msvideo': $extension = 'avi'; break;
			case 'video/x-sgi-movie': $extension = 'movie'; break;
			case 'x-conference-xcooltalk': $extension = 'ice'; break;
			default: $extension = $default ? strtolower(str_replace('.','', $default)) : null; break;
		}
		
		return $extension ? ".{$extension}" : null;
		
	}
	
	//------------------------------------------------------------------------------------------------------------------------------
	
	public static function mimetype($value, $default='application/octet-stream'){
		
		if(!$value) return null;
		else if(strpos($value, '/') !== false) return strtolower(trim($value));
		else return MimeTypeHelper::from_extension($value, $default);
	}
	
	
}
