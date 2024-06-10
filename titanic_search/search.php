<?php
// Функция для загрузки данных из CSV файла
function load_data($filename) {
    $rows = array();
    if (($handle = fopen($filename, "r")) !== FALSE) {
        $header = fgetcsv($handle, 1000, ",");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $rows[] = array_combine($header, $data);
        }
        fclose($handle);
    }
    return $rows;
}

// Загрузка данных из файла
$data = load_data('titanic.csv');

// Поиск по возрасту
$age_filter = isset($_GET['age']) ? intval($_GET['age']) : null;
$age_results = array();
if ($age_filter !== null) {
    foreach ($data as $row) {
        if (isset($row['Age']) && intval($row['Age']) == $age_filter) {
            $age_results[] = $row;
        }
    }
}

// Поиск по имени с регулярным выражением
$name_filter = isset($_GET['name']) ? $_GET['name'] : null;
$name_results = array();
if ($name_filter !== null) {
    $pattern = "/$name_filter/i";
    foreach ($data as $row) {
        if (isset($row['Name']) && preg_match($pattern, $row['Name'])) {
            $name_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Titanic Search Results</title>
</head>
<body>
    <h1>Search Results</h1>

    <h2>Search by Age</h2>
    <?php if ($age_filter !== null): ?>
        <p>Age: <?php echo htmlspecialchars($age_filter); ?></p>
        <?php if (count($age_results) > 0): ?>
            <ul>
                <?php foreach ($age_results as $result): ?>
                    <li><?php echo htmlspecialchars($result['Name']); ?> (Age: <?php echo htmlspecialchars($result['Age']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No passengers found with age <?php echo htmlspecialchars($age_filter); ?>.</p>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Search by Name</h2>
    <?php if ($name_filter !== null): ?>
        <p>Name pattern: <?php echo htmlspecialchars($name_filter); ?></p>
        <?php if (count($name_results) > 0): ?>
            <ul>
                <?php foreach ($name_results as $result): ?>
                    <li><?php echo htmlspecialchars($result['Name']); ?> (Age: <?php echo htmlspecialchars($result['Age']); ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No passengers found with name matching "<?php echo htmlspecialchars($name_filter); ?>".</p>
        <?php endif; ?>
    <?php endif; ?>

    <h2>Search Form</h2>
    <form method="GET" action="search.php">
        <label for="age">Age:</label>
        <input type="number" id="age" name="age">
        <br>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name">
        <br>
        <input type="submit" value="Search">
    </form>
</body>
</html>