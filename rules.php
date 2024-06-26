<?php
define('MYSQL_HOST', 'mysql');
define('MYSQL_USER', $_ENV['MYSQL_USER']);
define('MYSQL_PASSWORD', $_ENV['MYSQL_PASSWORD']);
define('MYSQL_DB', $_ENV['MYSQL_DATABASE']);

$conn = new PDO('mysql:host='.MYSQL_HOST.';port=3306;dbname='.MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);

require_once 'Repository/RuleRepository.php';
require_once 'Repository/ConditionRepository.php';
require_once 'Repository/AgentRepository.php';


$ruleRepository = new RuleRepository($conn);
$conditionRepository = new ConditionRepository($conn);
$agentRepository = new AgentRepository($conn);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка данных из формы
    $name = $_POST['name'];
    $clientId = $_POST['client_id'];
    $managerText = $_POST['manager_text'];
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Добавление нового правила
    $ruleId = $ruleRepository->addRule($name, $clientId, $managerText, $isActive);

    // Обработка условий
    if (isset($_POST['conditions'])) {
        foreach ($_POST['conditions'] as $condition) {
            $conditionType = $condition['condition_type'];
            $comparisonOperator = $condition['comparison_operator'];
            $value = $condition['value'];
            $conditionRepository->addCondition($ruleId, $conditionType, $comparisonOperator, $value);
        }
    }

    header('Location: index.php');
    exit();
} else {
    $agents = $agentRepository->getAgents();
    $types = $conditionRepository->getTyps();
    $operators = $conditionRepository->getOperators();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить правило</title>
</head>
<body>
<h1>Добавить правило</h1>
<form method="post" action="">
    <label for="name">Имя:</label><br>
    <input type="text" id="name" name="name"><br><br>

    <label for="client_id">Client:</label><br>
    <select id="client_id" name="client_id">
        <?php foreach ($agents as $agent): ?>
            <option value="<?= $agent['id'] ?>"><?= $agent['name'] ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="manager_text">Текст для менеджера:</label><br>
    <textarea id="manager_text" name="manager_text"></textarea><br><br>

    <label for="is_active">Is Active:</label>
    <input type="checkbox" id="is_active" name="is_active" value="1"><br><br>

    <h2>Conditions:</h2>
    <div id="conditions">
    </div>
    <button type="button" onclick="addCondition()">Add Condition</button><br><br>

    <input type="submit" value="Submit">
</form>
<script>
    let condition = 0;
    // Динамичное добавление поля с условиями
    function addCondition() {

        var div = document.createElement('div');
        div.innerHTML = '<label for="condition_type">Condition Type:</label><br>\
                            <select name="conditions['+condition+'][condition_type]" onchange="handleValueType(this, '+condition+')" required>\
                                <option selected disabled>Выберите тип</option>\
                                <?php foreach ($types as $key => $type): ?>\
                                    <option data-type="<?= $type['type'] ?>" data-operators="<?= htmlspecialchars(json_encode($type['operators'])) ?>" value="<?= $key ?>"><?= $key ?></option>\
                                <?php endforeach; ?>\
                            </select><br><div id="'+condition+'"></div><br><br>';
        document.getElementById('conditions').appendChild(div);
        condition++;
    }

    function handleValueType(select, condition) {
        var inputBlock = document.getElementById(condition);
        var selectedType = select.options[select.selectedIndex].dataset.type;
        var selectedOperators = JSON.parse(select.options[select.selectedIndex].dataset.operators);
        var operatorHtml = "";
        var valueHtml = "";

        for (var i = 0; i < selectedOperators.length; i++) {
            operatorHtml += '<option value="' + selectedOperators[i] + '">' + selectedOperators[i] + '</option>';
        }

        if (selectedType === "integer") {
            valueHtml = '<input type="number" name="conditions[' + condition + '][value]" required><br><br>';
        } else if (selectedType === "boolean") {
            valueHtml = '<select name="conditions[' + condition + '][value]" required>' +
                '<option value="0">False</option>' +
                '<option value="1">True</option>' +
                '</select><br><br>';
        }

        inputBlock.innerHTML = '<label for="comparison_operator">Comparison Operator:</label><br>' +
            '<select name="conditions[' + condition + '][comparison_operator]" required>' + operatorHtml + '</select><br><br>' +
            '<label for="value">Value:</label><br>' + valueHtml;
    }
</script>
</body>
</html>
