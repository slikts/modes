<?php
namespace modes;

function get_file_count($author = NULL, $deleted = FALSE, $tag = NULL, $tag_like = FALSE) {
    global $dbh;

    $query = $dbh->prepare('SELECT COUNT(*) FROM files');
    $query->execute();
    $result = $query->fetch();

    return $result[0];

    $sql = 'SELECT COUNT(*) FROM (SELECT a.md5
        FROM img a';
    if ($tag) {
		if ($tag_like) {
			$tag .= '%';
        }
        $tag = $dbh->quote($tag);
        $sql .= ' LEFT JOIN img_to_tag b
            ON b.img_md5 = a.md5';
		if (!$tag_like){
        	$where[] = "b.tag_title = $tag";
        } else {
			$where[] = "b.tag_title LIKE $tag";
        }
    }
    if (!$deleted) {
        $where[] = 'a.deleted IS NULL';
    }
    if ($author) {
        $author = $dbh->quote($author);
        $where[] = "a.created_by = $author OR a.restored_by = $author";
    }
    if ($where) $sql .= ' WHERE ' . join(' AND ', $where);

	$sql .= ' GROUP BY a.md5) AS q';
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch();
    return $result[0];
}

function get_total_file_size() {
    $du = array();
    exec('/usr/bin/du ' . FILES_DIR, $du);
    $total = explode("\t", $du[0]);
    return $total[0];
}

function get_new_file_id() {
    global $dbh;

    $sql = 'SELECT nextval(\'img_seq\')';
    $query = $dbh->query($sql);
    return $query->fetchColumn();
}

function format_file_id($id) {
    return base_convert(intval($id), 10, 32);
}

function get_file_by_hash($md5)
{
    global $dbh;

    $query = $dbh->prepare('SELECT * FROM files WHERE md5 = ?');
    $result = $query->execute(array($md5));

    return $query->fetchColumn();
}