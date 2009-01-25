<?php

/*
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
	PDF output driver.
	Extends the LaTeX driver and convert the resulting LaTeX to PDF using pdflatex,
	then send it to the browser with the correct mime type.
*/

class weePDFOutput extends weeLaTeXOutput
{
	/**
		Resulting PDF filename.
	*/

	protected $sFilename = 'file.pdf';

	/**
		Options sent to PDFLaTeX.
	*/

	protected $sOptions = '';

	/**
		Initialize the output driver.
		This driver always enable output buffering regardless of the setting.
	*/

	public function __construct($aParams = array())
	{
		parent::__construct(array('buffer' => true) + $aParams);
	}

	/**
		Fetch the buffered LaTeX, convert it to PDF and echo it.
		It will then be handled by the weeOutput defined ob callback.

		TODO: do not execute this function if the script is terminating following an error or an exception
	*/

	public function __destruct()
	{
		// Store LaTeX output in a temporary file

		$sTmpFilename = tempnam(null, null);
		file_put_contents($sTmpFilename, ob_get_contents());
		ob_end_clean();

		// Convert it to PDF

		$sTmpDir = sys_get_temp_dir();
		chdir($sTmpDir);

		$sPdfLatex = 'pdflatex ' . $this->sOptions . ' ' . $sTmpFilename;
		exec($sPdfLatex . ' > ' . $sTmpDir . '/pdflatex1.log');
		exec($sPdfLatex . ' > ' . $sTmpDir . '/pdflatex2.log');

		// TODO: Throw an exception or something if the PDF generation isnt completed

		// Send the PDF to the browser

		safe_header('Content-Type: application/pdf');
		safe_header('Content-Length: ' . filesize($sTmpFilename . '.pdf'));
		safe_header('Content-Disposition: attachment; filename="' . $this->sFilename . '"');

		readfile($sTmpFilename . '.pdf');

		// Cleanup the temporary directory

		exec('rm ' . $sTmpFilename . '*');
	}

	/**
		Set the resulting PDF filename.

		@param $sPDFFilename PDF filename, including the ".pdf" part.
	*/

	public function setFilename($sPDFFilename)
	{
		$this->sFilename = $sPDFFilename;
		return $this;
	}

	/**
		Set options to be given to PDFLaTeX.

		@param $sOptions The options sent to PDFLaTeX (default: none).
		@see man pdflatex
	*/

	public function setOptions($sOptions)
	{
		$this->sOptions = $sOptions;
		return $this;
	}
}
