<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
"drop table if exists {$CFG->dbprefix}student_note"
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
array( "{$CFG->dbprefix}student_note",
"create table {$CFG->dbprefix}student_note (
    student_note_id INTEGER NOT NULL AUTO_INCREMENT,
    context_id     INTEGER NOT NULL,
    creator_id  INTEGER NOT NULL,
    student_id  INTEGER NOT NULL,
    updated_at  DATETIME NOT NULL,
    created_at  DATETIME NOT NULL,
    period_start  DATETIME,
    period_end  DATETIME,
    extra_time INTEGER,
    note_type INTEGER,
    absence_type INTEGER,
    note_text TEXT,
    private_text INTEGER,

    PRIMARY KEY (student_note_id),

    CONSTRAINT `{$CFG->dbprefix}student_notes_ibfk_1`
        FOREIGN KEY (`context_id`)
        REFERENCES `{$CFG->dbprefix}lti_context` (`context_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `{$CFG->dbprefix}student_notes_ibfk_2`
        FOREIGN KEY (`creator_id`)
        REFERENCES `{$CFG->dbprefix}lti_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT `{$CFG->dbprefix}student_notes_ibfk_3`
        FOREIGN KEY (`student_id`)
        REFERENCES `{$CFG->dbprefix}lti_user` (`user_id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);
