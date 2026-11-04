<?php
declare(strict_types=1);

/**
 * Fetch and render article preview rows using PDO prepared statements.
 *
 * @param PDO $pdo
 * @param array{
 *   category?: string,
 *   excludeCategory?: string,
 *   orderDir?: 'ASC'|'DESC',
 *   limit?: int|null,
 *   basePrefix?: string,
 *   dateMode?: 'full'|'empty'|'none',
 *   showReadMore?: bool
 * } $options
 */
function renderArticleList(PDO $pdo, array $options = []): void
{
    $category = $options['category'] ?? null;
    $excludeCategory = $options['excludeCategory'] ?? null;
    $orderDir = ($options['orderDir'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
    $limit = $options['limit'] ?? null;
    $basePrefix = $options['basePrefix'] ?? '';
    $dateMode = $options['dateMode'] ?? 'full';
    $showReadMore = $options['showReadMore'] ?? true;

    $whereClauses = [];
    $params = [];

    if ($category !== null) {
        $whereClauses[] = 'kategorija = :category';
        $params[':category'] = $category;
    }
    if ($excludeCategory !== null) {
        $whereClauses[] = 'kategorija != :excludeCategory';
        $params[':excludeCategory'] = $excludeCategory;
    }

    $whereSql = $whereClauses !== [] ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
    $limitSql = is_int($limit) && $limit > 0 ? 'LIMIT :limit' : '';

    $sql = sprintf(
        'SELECT ID, naslov, kraj, DATE_FORMAT(datum, "%%d. %%m. %%Y") AS lepdatum, preview, naslovnaslika
         FROM clanki
         %s
         ORDER BY datum %s, ID %s
         %s',
        $whereSql,
        $orderDir,
        $orderDir,
        $limitSql
    );

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    if (is_int($limit) && $limit > 0) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();

    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $id = (int) $row['ID'];
        $naslov = htmlspecialchars((string) $row['naslov'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $krajRaw = trim((string) ($row['kraj'] ?? ''));
        $datum = htmlspecialchars((string) $row['lepdatum'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $preview = (string) ($row['preview'] ?? '');
        $coverRaw = trim((string) ($row['naslovnaslika'] ?? ''));

        $articleUrl = htmlspecialchars($basePrefix . 'clanki/' . $id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        echo '<div class="novica row align-items-start">';
        echo '<div class="col-md-6">';
        echo '<a href="' . $articleUrl . '"><h2>' . $naslov . '</h2></a>';

        if ($dateMode === 'full') {
            $datumText = $krajRaw === ''
                ? $datum
                : htmlspecialchars($krajRaw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . ', ' . $datum;
            echo '<div class="datum">' . $datumText . '</div>';
        } elseif ($dateMode === 'empty') {
            echo '<div class="datum"></div>';
        }

        if ($showReadMore) {
            echo '<p>' . $preview . ' &nbsp;<a href="' . $articleUrl . '" class="povezava">Preberi več...</a></p>';
        } else {
            echo '<p>' . $preview . '</p>';
        }

        echo '</div>';
        echo '<div class="col-md-6">';
        if ($coverRaw !== '') {
            $coverUrl = htmlspecialchars($basePrefix . 'clanki/' . $id . '/' . rawurlencode($coverRaw), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            echo '<a href="' . $articleUrl . '"><img src="' . $coverUrl . '" alt="' . $naslov . '" loading="lazy"></a>';
        }
        echo '</div></div>';
    }
}

