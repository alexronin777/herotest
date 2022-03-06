CREATE TABLE IF NOT EXISTS `fighters`
(
    `FighterId` int(11)        NOT NULL AUTO_INCREMENT,
    `Health`    decimal(10, 0) NOT NULL,
    `Strength`  decimal(10, 0) NOT NULL,
    `Defence`   decimal(10, 0) NOT NULL,
    `Speed`     decimal(10, 0) NOT NULL,
    `Luck`      decimal(10, 0) NOT NULL,
    `CreatedAt` datetime       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`FighterId`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;