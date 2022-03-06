<?php

namespace modules\Fight\Domain;

class OutcomeStatus
{
    const OUTCOME_UNDECIDED = 'UNDECIDED';
    const OUTCOME_LOOSER = 'LOOSER';
    const OUTCOME_WINNER = 'WINNER';

    const OUTCOME_STATUS = [self::OUTCOME_UNDECIDED, self::OUTCOME_WINNER, self::OUTCOME_LOOSER];
}