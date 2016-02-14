# Wellness MySQL Stored Procedures
# Version 0.1
# June 19, 2014
# Update using: mysql -u abenity -p abenity_wellness < /home/abenity/libraries/wellness/stored-procedures.sql

# Set temporary delimiter
DELIMITER $$

####################################################################################################
# Widgets
####################################################################################################

# Begin get_widgets
DROP PROCEDURE IF EXISTS get_widgets;
CREATE PROCEDURE get_widgets()
BEGIN
    SELECT * FROM `abenity_wellness`.`widget` ORDER BY widget_id ASC;
END$$
# End get_widgets


####################################################################################################
# Recipes
####################################################################################################

# Begin add_recipe
DROP PROCEDURE IF EXISTS add_recipe;
CREATE PROCEDURE add_recipe(
    recipe_key_var VARCHAR(32),
    publish_status_var TINYINT(1),
    title_var VARCHAR(128),
    description_var VARCHAR(128),
    instructions_var TEXT,
    category_var VARCHAR(32),
    content_provider_var VARCHAR(16),
    url_var VARCHAR(120),
    image_url_var VARCHAR(120),
    image_width_var TINYINT(3),
    image_height_var TINYINT(3),
    popularity_var FLOAT(4,3),
    OUT last_id INT
)
BEGIN

    INSERT INTO `abenity_wellness`.`recipe`
    SET recipe_key = recipe_key_var,
        publish_status = publish_status_var,
        title = title_var,
        description = description_var,
        instructions = instructions_var,
        category = category_var,
        content_provider = content_provider_var,
        url = url_var,
        image_url = image_url_var,
        image_width = image_width_var,
        image_height = image_height_var,
        popularity = popularity_var;

    SET last_id = LAST_INSERT_ID();
END$$
# End add_recipe

# Begin update_recipe
DROP PROCEDURE IF EXISTS update_recipe;
CREATE PROCEDURE update_recipe(
    recipe_key_var VARCHAR(32),
    publish_status_var TINYINT(1),
    title_var VARCHAR(128),
    description_var VARCHAR(128),
    instructions_var TEXT,
    category_var VARCHAR(32),
    content_provider_var VARCHAR(16),
    url_var VARCHAR(120),
    image_url_var VARCHAR(120),
    image_width_var TINYINT(3),
    image_height_var TINYINT(3),
    popularity_var FLOAT(4,3),
    recipe_id SMALLINT(3)
)
BEGIN

    UPDATE `abenity_wellness`.`recipe`
    SET recipe_key = recipe_key_var,
        publish_status = publish_status_var,
        title = title_var,
        description = description_var,
        instructions = instructions_var,
        category = category_var,
        content_provider = content_provider_var,
        url = url_var,
        image_url = image_url_var,
        image_width = image_width_var,
        image_height = image_height_var,
        popularity = popularity_var
    WHERE recipe_id = recipe_id_var;

END$$
# End update_recipe

# Begin track_recipe_view
DROP PROCEDURE IF EXISTS track_recipe_view;
CREATE PROCEDURE track_recipe_view(recipe_id_var SMALLINT(3))
BEGIN
    UPDATE `abenity_wellness`.`recipe` SET views = views + 1 WHERE recipe_id = recipe_id_var;
END$$
# End track_recipe_view

# Begin delete_recipe
DROP PROCEDURE IF EXISTS delete_recipe;
CREATE PROCEDURE delete_recipe(recipe_id_var SMALLINT(3), deletion_type varchar(4))
BEGIN
    IF deletion_type = 'hard' THEN
        DELETE FROM `abenity_wellness`.`recipe` WHERE recipe_id = recipe_id_var;
    ELSE
        UPDATE `abenity_wellness`.`recipe` SET publish_status = 0 WHERE recipe_id = recipe_id_var;
    END IF;
END$$
# End delete_recipe

# Begin get_recipes
DROP PROCEDURE IF EXISTS get_recipes;
CREATE PROCEDURE get_recipes()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`recipe`
    LEFT JOIN `abenity_wellness`.`recipe_rating` ON `recipe`.`recipe_id` = `recipe_rating`.`id`
    ORDER BY `title` ASC;
END$$
# End get_recipes

# Begin get_recipe_by_id
DROP PROCEDURE IF EXISTS get_recipe_by_id;
CREATE PROCEDURE get_recipe_by_id(recipe_var SMALLINT(1))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`recipe`
    LEFT JOIN `abenity_wellness`.`recipe_rating` ON `recipe`.`recipe_id` = `recipe_rating`.`id`
    WHERE recipe_id = recipe_var;
END$$
# End get_recipe_by_id

# Begin get_recipe_by_category
DROP PROCEDURE IF EXISTS get_recipe_by_category;
CREATE PROCEDURE get_recipe_by_category(recipe_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`recipe`
    LEFT JOIN `abenity_wellness`.`recipe_rating` ON `recipe`.`recipe_id` = `recipe_rating`.`id`
    WHERE category = recipe_var
    ORDER BY `title` ASC;
END$$
# End get_recipe_by_category

# Begin get_recipe_by_popularity
DROP PROCEDURE IF EXISTS get_recipe_by_popularity;
CREATE PROCEDURE get_recipe_by_popularity()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`recipe`
    LEFT JOIN `abenity_wellness`.`recipe_rating` ON `recipe`.`recipe_id` = `recipe_rating`.`id`
    ORDER BY views DESC;
END$$
# End get_recipe_by_popularity

# Begin get_recipe_by_random
DROP PROCEDURE IF EXISTS get_recipe_by_random;
CREATE PROCEDURE get_recipe_by_random()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`recipe`
    LEFT JOIN `abenity_wellness`.`recipe_rating` ON `recipe`.`recipe_id` = `recipe_rating`.`id`
    ORDER BY rand();
END$$
# End get_recipe_by_random

# Begin get_recipe_categories
DROP PROCEDURE IF EXISTS get_recipe_categories;
CREATE PROCEDURE get_recipe_categories(publish_status_var TINYINT(1))
BEGIN

    IF publish_status_var = -1 THEN

        SELECT category, count(recipe_id) as recipe_count
        FROM `abenity_wellness`.`recipe`
        WHERE category != ''
        GROUP BY category
        ORDER BY category ASC;

    ELSE

        SELECT category, count(recipe_id) as recipe_count
        FROM `abenity_wellness`.`recipe`
        WHERE category != ''
            AND publish_status = publish_status_var
        GROUP BY category
        ORDER BY category ASC;

    END IF;

END$$
# End get_recipe_categories

####################################################################################################
# Videos
####################################################################################################

# Begin video_key_exists
DROP PROCEDURE IF EXISTS video_key_exists;
CREATE PROCEDURE video_key_exists(key_var VARCHAR(32), id_var SMALLINT(3))
BEGIN
    SELECT * FROM `abenity_wellness`.`video` WHERE video_key = key_var AND video_id != id_var;
END$$
# End video_key_exists

# Begin add_video
DROP PROCEDURE IF EXISTS add_video;
CREATE PROCEDURE add_video(
    video_key_var VARCHAR(32),
    publish_status_var TINYINT(1),
    title_var VARCHAR(128),
    description_var VARCHAR(128),
    cdn_var VARCHAR(24),
    cdn_id_var VARCHAR(24),
    url_var VARCHAR(120),
    width_var SMALLINT(4),
    height_var SMALLINT(3),
    content_provider_var VARCHAR(32),
    category_var VARCHAR(32),
    OUT last_id INT
)
BEGIN

    INSERT INTO `abenity_wellness`.`video`
    SET video_key = video_key_var,
        publish_status = publish_status_var,
        title = title_var,
        description = description_var,
        cdn = cdn_var,
        cdn_id = cdn_id_var,
        url = url_var,
        width = width_var,
        height = height_var,
        content_provider = content_provider_var,
        category = category_var;

    SET last_id = LAST_INSERT_ID();
END$$
# End add_video

# Begin update_video
DROP PROCEDURE IF EXISTS update_video;
CREATE PROCEDURE update_video(
    video_key_var VARCHAR(32),
    publish_status_var TINYINT(1),
    title_var VARCHAR(128),
    description_var VARCHAR(128),
    cdn_var VARCHAR(24),
    cdn_id_var VARCHAR(24),
    url_var VARCHAR(120),
    width_var SMALLINT(4),
    height_var SMALLINT(3),
    content_provider_var VARCHAR(32),
    category_var VARCHAR(32),
    video_id_var SMALLINT(3)
)
BEGIN

    UPDATE `abenity_wellness`.`video`
    SET video_key = video_key_var,
        publish_status = publish_status_var,
        title = title_var,
        description = description_var,
        cdn = cdn_var,
        cdn_id = cdn_id_var,
        url = url_var,
        width = width_var,
        height = height_var,
        content_provider = content_provider_var,
        category = category_var
    WHERE video_id = video_id_var;
END$$
# End update_video

# Begin track_video_view
DROP PROCEDURE IF EXISTS track_video_view;
CREATE PROCEDURE track_video_view(video_id_var SMALLINT(3))
BEGIN
    UPDATE `abenity_wellness`.`video` SET views = views + 1 WHERE video_id = video_id_var;
END$$
# End track_video_view

# Begin delete_video
DROP PROCEDURE IF EXISTS delete_video;
CREATE PROCEDURE delete_video(video_id_var SMALLINT(3), deletion_type varchar(4))
BEGIN
    IF deletion_type = 'hard' THEN
        DELETE FROM `abenity_wellness`.`video` WHERE video_id = video_id_var;
    ELSE
        UPDATE `abenity_wellness`.`video` SET publish_status = 0 WHERE video_id = video_id_var;
    END IF;
END$$
# End delete_video

# Begin get_video
DROP PROCEDURE IF EXISTS get_videos;
CREATE PROCEDURE get_videos()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`video`
    ORDER BY title ASC;
END$$
# End get_video

# Begin get_video_by_id
DROP PROCEDURE IF EXISTS get_video_by_id;
CREATE PROCEDURE get_video_by_id(video_var SMALLINT(1))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`video`
    WHERE video_id = video_var;
END$$
# End get_video_by_id

# Begin get_video_by_category
DROP PROCEDURE IF EXISTS get_video_by_category;
CREATE PROCEDURE get_video_by_category(video_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`video`
    WHERE category = video_var
    ORDER BY title ASC;
END$$
# End get_video_by_category

# Begin get_video_by_popularity
DROP PROCEDURE IF EXISTS get_video_by_popularity;
CREATE PROCEDURE get_video_by_popularity()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`video`
    ORDER BY views DESC;
END$$
# End get_video_by_popularity

# Begin get_video_by_random
DROP PROCEDURE IF EXISTS get_video_by_random;
CREATE PROCEDURE get_video_by_random()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`video`
    ORDER BY rand();
END$$
# End get_video_by_random

# Begin get_video_by_source
DROP PROCEDURE IF EXISTS get_video_by_source;
CREATE PROCEDURE get_video_by_source(video_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`video`
    WHERE content_provider = video_var
    ORDER BY title ASC;
END$$
# End get_video_by_source

# Begin get_video_categories
DROP PROCEDURE IF EXISTS get_video_categories;
CREATE PROCEDURE get_video_categories()
BEGIN
    SELECT category, count(video_id) as video_count
    FROM `abenity_wellness`.`video`
    GROUP BY category
    ORDER BY category ASC;
END$$
# End get_video_categories

# Begin get_video_sources
DROP PROCEDURE IF EXISTS get_video_sources;
CREATE PROCEDURE get_video_sources()
BEGIN
    SELECT content_provider, count(video_id) as num
    FROM `abenity_wellness`.`video`
    GROUP BY content_provider
    ORDER BY content_provider ASC;
END$$
# End get_video_sources

####################################################################################################
# Tips
####################################################################################################

# Begin delete_tip
DROP PROCEDURE IF EXISTS delete_tip;
CREATE PROCEDURE delete_tip(tip_id_var SMALLINT(3), deletion_type varchar(4))
BEGIN
    IF deletion_type = 'hard' THEN
        DELETE FROM `abenity_wellness`.`tip` WHERE tip_id = tip_id_var;
    ELSE
        UPDATE `abenity_wellness`.`tip` SET publish_status = 0 WHERE tip_id = tip_id_var;
    END IF;
END$$
# End delete_tip

# Begin get_wellness_tips
DROP PROCEDURE IF EXISTS get_wellness_tips;
CREATE PROCEDURE get_wellness_tips(publish_status_var TINYINT(1))
BEGIN

    IF publish_status_var = -1 THEN

        SELECT *
        FROM `abenity_wellness`.`tip`
        ORDER BY category, message ASC;

    ELSE

        SELECT *
        FROM `abenity_wellness`.`tip`
        WHERE status = publish_status_var
        ORDER BY category, message ASC;

    END IF;

END$$
# End get_wellness_tips

# Begin get_wellness_tip_by_id
DROP PROCEDURE IF EXISTS get_wellness_tip_by_id;
CREATE PROCEDURE get_wellness_tip_by_id(tip_var SMALLINT(1))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`tip`
    WHERE tip_id = tip_var;
END$$
# End get_wellness_tip_by_id

# Begin get_wellness_tip_by_category
DROP PROCEDURE IF EXISTS get_wellness_tip_by_category;
CREATE PROCEDURE get_wellness_tip_by_category(tip_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`tip`
    WHERE status = 1 AND category = tip_var;
END$$
# End get_wellness_tip_by_category

# Begin get_wellness_tip_by_next
DROP PROCEDURE IF EXISTS get_wellness_tip_by_next;
CREATE PROCEDURE get_wellness_tip_by_next(tip_var SMALLINT(1))
BEGIN
    DECLARE found_rows INT(11);
    SET found_rows = (SELECT count(*) FROM `abenity_wellness`.`tip` WHERE status = 1 AND tip_id > tip_var ORDER BY tip_id ASC LIMIT 1);
    SELECT found_rows;
    IF found_rows = 0 THEN
        SELECT * FROM `abenity_wellness`.`tip` WHERE status = 1 ORDER BY tip_id ASC LIMIT 1;
    END IF;
END$$
# End get_wellness_tip_by_next

# Begin get_wellness_tip_by_random
DROP PROCEDURE IF EXISTS get_wellness_tip_by_random;
CREATE PROCEDURE get_wellness_tip_by_random()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`tip`
    WHERE status = 1
    ORDER BY rand();
END$$
# End get_wellness_tip_by_random

# Begin get_tip_categories
DROP PROCEDURE IF EXISTS get_tip_categories;
CREATE PROCEDURE get_tip_categories()
BEGIN
    SELECT category, count(tip_id) as tip_count
    FROM `abenity_wellness`.`tip`
    GROUP BY category
    ORDER BY category ASC;
END$$
# End get_tip_categories

# Begin get_tip_sources
DROP PROCEDURE IF EXISTS get_tip_sources;
CREATE PROCEDURE get_tip_sources()
BEGIN
    SELECT source_name, count(tip_id) as num
    FROM `abenity_wellness`.`tip`
    GROUP BY source_name
    ORDER BY source_name ASC;
END$$
# End get_tip_sources


####################################################################################################
# Food
####################################################################################################

# Begin get_foods
DROP PROCEDURE IF EXISTS get_foods;
CREATE PROCEDURE get_foods(publish_status_var TINYINT(1))
BEGIN

    IF publish_status_var = -1 THEN
        SELECT *
        FROM `abenity_wellness`.`food`
        ORDER BY category, message ASC;
    ELSE
        SELECT *
        FROM `abenity_wellness`.`food`
        WHERE publish_status = publish_status_var
        ORDER BY category, message ASC;
    END IF;

END$$
# End get_foods

# Begin get_food_by_id
DROP PROCEDURE IF EXISTS get_food_by_id;
CREATE PROCEDURE get_food_by_id(food_var SMALLINT(1))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`food`
    WHERE food_id = food_var;
END$$
# End get_food_by_id

# Begin get_food_by_group
DROP PROCEDURE IF EXISTS get_food_by_group;
CREATE PROCEDURE get_food_by_group(food_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`food`
    WHERE publish_status = 1 AND food_group = food_var;
END$$
# End get_food_by_group

# Begin get_food_by_next
DROP PROCEDURE IF EXISTS get_food_by_next;
CREATE PROCEDURE get_food_by_next(food_var SMALLINT(1))
BEGIN
    DECLARE found_rows INT(11);
    SET found_rows = (SELECT count(*) FROM `abenity_wellness`.`food` WHERE publish_status = 1 AND food_id > food_var ORDER BY food_id ASC LIMIT 1);
    SELECT found_rows;
    IF found_rows = 0 THEN
        SELECT * FROM `abenity_wellness`.`food` WHERE publish_status = 1 ORDER BY food_id ASC LIMIT 1;
    END IF;
END$$
# End get_food_by_next

# Begin get_food_by_random
DROP PROCEDURE IF EXISTS get_food_by_random;
CREATE PROCEDURE get_food_by_random()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`food`
    WHERE publish_status = 1
    ORDER BY rand();
END$$
# End get_food_by_random

# Begin delete_food
DROP PROCEDURE IF EXISTS delete_food;
CREATE PROCEDURE delete_food(food_id_var SMALLINT(3), deletion_type varchar(4))
BEGIN
    IF deletion_type = 'hard' THEN
        DELETE FROM `abenity_wellness`.`food` WHERE food_id = food_id_var;
    ELSE
        UPDATE `abenity_wellness`.`food` SET publish_status = 0 WHERE food_id = food_id_var;
    END IF;
END$$
# End delete_food


####################################################################################################
# Activities
####################################################################################################

# Begin delete_activity
DROP PROCEDURE IF EXISTS delete_activity;
CREATE PROCEDURE delete_activity(activity_id_var SMALLINT(3), deletion_type varchar(4))
BEGIN
    IF deletion_type = 'hard' THEN
        DELETE FROM `abenity_wellness`.`activity` WHERE activity_id = activity_id_var;
    ELSE
        UPDATE `abenity_wellness`.`activity` SET publish_status = 0 WHERE activity_id = activity_id_var;
    END IF;
END$$
# End delete_activity

# Begin get_activities
DROP PROCEDURE IF EXISTS get_activities;
CREATE PROCEDURE get_activities()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`activity`
    ORDER BY name ASC;
END$$
# End get_activitys

# Begin get_activity_by_id
DROP PROCEDURE IF EXISTS get_activity_by_id;
CREATE PROCEDURE get_activity_by_id(activity_var SMALLINT(1))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`activity`
    WHERE activity_id = activity_var;
END$$
# End get_activity_by_id

# Begin get_activity_by_group
DROP PROCEDURE IF EXISTS get_activity_by_group;
CREATE PROCEDURE get_activity_by_group(activity_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`activity`
    WHERE `group` = activity_var;
END$$
# End get_activity_by_group

# Begin get_activity_by_random
DROP PROCEDURE IF EXISTS get_activity_by_random;
CREATE PROCEDURE get_activity_by_random()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`activity`
    ORDER BY rand();
END$$
# End get_activity_by_random


####################################################################################################
# Profiles
####################################################################################################

# Begin get_user_wellness_profile
DROP PROCEDURE IF EXISTS get_user_wellness_profile;
CREATE PROCEDURE get_user_wellness_profile(user_id_var INT(6))
BEGIN
    SELECT * FROM `abenity_wellness`.`user_profile` WHERE user_id = user_id_var;
END$$
# End get_user_wellness_profile

# Begin delete_user_profile
DROP PROCEDURE IF EXISTS delete_user_profile;
CREATE PROCEDURE delete_user_profile(user_id_var INT(6))
BEGIN
    DELETE FROM `abenity_wellness`.`user_profile` WHERE user_id = user_id_var;
END$$
# End delete_user_profile

# Begin add_profile
DROP PROCEDURE IF EXISTS add_profile;
CREATE PROCEDURE add_profile(
    user_id_var MEDIUMINT,
    gender_var ENUM('m','f'),
    birthday_var DATE,
    weight_var SMALLINT,
    height_var TINYINT,
    waist_var FLOAT,
    bps_var TINYINT,
    bpd_var TINYINT,
    smoker_var ENUM('unknown','yes','no'),
    alcohol_consumption_var ENUM('unknown','none','mild','medium','heavy'),
    activity_level_var ENUM('unknown','sedentary','moderate','active','very active','extremely active'),
    accept_terms_var TINYINT,
    accept_sharing_var TINYINT
)
BEGIN
    INSERT INTO `abenity_wellness`.`user_profile`
    SET
        user_id = user_id_var,
        gender = gender_var,
        birthday = birthday_var,
        weight = weight_var,
        height = height_var,
        waist = waist_var,
        bps = bps_var,
        bpd = bpd_var,
        smoker = smoker_var,
        alcohol_consumption = alcohol_consumption_var,
        activity_level = activity_level_var,
        accept_terms = accept_terms_var,
        accept_sharing = accept_sharing_var;
END$$
# End add_profile

# Begin update_profile
DROP PROCEDURE IF EXISTS update_profile;
CREATE PROCEDURE update_profile(
    user_id_var MEDIUMINT,
    gender_var ENUM('m','f'),
    birthday_var DATE,
    weight_var SMALLINT,
    height_var TINYINT,
    waist_var FLOAT,
    bps_var TINYINT,
    bpd_var TINYINT,
    smoker_var ENUM('unknown','yes','no'),
    alcohol_consumption_var ENUM('unknown','none','mild','medium','heavy'),
    activity_level_var ENUM('unknown','sedentary','moderate','active','very active','extremely active'),
    accept_terms_var TINYINT,
    accept_sharing_var TINYINT
)
BEGIN
    UPDATE `abenity_wellness`.`user_profile`
    SET
        gender = gender_var,
        birthday = birthday_var,
        weight = weight_var,
        height = height_var,
        waist = waist_var,
        bps = bps_var,
        bpd = bpd_var,
        smoker = smoker_var,
        alcohol_consumption = alcohol_consumption_var,
        activity_level = activity_level_var,
        accept_terms = accept_terms_var,
        accept_sharing = accept_sharing_var
    WHERE user_id = user_id_var;
END$$
# End update_profile

####################################################################################################
# Charity
####################################################################################################

# Begin delete_charity
DROP PROCEDURE IF EXISTS delete_charity;
CREATE PROCEDURE delete_charity(charity_id_var SMALLINT(3), deletion_type varchar(4))
BEGIN
    IF deletion_type = 'hard' THEN
        DELETE FROM `abenity_wellness`.`charity` WHERE charity_id = charity_id_var;
    ELSE
        UPDATE `abenity_wellness`.`charity` SET publish_status = 0 WHERE charity_id = charity_id_var;
    END IF;
END$$
# End delete_charity

# Begin get_charities
DROP PROCEDURE IF EXISTS get_charities;
CREATE PROCEDURE get_charities()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`charity`
    ORDER BY name ASC;
END$$
# End get_charities

# Begin get_charity_by_id
DROP PROCEDURE IF EXISTS get_charity_by_id;
CREATE PROCEDURE get_charity_by_id(charity_var SMALLINT(1))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`charity`
    WHERE charity_id = charity_var;
END$$
# End get_charity_by_id

# Begin get_charity_by_category
DROP PROCEDURE IF EXISTS get_charity_by_category;
CREATE PROCEDURE get_charity_by_category(charity_var VARCHAR(32))
BEGIN
    SELECT *
    FROM `abenity_wellness`.`charity`
    WHERE category = charity_var
    ORDER BY name ASC;
END$$
# End get_charity_by_category

# Begin get_charity_by_next
DROP PROCEDURE IF EXISTS get_charity_by_next;
CREATE PROCEDURE get_charity_by_next(charity_var SMALLINT(1))
BEGIN
    DECLARE found_rows INT(11);
    SET found_rows = (SELECT count(*) FROM `abenity_wellness`.`charity` WHERE charity_id > charity_var ORDER BY charity_id ASC LIMIT 1);
    SELECT found_rows;
    IF found_rows = 0 THEN
        SELECT * FROM `abenity_wellness`.`charity` ORDER BY charity_id ASC LIMIT 1;
    END IF;
END$$

# Begin get_charity_by_random
DROP PROCEDURE IF EXISTS get_charity_by_random;
CREATE PROCEDURE get_charity_by_random()
BEGIN
    SELECT *
    FROM `abenity_wellness`.`charity`
    WHERE publish_status = 1
    ORDER BY rand();
END$$
# End get_charity_by_random

# Begin get_charity_categories
DROP PROCEDURE IF EXISTS get_charity_categories;
CREATE PROCEDURE get_charity_categories()
BEGIN
    SELECT category, count(charity_id) as charity_count
    FROM `abenity_wellness`.`charity`
    GROUP BY category
    ORDER BY category ASC;
END$$
# End get_charity_categories

# Begin update_charity_logo
DROP PROCEDURE IF EXISTS update_charity_logo;
CREATE PROCEDURE update_charity_logo(
    logo_url_var VARCHAR(120),
    logo_width_var TINYINT(3),
    logo_height_var TINYINT(3),
    charity_id_var SMALLINT(3)
)
BEGIN
    UPDATE `abenity_wellness`.`charity`
    SET logo_url = logo_url_var,
        logo_width = logo_width_var,
        logo_height = logo_height_var
    WHERE charity_id = charity_id_car;
END$$
# End update_charity_logo

####################################################################################################
# Miscellaneous
####################################################################################################

# Begin get_user_favorite_recipe
DROP PROCEDURE IF EXISTS get_user_favorite_recipe;
CREATE PROCEDURE get_user_favorite_recipe(user_id_var INT(6), recipe_id_var SMALLINT(3) )
BEGIN
    SELECT * FROM `abenity_wellness`.`user_favorite_recipe` WHERE user_id = user_id_var AND recipe_id = recipe_id_var;
END$$
# End get_user_favorite_recipe

# Begin get_user_favorite_recipes
DROP PROCEDURE IF EXISTS get_user_favorite_recipes;
CREATE PROCEDURE get_user_favorite_recipes(user_id_var INT(6))
BEGIN
    SELECT * FROM `abenity_wellness`.`user_favorite_recipe` WHERE user_id = user_id_var;
END$$
# End get_user_favorite_recipes

# Begin add_user_favorite_recipe
DROP PROCEDURE IF EXISTS add_user_favorite_recipe;
CREATE PROCEDURE add_user_favorite_recipe(user_id_var INT(6), recipe_id_var SMALLINT(3) )
BEGIN
    INSERT INTO `abenity_wellness`.`user_favorite_recipe` SET user_id = user_id_var, recipe_id = recipe_id_var;
END$$
# End add_user_favorite_recipe

# Begin delete_user_favorite_recipe
DROP PROCEDURE IF EXISTS delete_user_favorite_recipe;
CREATE PROCEDURE delete_user_favorite_recipe(user_id_var INT(6), recipe_id_var SMALLINT(3) )
BEGIN
    DELETE FROM `abenity_wellness`.`user_favorite_recipe` WHERE user_id = user_id_var AND recipe_id = recipe_id_var;
END$$
# End delete_user_favorite_recipe

# Begin add_to_advisor_forum
DROP PROCEDURE IF EXISTS add_to_advisor_forum;
CREATE PROCEDURE add_to_advisor_forum(
    date_asked_var DATE,
    asker_name_var VARCHAR(120),
    asker_location_var VARCHAR(120),
    question_var VARCHAR(255),
    OUT last_id INT
)
BEGIN
    INSERT INTO `abenity_wellness`.`advisor_forum`
    SET date_asked = date_asked_var,
        asker_name = asker_name_var,
        asker_location = asker_location_var,
        question = question_var;
    SET last_id = LAST_INSERT_ID();
END$$
# End add_to_advisor_forum

# Begin update_advisor_forum
DROP PROCEDURE IF EXISTS update_advisor_forum;
CREATE PROCEDURE update_advisor_forum(
    date_asked_var DATE,
    date_answered_var DATE,
    asker_name_var VARCHAR(120),
    asker_location_var VARCHAR(120),
    answerer_name_var VARCHAR(255),
    question_var VARCHAR(255),
    answer_var BLOB,
    forum_id_var SMALLINT(3)
)
BEGIN
    UPDATE `abenity_wellness`.`advisor_forum`
    SET date_asked = date_asked_var,
        date_answered = date_answered_var,
        asker_name = asker_name_var,
        asker_location = asker_location_var,
        answerer_name = answerer_name_var,
        question = question_var,
        answer = answer_var
    WHERE forum_id = forum_id_var;
END$$
# End update_advisor_forum

# Reset delimiter
DELIMITER ;