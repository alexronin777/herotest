<?php

namespace modules\Fight\Domain;

class SkillTypes
{
    const ATTACK_TYPE = 'ATTACK';
    const DEFENCE_TYPE = 'DEFENCE';
    const HEALTH_TYPE = 'HEALTH';
    const STRENGTH_TYPE = 'STRENGTH';
    const SPEED_TYPE = 'SPEED';
    const LUCK_TYPE = 'LUCK';
    const TURN_TYPE = 'TURN';

    const SKILL_TYPES = [self::ATTACK_TYPE, self::DEFENCE_TYPE, self::HEALTH_TYPE, self::STRENGTH_TYPE, self::SPEED_TYPE, self::LUCK_TYPE, self::TURN_TYPE];
}