<?php

$aFoundMessages = $oMailbox->search('FROM summer.glau@example.org SINCE ' . date('d-M-Y', time() - 60*60*24*7));
