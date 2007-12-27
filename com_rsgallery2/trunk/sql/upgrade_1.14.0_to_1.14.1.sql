# in some version prior to 1.12.2 #__rsgallery2_acl was hardcoded with the prefix jos.
# if Joomla! was installed using a different prefix then #__rsgallery2_acl will be missing.
#

# add more permissions to acl table
ALTER TABLE `#__rsgallery2_acl` ADD `public_vote_view` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `#__rsgallery2_acl` ADD `public_vote_vote` TINYINT( 1 ) NOT NULL DEFAULT '0';

ALTER TABLE `#__rsgallery2_acl` ADD `registered_vote_view` TINYINT( 1 ) NOT NULL DEFAULT '1';
ALTER TABLE `#__rsgallery2_acl` ADD `registered_vote_vote` TINYINT( 1 ) NOT NULL DEFAULT '1';
