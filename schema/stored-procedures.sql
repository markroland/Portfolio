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

# Begin update_discipline
DROP PROCEDURE IF EXISTS update_discipline;
CREATE PROCEDURE update_discipline(
    discipline_id_var INT,
    discipline_var VARCHAR(32)
)
BEGIN
    UPDATE `discipline`
    SET discipline = discipline_var
    WHERE discipline_id = discipline_id_var;
END$$
# End update_discipline

# Begin delete_discipline
DROP PROCEDURE IF EXISTS delete_discipline;
CREATE PROCEDURE delete_discipline(
    discipline_id_var INT
)
BEGIN
    DELETE
    FROM `discipline`
    WHERE discipline_id = discipline_id_var;
END$$
# End delete_discipline

# Begin get_disciplines
DROP PROCEDURE IF EXISTS get_disciplines;
CREATE PROCEDURE get_disciplines()
BEGIN
    SELECT *
    FROM `discipline`
    ORDER BY discipline ASC;
END$$
# End get_disciplines

# Begin get_discipline_by_id
DROP PROCEDURE IF EXISTS get_discipline_by_id;
CREATE PROCEDURE get_discipline_by_id(
    discipline_id_var INT
)
BEGIN
    SELECT *
    FROM `discipline`
    WHERE discipline_id = discipline_id_var;
END$$
# End get_discipline_by_id

# Begin get_discipline_by_word
DROP PROCEDURE IF EXISTS get_discipline_by_word;
CREATE PROCEDURE get_discipline_by_word(
    discipline_word_var VARCHAR(32)
)
BEGIN
    SELECT *
    FROM `discipline`
    WHERE discipline = discipline_word_var;
END$$
# End get_discipline_by_word

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

# Begin update_keyword
DROP PROCEDURE IF EXISTS update_keyword;
CREATE PROCEDURE update_keyword(
    keyword_id_var INT,
    keyword_var VARCHAR(32)
)
BEGIN
    UPDATE `keyword`
    SET keyword = keyword_var
    WHERE keyword_id = keyword_id_var;
END$$
# End update_keyword

# Begin delete_keyword
DROP PROCEDURE IF EXISTS delete_keyword;
CREATE PROCEDURE delete_keyword(
    keyword_id_var INT
)
BEGIN
    DELETE
    FROM `keyword`
    WHERE keyword_id = keyword_id_var;
END$$
# End delete_keyword

# Begin get_keywords
DROP PROCEDURE IF EXISTS get_keywords;
CREATE PROCEDURE get_keywords()
BEGIN
    SELECT *
    FROM `keyword`
    ORDER BY keyword ASC;
END$$
# End get_keywords

# Begin get_keyword_by_id
DROP PROCEDURE IF EXISTS get_keyword_by_id;
CREATE PROCEDURE get_keyword_by_id(
    keyword_id_var INT
)
BEGIN
    SELECT *
    FROM `keyword`
    WHERE keyword_id = keyword_id_var;
END$$
# End get_keyword_by_id

# Begin get_keyword_by_word
DROP PROCEDURE IF EXISTS get_keyword_by_word;
CREATE PROCEDURE get_keyword_by_word(
    keyword_word_var VARCHAR(32)
)
BEGIN
    SELECT *
    FROM `keyword`
    WHERE `keyword` = keyword_word_var;
END$$
# End get_keyword_by_word

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

# Begin update_medium
DROP PROCEDURE IF EXISTS update_medium;
CREATE PROCEDURE update_medium(
    medium_id_var INT,
    medium_var VARCHAR(32)
)
BEGIN
    UPDATE `medium`
    SET medium = medium_var
    WHERE medium_id = medium_id_var;
END$$
# End update_medium

# Begin delete_medium
DROP PROCEDURE IF EXISTS delete_medium;
CREATE PROCEDURE delete_medium(
    medium_id_var INT
)
BEGIN
    DELETE
    FROM `medium`
    WHERE medium_id = medium_id_var;
END$$
# End delete_medium

# Begin get_media
DROP PROCEDURE IF EXISTS get_media;
CREATE PROCEDURE get_media()
BEGIN
    SELECT *
    FROM `medium`
    ORDER BY medium ASC;
END$$
# End get_media

# Begin get_medium_by_id
DROP PROCEDURE IF EXISTS get_medium_by_id;
CREATE PROCEDURE get_medium_by_id(
    medium_id_var INT
)
BEGIN
    SELECT *
    FROM `medium`
    WHERE medium_id = medium_id_var;
END$$
# End get_medium_by_id

# Begin get_medium_by_word
DROP PROCEDURE IF EXISTS get_medium_by_word;
CREATE PROCEDURE get_medium_by_word(
    medium_word_var VARCHAR(32)
)
BEGIN
    SELECT *
    FROM `medium`
    WHERE medium = medium_word_var;
END$$
# End get_medium_by_word

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

# Begin update_project
DROP PROCEDURE IF EXISTS update_project;
CREATE PROCEDURE update_project(
    project_id_var SMALLINT(3),
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
    weight_lbs_var FLOAT(7,4)
)
BEGIN

    UPDATE `project`
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
        weight_lbs = weight_lbs_var
    WHERE project_id = project_id_var;

END$$
# End update_project

# Begin delete_project
DROP PROCEDURE IF EXISTS delete_project;
CREATE PROCEDURE delete_project(
    project_id_var INT
)
BEGIN
    DELETE
    FROM `project`
    WHERE project_id = project_id_var;
END$$
# End delete_project

# Begin get_projects_admin
DROP PROCEDURE IF EXISTS get_projects_admin;
CREATE PROCEDURE get_projects_admin()
BEGIN
    SELECT *
    FROM `project`
    ORDER BY title ASC;
END$$
# End get_projects_admin

# Begin get_project_by_id
DROP PROCEDURE IF EXISTS get_project_by_id;
CREATE PROCEDURE get_project_by_id(
    project_id_var INT
)
BEGIN
    SELECT *
    FROM `project`
    WHERE project_id = project_id_var;
END$$
# End get_project_by_id

# Begin get_project_by_url_safe_title
DROP PROCEDURE IF EXISTS get_project_by_url_safe_title;
CREATE PROCEDURE get_project_by_url_safe_title(
    url_safe_title_var VARCHAR(32)
)
BEGIN
    SELECT *
    FROM `project`
    WHERE url_safe_title = url_safe_title_var;
END$$
# End get_project_by_url_safe_title

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

# Begin delete_project_disciplines
DROP PROCEDURE IF EXISTS delete_project_disciplines;
CREATE PROCEDURE delete_project_disciplines(
    project_id_var INT
)
BEGIN
    DELETE
    FROM `project_discipline`
    WHERE project_id = project_id_var;
END$$
# End delete_project_disciplines

# Begin get_project_disciplines
DROP PROCEDURE IF EXISTS get_project_disciplines;
CREATE PROCEDURE get_project_disciplines(
    project_id_var INT
)
BEGIN
    SELECT *
    FROM `project_discipline`
    WHERE project_id = project_id_var;
END$$
# End get_project_disciplines

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

# Begin get_project_hits_by_id
DROP PROCEDURE IF EXISTS get_project_hits_by_id;
CREATE PROCEDURE get_project_hits_by_id(
    project_id_var INT
)
BEGIN
    SELECT *
    FROM `project_hits`
    WHERE project_id = project_id_var;
END$$
# End get_project_hits_by_id

####################################################################################################
# Project Item
####################################################################################################

# Begin add_project_item
DROP PROCEDURE IF EXISTS add_project_item;
CREATE PROCEDURE add_project_item(
    project_id_var SMALLINT(3),
    rank_var SMALLINT (3),
    purpose_var VARCHAR(32),
    media_type_var VARCHAR(32),
    URL_var VARCHAR(120),
    width_var INT(11),
    height_var INT(11),
    title_var VARCHAR(64),
    description_var VARCHAR(255),
    OUT item_id INT
)
BEGIN

    INSERT INTO `project_item`
    SET project_id = project_id_var,
        rank = rank_var,
        purpose = purpose_var,
        media_type = media_type_var,
        `URL` = URL_var,
        width = width_var,
        height = height_var,
        title = title_var,
        `description` = description_var;

    SET item_id = LAST_INSERT_ID();
END$$
# End add_project_item

# Begin get_project_items
DROP PROCEDURE IF EXISTS get_project_items;
CREATE PROCEDURE get_project_items(
    project_id_var INT
)
BEGIN
    SELECT *
    FROM `project_item`
    WHERE project_id = project_id_var;
END$$
# End get_project_items

# Begin get_project_item_by_id
DROP PROCEDURE IF EXISTS get_project_item_by_id;
CREATE PROCEDURE get_project_item_by_id(
    item_id_var INT
)
BEGIN
    SELECT *
    FROM `project_item`
    WHERE item_id = item_id_var;
END$$
# End get_project_item_by_id

# Begin get_project_item_purposes
DROP PROCEDURE IF EXISTS get_project_item_purposes;
CREATE PROCEDURE get_project_item_purposes()
BEGIN
    SELECT DISTINCT purpose
    FROM `project_item`
    WHERE purpose <> ''
    ORDER BY purpose ASC;
END$$
# End get_project_item_purposes

# Begin update_project_item
DROP PROCEDURE IF EXISTS update_project_item;
CREATE PROCEDURE update_project_item(
    item_id_var INT,
    project_id_var SMALLINT(3),
    rank_var SMALLINT (3),
    purpose_var VARCHAR(32),
    media_type_var VARCHAR(32),
    URL_var VARCHAR(120),
    width_var INT(11),
    height_var INT(11),
    title_var VARCHAR(64),
    description_var VARCHAR(255)
)
BEGIN
    UPDATE `project_item`
    SET project_id = project_id_var,
        rank = rank_var,
        purpose = purpose_var,
        media_type = media_type_var,
        `URL` = URL_var,
        width = width_var,
        height = height_var,
        title = title_var,
        `description` = description_var
    WHERE item_id = item_id_var;
END$$
# End update_project_item

# Begin delete_project_item
DROP PROCEDURE IF EXISTS delete_project_item;
CREATE PROCEDURE delete_project_item(
    item_id_var INT
)
BEGIN
    DELETE
    FROM `project_item`
    WHERE item_id = item_id_var;
END$$
# End delete_project_item

# Begin delete_project_items
DROP PROCEDURE IF EXISTS delete_project_items;
CREATE PROCEDURE delete_project_items(
    project_id_var INT
)
BEGIN
    DELETE
    FROM `project_item`
    WHERE project_id = project_id_var;
END$$
# End delete_project_items

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

# Begin delete_project_keywords
DROP PROCEDURE IF EXISTS delete_project_keywords;
CREATE PROCEDURE delete_project_keywords(
    project_id_var INT
)
BEGIN
    DELETE
    FROM `project_keyword`
    WHERE project_id = project_id_var;
END$$
# End delete_project_keywords

# Begin get_project_keywords
DROP PROCEDURE IF EXISTS get_project_keywords;
CREATE PROCEDURE get_project_keywords(
    project_id_var INT
)
BEGIN
    SELECT *
    FROM `project_keyword`
    WHERE project_id = project_id_var;
END$$
# End get_project_keywords

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

# Begin delete_project_media
DROP PROCEDURE IF EXISTS delete_project_media;
CREATE PROCEDURE delete_project_media(
    project_id_var INT
)
BEGIN
    DELETE
    FROM `project_medium`
    WHERE project_id = project_id_var;
END$$
# End delete_project_media

# Begin get_project_media
DROP PROCEDURE IF EXISTS get_project_media;
CREATE PROCEDURE get_project_media(
    project_id_var INT
)
BEGIN
    SELECT *
    FROM `project_medium`
    WHERE project_id = project_id_var;
END$$
# End get_project_media

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

# Begin delete_related_projects
DROP PROCEDURE IF EXISTS delete_related_projects;
CREATE PROCEDURE delete_related_projects(
    project_id_var INT
)
BEGIN
    DELETE
    FROM `related_projects`
    WHERE project_id_A = project_id_var OR project_id_B = project_id_var;
END$$
# End delete_related_projects

# Begin get_related_projects
DROP PROCEDURE IF EXISTS get_related_projects;
CREATE PROCEDURE get_related_projects(
    project_id_var INT
)
BEGIN
    SELECT DISTINCT
        CASE
            WHEN project_id_A = project_id_var THEN project_id_B
            ELSE project_id_A
        END AS project_id
    FROM related_projects
    WHERE project_id_A = project_id_var OR project_id_B = project_id_var
    ORDER BY project_id ASC;
END$$
# End get_related_projects

# Reset delimiter
DELIMITER ;
