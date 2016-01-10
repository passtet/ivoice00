<?php
function getRecords($table, $limit = null) {
    $conn = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASSWORD, DB_NAME);
    if (!$conn) throw new Exception("Connection to DB failed: " . mysqli_connect_error());

    $sql = "SELECT * FROM $table ORDER BY sec4 DESC";
    if($limit) $sql .= " LIMIT $limit";
    if (!$query = mysqli_query($conn, $sql)) throw new Exception("Error: " . $sql . "<br>" . mysqli_error($conn));
    while($res[] = mysqli_fetch_array($query));

    mysqli_close($conn);
    return $res;
}