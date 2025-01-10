# Portfolio MySQL Stored Procedures

# Set temporary delimiter
DELIMITER $$

####################################################################################################
# Discipline
####################################################################################################

# Begin add_discipline
DROP PROCEDURE IF EXISTS add_discipline;
CREATE PROCEDURE add_discipline(
    discipline_var VARCHAR(32),
    OUT discipline_id INT
)
BEGIN

    INSERT INTO `discipline`
    SET discipline = discipline_var;

    SET discipline_id = LAST_INSERT_ID();
END$$
# End add_discipline

####################################################################################################
# Keyword
####################################################################################################

# Begin add_keyword
DROP PROCEDURE IF EXISTS add_keyword;
CREATE PROCEDURE add_keyword(
    keyword_var VARCHAR(32),
    OUT keyword_id INT
)
BEGIN

    INSERT INTO `keyword`
    SET keyword = keyword_var;

    SET keyword_id = LAST_INSERT_ID();
END$$
# End add_keyword

####################################################################################################
# Medium
####################################################################################################

# Begin add_medium
DROP PROCEDURE IF EXISTS add_medium;
CREATE PROCEDURE add_medium(
    medium_var VARCHAR(32),
    OUT medium_id INT
)
BEGIN

    INSERT INTO `medium`
    SET medium = medium_var;

    SET medium_id = LAST_INSERT_ID();
END$$
# End add_medium

####################################################################################################
# Project
####################################################################################################

# Begin add_project
DROP PROCEDURE IF EXISTS add_project;
CREATE PROCEDURE add_project(
    publish_var TINYINT(1),
    grade_var FLOAT(3,2),
    start_date_var DATE,
    completion_date_var DATE,
    title_var VARCHAR(64),
    url_safe_title_var VARCHAR(32),
    synopsis_var TEXT,
    description_var TEXT,
    tutorial_var TEXT,
    open_source_var TINYINT(1),
    location_var VARCHAR(32),
    width_inches_var FLOAT(7,4),
    height_inches_var FLOAT(7,4),
    depth_inches_var FLOAT(7,4),
    weight_lbs_var FLOAT(7,4),
    OUT project_id SMALLINT(3)
)
BEGIN

    INSERT INTO `project`
    SET publish = publish_var,
        grade = grade_var,
        start_date = start_date_var,
        completion_date = completion_date_var,
        title = title_var,
        url_safe_title = url_safe_title_var,
        synopsis = synopsis_var,
        description = description_var,
        tutorial = tutorial_var,
        open_source = open_source_var,
        location = location_var,
        width_inches = width_inches_var,
        height_inches = height_inches_var,
        depth_inches = depth_inches_var,
        weight_lbs = weight_lbs_var;

    SET project_id = LAST_INSERT_ID();

END$$
# End add_project

####################################################################################################
# Project Discipline
####################################################################################################

# Begin add_project_discipline
DROP PROCEDURE IF EXISTS add_project_discipline;
CREATE PROCEDURE add_project_discipline(
    project_id_var SMALLINT(3),
    discipline_id_var TINYINT(3)
)
BEGIN

    INSERT INTO `project_discipline`
    SET project_id = project_id_var,
        discipline_id = discipline_id_var;

END$$
# End add_project_discipline

####################################################################################################
# Project Hits
####################################################################################################

# Begin add_project_hits
DROP PROCEDURE IF EXISTS add_project_hits;
CREATE PROCEDURE add_project_hits(
    project_id_var SMALLINT(3),
    date_var DATE,
    hits_var SMALLINT(5)
)
BEGIN

    INSERT INTO `project_hits`
    SET project_id = project_id_var,
        `date` = date_var,
        hits = hits_var;

END$$
# End add_project_hits

####################################################################################################
# Project Keyword
####################################################################################################

# Begin add_project_keyword
DROP PROCEDURE IF EXISTS add_project_keyword;
CREATE PROCEDURE add_project_keyword(
    project_id_var SMALLINT(3),
    keyword_id_var TINYINT(3)
)
BEGIN

    INSERT INTO `project_keyword`
    SET project_id = project_id_var,
        keyword_id = keyword_id_var;

END$$
# End add_project_keyword

####################################################################################################
# Project Medium
####################################################################################################

# Begin add_project_medium
DROP PROCEDURE IF EXISTS add_project_medium;
CREATE PROCEDURE add_project_medium(
    project_id_var SMALLINT(3),
    medium_id_var TINYINT(3)
)
BEGIN

    INSERT INTO `project_medium`
    SET project_id = project_id_var,
        medium_id = medium_id_var;

END$$
# End add_project_medium

####################################################################################################
# Related Projects
####################################################################################################

# Begin add_related_project
DROP PROCEDURE IF EXISTS add_related_project;
CREATE PROCEDURE add_related_project(
    project_id_A_var SMALLINT(3),
    project_id_B_var SMALLINT(3)
)
BEGIN

    INSERT INTO `related_projects`
    SET project_id_A = project_id_A_var,
        project_id_B = project_id_B_var;

END$$
# End add_related_project

# Reset delimiter
DELIMITER ;
