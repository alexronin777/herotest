CREATE TABLE IF NOT EXISTS `fight_opponents`
(
    `FightOpponentsId` int(11)                              NOT NULL AUTO_INCREMENT,
    `FighterId`        int(11)                              NOT NULL,
    `FightsHistoryId`  int(11)                              NOT NULL,
    `RemainingHealth`  decimal(10, 0)                       NOT NULL,
    `Outcome`          enum ('WINNER','LOOSER','UNDECIDED') NOT NULL DEFAULT 'UNDECIDED',
    PRIMARY KEY (`FightOpponentsId`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;