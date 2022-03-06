<?php

namespace modules\Fight\Port\Adapter\Persistence;

use modules\common\DbInterface;
use modules\Fight\Application\Exceptions\RepositoryException;
use modules\Fight\Domain\Fight;
use modules\Fight\Domain\Fighter;
use modules\Fight\Domain\FightFilter;
use modules\Fight\Domain\FightRepositoryInterface;
use modules\Fight\Domain\Skill;
use modules\Fight\Domain\SkillCollection;

class FightRepository implements FightRepositoryInterface
{
    private DbInterface $connection;

    public function __construct(DbInterface $connection)
    {
        $this->connection = $connection;
    }

    public function updateFightHistoryStatus(int $fightId, string $status)
    {
        $sql = "UPDATE fights_history SET FightStatus = :status WHERE FightsHistoryId = :fightId LIMIT 1";
        $query = $this->connection->prepare($sql);
        $query->bindParam(':status', $status);
        $query->bindParam(':fightId', $fightId);

        $query->execute();
    }

    public function updateFightOpponents(int $fightId, $opponent)
    {
        $sql = "UPDATE fight_opponents SET RemainingHealth = :health, Outcome = :outcome WHERE FighterId = :fighterId AND FightsHistoryId = :fightId";
        $query = $this->connection->prepare($sql);
        $health = $opponent->getHealth();
        $outcome = $opponent->getBattleStatus();
        $fighterId = $opponent->getFighterId();

        $query->bindParam(':health', $health);
        $query->bindParam(':outcome', $outcome);
        $query->bindParam(':fighterId', $fighterId, \PDO::PARAM_INT);
        $query->bindParam(':fightId', $fightId, \PDO::PARAM_INT);

        $query->execute();
    }

    public function getSkills(array $skillsIds)
    {
        $in = str_repeat('?,', count($skillsIds) - 1) . '?';
        $sql = "SELECT * FROM skills WHERE SkillId IN ($in)";
        $query = $this->connection->prepare($sql);
        $query->execute($skillsIds);

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function pickFights(FightFilter $filter): array
    {
        $status = $filter->getStatus();
        $fightId = $filter->getFightId();
        $limit = $filter->getLimit();

        $sql = "SELECT * FROM fights_history WHERE FightStatus = :status";
        if ($fightId) {
            $sql .= " AND FightHistoryId = :fightId";
        }
        $sql .= " LIMIT :limit";
        $query = $this->connection->prepare($sql);
        $query->bindParam(':status', $status);
        if ($fightId) {
            $query->bindParam(':fightId', $status, \PDO::PARAM_INT);
        }
        $query->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($result)) {
            return [];
        }

        $fights = [];
        foreach ($result as $item) {
            $fights[] = [
                'fightDetails' => $item,
                'opponents' => $this->getFightOpponents($item['FightsHistoryId'])
            ];
        }

        return $fights;
    }

    /**
     * @throws RepositoryException
     */
    public function enqueueFight(Fight $fight): int
    {
        $query = $this->connection->prepare("INSERT INTO fights_history (FightStatus, CreatedAt) VALUES (:status, :created)");
        $status = $fight->getStatus();
        $created = $fight->getDate()->format('Y-m-d H:i:s');
        $query->bindParam(':status', $status);
        $query->bindParam(':created', $created);

        try {
            $query->execute();
            $fightId = $this->connection->lastInsertId();
            /** @var Fighter $opponent */
            foreach ($fight->getOpponents() as $opponent) {
                $this->registerOpponent($fightId, $opponent);
            }
        } catch (\Exception $e) {
            throw new RepositoryException('Could not save new fight');
        }

        return $fightId;
    }

    /**
     * @throws RepositoryException
     */
    public function saveFighter(Fighter $fighter): int
    {
        $query = $this->connection->prepare("INSERT INTO fighters (Health, Strength, Defence, Speed, Luck)
            VALUES (:health, :strength, :defence, :speed, :luck)");
        $health = $fighter->getHealth();
        $strength = $fighter->getStrength();
        $defence = $fighter->getDefence();
        $speed = $fighter->getSpeed();
        $luck = $fighter->getLuck();

        $query->bindParam(':health', $health, \PDO::PARAM_INT);
        $query->bindParam(':strength', $strength, \PDO::PARAM_INT);
        $query->bindParam(':defence', $defence, \PDO::PARAM_INT);
        $query->bindParam(':speed', $speed, \PDO::PARAM_INT);
        $query->bindParam(':luck', $luck, \PDO::PARAM_INT);

        try {
            $query->execute();
            $fighterId = $this->connection->lastInsertId();

            if ($fighter->getSkills()->hasObjects()) {
                $this->saveSkills($fighterId, $fighter->getSkills());
            }

            return $fighterId;
        } catch (\Exception $e) {
            throw new RepositoryException($e->getMessage());
        }
    }

    private function registerOpponent(int $fightId, Fighter $fighter): int
    {
        $query = $this->connection->prepare("
                INSERT INTO 
                    fight_opponents 
                        (FighterId, FightsHistoryId, RemainingHealth, Outcome) 
                VALUES 
                    (:fighterId, :fightId, :health, :outcome)
                        ");
        $fighterId = $fighter->getFighterId();
        $health = $fighter->getHealth();
        $outcome = $fighter->getBattleStatus();

        $query->bindParam(':fighterId', $fighterId, \PDO::PARAM_INT);
        $query->bindParam(':fightId', $fightId, \PDO::PARAM_INT);
        $query->bindParam(':health', $health, \PDO::PARAM_INT);
        $query->bindParam(':outcome', $outcome);
        $query->execute();

        return $this->connection->lastInsertId();
    }

    private function saveSkills(int $fighterId, SkillCollection $fighterSkills)
    {
        /** @var Skill $skill */
        foreach ($fighterSkills as $skill) {
            $this->addFighterSkill($fighterId, $skill->getSkillId());
        }
    }

    private function addFighterSkill(int $fighterId, int $skillId)
    {
        $query = $this->connection->prepare("INSERT INTO fighter_skills (FighterId, SkillId) VALUES (:fighterId, :skillId)");
        $query->bindParam(':fighterId', $fighterId, \PDO::PARAM_INT);
        $query->bindParam(':skillId', $skillId, \PDO::PARAM_INT);
        $query->execute();
    }

    private function getFightOpponents(int $fightId): array
    {
        $query = $this->connection->prepare("
                SELECT
                    fighters.*
                FROM
                    fighters
                INNER JOIN
                    fight_opponents ON fight_opponents.FighterId = fighters.FighterId
                WHERE
                    fight_opponents.FightsHistoryId = :fightHistoryId
	            ");
        $query->bindParam(':fightHistoryId', $fightId, \PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(\PDO::FETCH_ASSOC);

        $opponents = [];

        foreach ($result as $item) {
            $skills = $this->getFighterSkills($item['FighterId']);
            $item['Skills'] = $skills;
            $opponents[] = $item;
        }
        return $opponents;
    }

    private function getFighterSkills(int $fighterId): array
    {
        $query = $this->connection->prepare("
                SELECT 
                    skills.* 
                FROM 
                    skills 
                INNER JOIN 
                    fighter_skills ON skills.SkillId = fighter_skills.SkillId 
                WHERE
                    fighter_skills.FighterId = :fighterId");
        $query->bindParam(':fighterId', $fighterId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
}