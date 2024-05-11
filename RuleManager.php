<?php

class RuleManager
{
    protected RuleRepository $ruleRepository;
    protected ConditionRepository $conditionRepository;
    protected HotelRepository $hotelRepository;
    protected AgentRepository $agentRepository;


    public function __construct(
        RuleRepository $ruleRepository, ConditionRepository $conditionRepository,
        HotelRepository $hotelRepository, AgentRepository $agentRepository)
    {
        $this->ruleRepository = $ruleRepository;
        $this->conditionRepository = $conditionRepository;
        $this->hotelRepository = $hotelRepository;
        $this->agentRepository = $agentRepository;
    }

    public function checkHotelRules(int $hotelId): void
    {
        if (!$this->hotelRepository->getHotelById($hotelId)) {
            echo "Отель не найден";
            return;
        }
        $AgenciesRepository = '';
        // Получаем все правила
        $rules = $this->ruleRepository->getAllRulesWithConditions();

        foreach ($rules as $rule) {

            $allConditionsMet = true;
            $clientId = $rule['client_id'];

            $hotelData = $this->hotelRepository->getHotelByIdForAgency($hotelId, $clientId);
            $client = $this->agentRepository->getAgentById($clientId);
            foreach ($rule['conditions'] as $condition) {
                $isConditionMet = $this->checkConditionForHotel($hotelData, $condition);
                // Если условие не выполняется, выходим из цикла
                if (!$isConditionMet) {
                    $allConditionsMet = false;
                    break;
                }
            }

            // Если все условия выполнены, выводим информацию о применимом правиле
            if ($allConditionsMet) {
                echo "Агент ID: {$client['id']}  Имя: {$client['name']}<br>";
                echo "Текст для менеджера: <br> {$rule['manager_text']} (Правило '{$rule['name']}' для агентства '{$rule['client_id']}')<br><br>";
            }
        }
    }

    public function checkConditionForHotel(array $hotelData, array $condition): bool
    {
        $conditionType = $condition['condition_type'];
        $comparisonOperator = $condition['comparison_operator'];
        $value = $condition['value'];

        switch ($conditionType) {
            case 'country':
                $hotelValue = $hotelData['country_id'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'city':
                $hotelValue = $hotelData['city_id'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'stars':
                $hotelValue = $hotelData['stars'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'company':
                $hotelValue = $hotelData['company'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'commission':
                $commission = $hotelData['commission_percent'] ?? 0;
                $discount = $hotelData['discount_percent'] ?? 0;
                return $this->compareValues($commission, $comparisonOperator, $value) || $this->compareValues($discount, $comparisonOperator, $value);
                break;
            case 'is_default':
                $hotelValue = $hotelData['is_default'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'blacklist':
                $hotelValue = $hotelData['is_black'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'recommendation':
                $hotelValue = $hotelData['is_recomend'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            case 'whitelist':
                $hotelValue = $hotelData['is_white'];
                return $this->compareValues($hotelValue, $comparisonOperator, $value);
                break;
            default:
                // Если тип условия неизвестен, вернуть false
                return false;
        }
    }

    protected function compareValues($value1, $operator, $value2): bool
    {
        return match ($operator) {
            'equal' => $value1 == $value2,
            'not_equal' => $value1 != $value2,
            'greater_than' => $value1 > $value2,
            'less_than' => $value1 < $value2,
            default => false,
        };
    }
}
