<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "xhtml11.dtd">
<html>
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8" />
    <TITLE>График Температур</TITLE>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include('settings.php') ?>
    <?php include('functions.php') ?>
    <div id="left_column">
        <figure id="temperature_chamber">
            <h2>Темп. в камере</h2>
            <div class="mini_chart"></div>
        </figure>
        <figure id="humidity">
            <h2>Влажность в камере</h2>
            <div class="mini_chart"></div>
        </figure>
        <figure id="temperature_processing">
            <h2>Темп. подачи</h2>
            <div class="mini_chart"></div>
        </figure>
        <figure id="temperature_flow">
            <h2>Темп. обработки</h2>
            <div class="mini_chart"></div>
        </figure>
    </div>
    <div id="center_column">
        <figure id="chart" data-chart-limit="10"
            <?php foreach(getRecords('temperature', 10) as $number => $_data): ?>
                <?php foreach($_data as $name => $value) echo "data-chart-$name-$number='$value' "; ?>
            <?php endforeach; ?>
            ></figure>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="chart.js"></script>
    <script src="script.js"></script>
</body>