ALTER TABLE `events`
ADD COLUMN `cost` DECIMAL NULL DEFAULT '0' AFTER `slug`;