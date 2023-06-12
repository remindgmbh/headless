# -----------------------------------------------------------------------------#
# Table definitions -----------------------------------------------------------#
# -----------------------------------------------------------------------------#

CREATE TABLE tx_headless_item (
    tt_content int(11) unsigned DEFAULT '0' NOT NULL,
    header varchar(255) DEFAULT '' NOT NULL,
    header_layout varchar(30) DEFAULT '0' NOT NULL,
    header_link varchar(1024) DEFAULT '' NOT NULL,
    header_position varchar(255) DEFAULT '' NOT NULL,
    subheader varchar(255) DEFAULT '' NOT NULL,
    bodytext mediumtext,
    space_before_class varchar(60) DEFAULT '' NOT NULL,
    space_after_class varchar(60) DEFAULT '' NOT NULL,
    image int(11) unsigned DEFAULT '0' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    flexform mediumtext,
);

# -----------------------------------------------------------------------------#
# External Table Changes ------------------------------------------------------#
# -----------------------------------------------------------------------------#

CREATE TABLE tt_content (
    tx_headless_background_color VARCHAR(60),
    tx_headless_background_full_width TINYINT(1) DEFAULT '0' NOT NULL,
    tx_headless_cookie_category TINYINT(1),
    tx_headless_cookie_message mediumtext,
    tx_headless_item int(11) unsigned DEFAULT '0',
    tx_headless_space_before_inside VARCHAR(60) DEFAULT '' NOT NULL,
    tx_headless_space_after_inside VARCHAR(60) DEFAULT '' NOT NULL,
);

CREATE TABLE pages (
    tx_headless_overview_label VARCHAR(60) DEFAULT '' NOT NULL,
);

CREATE TABLE sys_file_reference (
    tx_headless_lazy_loading TINYINT(1) DEFAULT '0' NOT NULL,
);
