<?php


class ConditionRepository
{
    protected PDO $db;
    protected array $validConditionTypes = [
        'country' => [
            'type' => 'integer',
            'operators' => [
                'equal',
                'not_equal'
            ]
        ],
        'city' => [
            'type' => 'integer',
            'operators' => [
                'equal',
                'not_equal'
            ]
        ],
        'stars' => [
            'type' => 'integer',
            'operators' => [
                'equal',
                'not_equal'
            ]
        ],
        'commission' => [
            'type' => 'integer',
            'operators' => [
                'equal',
                'not_equal',
                'greater_than',
                'less_than'
            ]
        ],
        'is_default' => [
            'type' => 'boolean',
            'operators' => [
                'equal',
            ]
        ],
        'company' => [
            'type' => 'integer',
            'operators' => [
                'equal',
                'not_equal'
            ]
        ],
        'blacklist' => [
            'type' => 'boolean',
            'operators' => [
                'equal',
            ],
        ],
        'recommendation' => [
            'type' => 'boolean',
            'operators' => [
                'equal',
            ],
        ],
        'whitelist' => [
            'type' => 'boolean',
            'operators' => [
                'equal',
            ],
        ],
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

        $expectedType = $this->validConditionTypes[$conditionType];
        if (!in_array($comparisonOperator, $expectedType['operators'])) {
            throw new InvalidArgumentException("Не верный оператор для данного типа");
        }

        // Проверка типа значения
        if ($expectedType['type'] === 'integer' && !is_int($value)) {
            $value = (int) $value;
        } elseif ($expectedType['type'] === 'boolean') {
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
