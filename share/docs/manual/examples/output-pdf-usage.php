<?php

// Create the PDF Output
$oPDFOutput = new weePDFOutput;

// Set the destination filename
$oPDFOutput->setFilename('my filename'); // No need for the extension

// Set options that will be passed to pdflatex on the command-line
$oPDFOutput->setOptions('-interaction nonstopmode');

// Finally, select the PDF Output
weeOutput::select($oPDFOutput);
