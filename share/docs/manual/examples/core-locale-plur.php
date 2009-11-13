<?php

// Switch to the french language
setlocale(LC_ALL, 'fr_FR');

// Plural translation
// Echoes the first or second parameter's translation depending on $iNumberOfPonies
echo _T('Singular version: %d pony.', 'Plural version: %d ponies.', $iNumberOfPonies);

// You can also use sprintf to format the string
echo sprintf(_T('Singular version: %d pony.', 'Plural version: %d ponies.', $iNumberOfPonies), $iNumberOfPonies);
