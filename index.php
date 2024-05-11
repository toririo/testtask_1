<?php
define('MYSQL_HOST', 'mysql');
define('MYSQL_USER', $_ENV['MYSQL_USER']);
define('MYSQL_PASSWORD', $_ENV['MYSQL_PASSWORD']);
define('MYSQL_DB', $_ENV['MYSQL_DATABASE']);

$conn = new PDO('mysql:host='.MYSQL_HOST.';port=3306;dbname='.MYSQL_DB, MYSQL_USER, MYSQL_PASSWORD);


require_once 'RuleManager.php';
require_once 'Repository/RuleRepository.php';
require_once 'Repository/ConditionRepository.php';
require_once 'Repository/HotelRepository.php';
require_once 'Repository/AgentRepository.php';

// Создаем экземпляр класса RuleManager
$ruleManager = new RuleManager(new RuleRepository($conn), new ConditionRepository($conn), new HotelRepository($conn), new AgentRepository($conn));

// Получаем идентификатор отеля из GET-параметров
$hotel_id = $_GET['hotel_id'] ?? 1;

// Проверяем правила для отеля
$ruleManager->checkHotelRules($hotel_id);
