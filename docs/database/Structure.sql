SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `user` ;

CREATE  TABLE IF NOT EXISTS `user` (
  `id_user` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `login_user` VARCHAR(250) NOT NULL ,
  `password_user` CHAR(40) NOT NULL COMMENT 'SHA1' ,
  `nickname_user` VARCHAR(250) NOT NULL ,
  PRIMARY KEY (`id_user`) ,
  UNIQUE INDEX `IDX_UQ_USER_LOGIN` (`login_user` ASC) ,
  UNIQUE INDEX `IDX_UQ_USER_NICKNAME` (`nickname_user` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `post`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `post` ;

CREATE  TABLE IF NOT EXISTS `post` (
  `id_post` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_user_post` INT UNSIGNED NULL COMMENT 'Author of the post' ,
  `id_thread_post` INT UNSIGNED NOT NULL COMMENT 'Thread of the post' ,
  `content_post` TEXT NOT NULL ,
  `date_post` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `solution_post` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id_post`) ,
  INDEX `IDX_FK_POST_USER` (`id_user_post` ASC) ,
  INDEX `IDX_FK_POST_THREAD` (`id_thread_post` ASC) ,
  CONSTRAINT `FK_POST_OWNER`
    FOREIGN KEY (`id_user_post` )
    REFERENCES `user` (`id_user` )
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `FK_POST_THREAD`
    FOREIGN KEY (`id_thread_post` )
    REFERENCES `thread` (`id_thread` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `thread`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `thread` ;

CREATE  TABLE IF NOT EXISTS `thread` (
  `id_thread` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_post_thread` INT UNSIGNED NOT NULL COMMENT 'Main post of the thread' ,
  `title_thread` VARCHAR(250) NOT NULL ,
  PRIMARY KEY (`id_thread`) ,
  INDEX `IDX_FK_THREAD_POST` (`id_post_thread` ASC) ,
  CONSTRAINT `FK_THREAD_POST`
    FOREIGN KEY (`id_post_thread` )
    REFERENCES `post` (`id_post` )
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag` ;

CREATE  TABLE IF NOT EXISTS `tag` (
  `id_tag` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name_tag` VARCHAR(250) NOT NULL ,
  PRIMARY KEY (`id_tag`) ,
  UNIQUE INDEX `IDX_UQ_TAG_NAME` (`name_tag` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `vote`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `vote` ;

CREATE  TABLE IF NOT EXISTS `vote` (
  `id_post` INT UNSIGNED NOT NULL ,
  `id_user` INT UNSIGNED NOT NULL ,
  `value_vote` SMALLINT NOT NULL ,
  PRIMARY KEY (`id_post`, `id_user`) ,
  INDEX `IDX_FK_VOTE_POST` (`id_post` ASC) ,
  INDEX `IDX_FK_VOTE_USER` (`id_user` ASC) ,
  CONSTRAINT `FK_VOTE_POST`
    FOREIGN KEY (`id_post` )
    REFERENCES `post` (`id_post` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_VOTE_USER`
    FOREIGN KEY (`id_user` )
    REFERENCES `user` (`id_user` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tag_thread`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `tag_thread` ;

CREATE  TABLE IF NOT EXISTS `tag_thread` (
  `id_tag` INT UNSIGNED NOT NULL ,
  `id_thread` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id_tag`, `id_thread`) ,
  INDEX `IDX_FK_TAGTHREAD_THREAD` (`id_thread` ASC) ,
  INDEX `IDX_FK_TAGTHREAD_TAG` (`id_tag` ASC) ,
  CONSTRAINT `FK_TAGTHREAD_TAG`
    FOREIGN KEY (`id_tag` )
    REFERENCES `tag` (`id_tag` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_TAGTHREAD_THREAD`
    FOREIGN KEY (`id_thread` )
    REFERENCES `thread` (`id_thread` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
