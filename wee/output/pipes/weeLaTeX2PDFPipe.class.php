<?php

/**
	Web:Extend
	Copyright (c) 2006-2009 Dev:Extend

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details.

	You should have received a copy of the GNU Lesser General Public
	License along with this library; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ALLOW_INCLUSION')) die;

/**
	A LaTeX-to-PDF pipe.
*/

class weeLaTeX2PDFPipe extends weePipe
{
	/**
		Construct a new LaTeX to PDF pipe.

		This pipe accepts two parameters:
		 - filename:	the filename of the produced PDF file.
		 - options:		a string pass to pdflatex
		This pipe accepts a single parameter named "options", which should
		command line arguments to pdflatex.

		@param $aParams The pipe parameters.
	*/

	public function __construct($aParams = array())
	{
		$this->aParams = $aParams;
	}

	/**
		Return the MIME type of the producted output.

		@return	string application/pdf
		@see	http://www.iana.org/assignments/media-types/
	*/

	public function getMIMEType()
	{
	    return 'application/pdf';
	}

	/**
		Start buffering.
	*/

	public function init()
	{
		ob_start();
	}

	/**
		Process the pipe.

		This method should be called after the input of the pipe has been
		sent into the output buffer.
	*/

	public function process()
	{
		// Store LaTeX output in a temporary file

		$sTmpFilename = tempnam(null, null);
		file_put_contents($sTmpFilename, ob_get_clean());

		// Convert it to PDF

		$sTmpDir = sys_get_temp_dir();

		$sPdfLatex = 'cd ' . $sTmpDir . ' && pdflatex ' . array_value($this->aParams, 'options') . ' ' . $sTmpFilename;
		exec($sPdfLatex . ' > ' . $sTmpDir . '/pdflatex1.log');
		exec($sPdfLatex . ' > ' . $sTmpDir . '/pdflatex2.log');

		$iSize = @filesize($sTmpFilename . '.pdf');
		$iSize === false and burn('UnexpectedValueException',
			_WT('The conversion from LaTeX to PDF failed.'));

		// Send the PDF to the browser

		safe_header('Content-Length: ' . $iSize);
		readfile($sTmpFilename . '.pdf');

		// Cleanup the temporary directory

		exec('rm ' . $sTmpFilename . '*');
	}
}
