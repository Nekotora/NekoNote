<?php

$create_table = 'CREATE TABLE `nekonote`.`note` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `user` VARCHAR(32) NOT NULL , `content` TEXT NOT NULL , `time` INT NOT NULL , PRIMARY KEY (`id`), INDEX (`user`)) ENGINE = InnoDB;';