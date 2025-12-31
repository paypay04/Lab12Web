<?php
$id = $_GET['id'];
$sql = "DELETE FROM data_barang WHERE id_barang = '{$id}'";
$result = mysqli_query($conn, $sql);
if($result) {
    header('location: index.php?page=user/list');
} else {
    echo "Error: " . mysqli_error($conn);
}
?>