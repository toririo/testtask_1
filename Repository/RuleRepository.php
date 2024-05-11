<?php

class RuleRepository
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllRules(): bool|array
    {
        $query = <<<SQL
            SELECT * FROM rules
        SQL;
        $result = $this->db->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRulesById($ruleId): bool|array
    {
        $query = <<<SQL
            SELECT * FROM rules WHERE id = :ruleId
        SQL;
        $statement = $this->db->prepare($query);
        $statement->execute(['ruleId' => $ruleId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addRule($name, $clientId, $managerText, $isActive): bool|string
    {
        $query = <<<SQL
            INSERT INTO rules (name, client_id, manager_text, is_active)
                  VALUES (:name, :clientId, :managerText, :isActive)
        SQL;

        $statement = $this->db->prepare($query);
        $statement->execute([
            'name' => $name,
            'clientId' => $clientId,
            'managerText' => $managerText,
            'isActive' => $isActive ? 1 : 0
        ]);

        return $this->db->lastInsertId();
    }

    public function getAllRulesWithConditions(): array
    {
        $query = <<<SQL
            SELECT r.*, rc.* 
                FROM rule_conditions rc
                INNER JOIN rules r ON r.id = rc.rule_id and r.is_active = 1
        SQL;
        $stmt = $this->db->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rules = [];
        foreach ($results as $row) {
            $ruleId = $row['rule_id'];
            if (!isset($rules[$ruleId])) {
                $rules[$ruleId] = [
                    'id' => $row['rule_id'],
                    'name' => $row['name'],
                    'client_id' => $row['client_id'],
                    'manager_text' => $row['manager_text'],
                    'is_active' => $row['is_active'],
                    'conditions' => []
                ];
            }

            if ($row['condition_type'] !== null) {
                $rules[$ruleId]['conditions'][] = [
                    'condition_type' => $row['condition_type'],
                    'comparison_operator' => $row['comparison_operator'],
                    'value' => $row['value']
                ];
            }
        }

        return array_values($rules);
    }
}
