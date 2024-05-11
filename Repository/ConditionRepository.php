<?php


class ConditionRepository
{
    protected PDO $db;
    protected array $validConditionTypes = [
        'country' => 'integer',
        'city' => 'integer',
        'stars' => 'integer',
        'commission' => 'integer',
        'is_default' => 'boolean',
        'company' => 'integer',
        'blacklist' => 'boolean',
        'recommendation' => 'boolean',
        'whitelist' => 'boolean',
    ];
    protected array $validComparisonOperators = ['equal', 'not_equal', 'greater_than', 'less_than'];

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getConditionsForRule($ruleId): bool|array
    {
        $query = <<<SQL
            SELECT * FROM rule_conditions WHERE rule_id = :ruleId
        SQL;
        $statement = $this->db->prepare($query);
        $statement->execute(['ruleId' => $ruleId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCondition($ruleId, $conditionType, $comparisonOperator, $value): void
    {
        // Проверка на допустимые значения condition_type и comparison_operator
        if (!isset($this->validConditionTypes[$conditionType])) {
            throw new InvalidArgumentException("Не верный тип: $conditionType");
        }

        if (!in_array($comparisonOperator, $this->validComparisonOperators)) {
            throw new InvalidArgumentException("Не верный оператор: $comparisonOperator");
        }

        // Проверка типа значения
        $expectedType = $this->validConditionTypes[$conditionType];
        if ($expectedType === 'integer' && !is_int($value)) {
            $value = (int) $value;
        } elseif ($expectedType === 'boolean') {
            $value = (bool) $value;
        }

        $query = <<<SQL
            INSERT INTO rule_conditions (rule_id, condition_type, comparison_operator, value)
                  VALUES (:ruleId, :conditionType, :comparisonOperator, :value)
        SQL;

        $statement = $this->db->prepare($query);
        $statement->execute([
            'ruleId' => $ruleId,
            'conditionType' => $conditionType,
            'comparisonOperator' => $comparisonOperator,
            'value' => $value
        ]);
    }

    public function getTyps(): array
    {
        return $this->validConditionTypes;
    }

    public function getOperators(): array
    {
        return $this->validComparisonOperators;
    }
}
