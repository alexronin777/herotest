DROP TABLE IF EXISTS `fighter_skills`;
CREATE TABLE IF NOT EXISTS `fighter_skills`
(
    `FighterSkillId` int(11) NOT NULL AUTO_INCREMENT,
    `FighterId`      int(11) NOT NULL,
    `SkillId`        int(11) NOT NULL,
    PRIMARY KEY (`FighterSkillId`),
    UNIQUE KEY `UniqueSkillTypePerFighter` (`FighterId`, `SkillId`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;