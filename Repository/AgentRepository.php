<?php

class AgentRepository
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAgentById(int $agentId): bool|array
    {
        $query = <<<SQL
            SELECT * FROM agencies WHERE id = :agentId
        SQL;
        $statement = $this->db->prepare($query);
        $statement->execute(['agentId' => $agentId]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function getAgents(): bool|array
    {
        $query = <<<SQL
            SELECT * FROM agencies
        SQL;
        $statement = $this->db->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
