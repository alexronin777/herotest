CREATE TABLE IF NOT EXISTS `fights_history`
(
    `FightsHistoryId` int(11)                     NOT NULL AUTO_INCREMENT,
    `FightStatus`     enum ('PENDING','COMPLETE') NOT NULL DEFAULT 'PENDING',
    `CreatedAt`       timestamp                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`FightsHistoryId`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;