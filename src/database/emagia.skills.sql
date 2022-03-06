CREATE TABLE IF NOT EXISTS `skills`
(
    `SkillId`     int(11)                                                    NOT NULL AUTO_INCREMENT,
    `Name`        varchar(100)                                               NOT NULL,
    `Description` varchar(255)                                               NOT NULL,
    `Type`        enum ('HEALTH','STRENGTH','DEFENCE','SPEED','LUCK','TURN') NOT NULL DEFAULT 'HEALTH',
    `Probability` int(11)                                                    NOT NULL COMMENT '%',
    `Value`       decimal(10, 0)                                             NOT NULL,
    `Operator`    varchar(10)                                                NOT NULL,
    PRIMARY KEY (`SkillId`),
    UNIQUE KEY `Type` (`Type`, `Probability`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8;

INSERT INTO `skills` (`SkillId`, `Name`, `Description`, `Type`, `Probability`, `Value`, `Operator`)
VALUES (1, 'Rapid strike',
        'Strike twice while it\'s his turn to attack; there\'s a 10% chance he\'ll use this skill\r\nevery time he attacks',
        'TURN', 10, '0', 'luck'),
       (2, 'Magic shield',
        'Takes only half of the usual damage when an enemy attacks; there\'s a 20%\r\nchange he\'ll use this skill every time he defends',
        'DEFENCE', 20, '2', 'divide');
