-- Align clanki.mapa with numeric primary key IDs (clanki/<ID>/ media folders)
-- and update legacy internal article link in post ID 76

UPDATE `clanki`
SET `mapa` = CAST(`ID` AS CHAR);

UPDATE `clanki`
SET `preview` = REPLACE(`preview`, '../clanki/pevski-vikend-2020/pevski-vikend-2020.php', '../clanki/76')
WHERE `ID` = 76;

