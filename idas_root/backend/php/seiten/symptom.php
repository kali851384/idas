<?php
require_once "../includes/db_config.php";

$sql = "SELECT symptom_id, name FROM symptome ORDER BY name";
$result = mysqli_query($conn, $sql);

// Falls Formular schon abgeschickt wurde
$selected = $_POST['kategorien'] ?? [];
?>

<form method="post">

<select name="kategorien[]" multiple size="10">
<?php
while ($row = mysqli_fetch_assoc($result)) {

    $id = $row['symptom_id'];
    $name = $row['name'];

    $isSelected = in_array($id, $selected) ? "selected" : "";

    echo "<option value='$id' $isSelected>$name</option>";
}
?>
</select>

<br><br>
<button type="submit">Absenden</button>

</form>
