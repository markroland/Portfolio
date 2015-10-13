<?php

/**
 * Portfolio
 *
 * @author Mark Roland (markroland.com)
 * @copyright Mark Roland, 2011
 * @version 1.0
 *
 **/
class portfolio{

	public $portfolio_database = 'markr34_portfolio';

	/**
	 * Get all projects
	 * @return array Summary of project information
	 */
	function get_all_projects(){
		$query = sprintf("SELECT project_id FROM `%s`.`project` ORDER BY title ASC", $this->portfolio_database);
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			$projects[$row['project_id']] = $this->get_project($row['project_id']);
		return $projects;
	}

	/**
	 * Get information about a portfolio project
	 * @param integer $index_value A unique project identifier
	 * @param integer $index The type of $index_value. Either project_id or url_safe_title
	 * @return array Summary of project information
	 */
	function get_project($filter, $filter_param = NULL, $limit = NULL, $offset = NULL){

		// Initialize query
		$query = sprintf("SELECT * FROM `%s`.`project`", $this->portfolio_database);

		// Add to query
		if($filter == 'all'){

			$query .= " ORDER BY title ASC";

		}elseif($filter == 'date'){

			if( $filter_param == 'ASC' )
				$query .= sprintf(" ORDER BY start_date ASC");
			else
				$query .= sprintf(" ORDER BY completion_date DESC, start_date DESC");

		}elseif($filter == 'id'){

			$query .= sprintf(" WHERE project_id = %d", mysql_real_escape_string($filter_param));

		}elseif($filter == 'url_safe_title'){

			$query .= sprintf(" WHERE url_safe_title='%s'", mysql_real_escape_string($filter_param));

		}elseif($filter == 'keyword'){

			$query .= sprintf(" WHERE keyword='%s'", mysql_real_escape_string($filter_param));

		}elseif($filter == 'medium'){

			$query .= sprintf(" WHERE medium='%s'", mysql_real_escape_string($filter_param));

		}elseif($filter == 'discipline'){

			$query .= sprintf(" WHERE discipline='%s'", mysql_real_escape_string($filter_param));

		}elseif($filter == 'popularity'){

			$query = sprintf("SELECT `%s`.`project`.*, SUM(hits) AS num_hits
				FROM `%s`.`project`
					LEFT JOIN `%s`.`project_hits` USING(project_id)
				WHERE `date` > '%s'
					AND `date` <= '%s'
				GROUP BY `project_id`
				ORDER BY num_hits DESC",
				$this->portfolio_database,
				$this->portfolio_database,
				date('Y-m-d', strtotime('-4 weeks')),
				date('Y-m-d', strtotime('yesterday'))
			);

		}elseif($filter == 'random'){

			$query .= " ORDER BY rand()";

		}elseif($filter == 'title'){

			if( $filter_param != 'ASC' && $filter_param != 'DESC' )
				$filter_param = 'ASC';

			$query .= sprintf(" ORDER BY title %s", mysql_real_escape_string($filter_param));

		}

		// Include limit and offset parameters, if relevant & provided
		if( $filter != 'id' && $limit > 0){
			if($offset > 0)
				$query .= sprintf(" LIMIT %d, %d", $offset, $limit);
			else
				$query .= sprintf(" LIMIT %d", $limit);
		}

		// Execute query
		$result = mysql_query($query);

		// Loop through results
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$project['overview'] = $row;

			$completion_date = explode('-', $row['overview']['completion_date']);
			if( substr($row['completion_date'], 4, 6) == '-00-00' ){
				$project['overview']['formatted_date'] = date('Y', strtotime( str_replace('-00-00', '-01-01', $row['completion_date']) ));
			}elseif( substr($row['completion_date'], 7, 3) == '-00' ){
				$project['overview']['formatted_date'] = date('F Y', strtotime( str_replace('-00', '-01', $row['completion_date']) ));
			}else{
				$project['overview']['formatted_date'] = date('F jS, Y', strtotime($row['completion_date']));
			}

			$project['overview']['in_progress'] = 0;
			if( substr($project['overview']['completion_date'],0,4) == '0000' ){
				$project['overview']['in_progress'] = 1;
				$project['overview']['formatted_date'] = date('F jS, Y', strtotime($row['start_date']));
			}

			// Get related information
			$project['items'] = $this->get_project_items('project_id', $project['overview']['project_id']);
			$project['disciplines'] = $this->get_project_disciplines($project['overview']['project_id']);
			$project['mediums'] = $this->get_project_mediums($project['overview']['project_id']);
			$project['related_projects'] = $this->get_related_projects($project['overview']['project_id']);
			
			
			$projects[] = $project;
		}

		// Apply advanced sorting for if projects requested by date. This factors in incomplete projects
		// that have an "empty" (0000-00-00) completion date
		if( $filter == 'date' && $filter_param == 'DESC' ){
			foreach($projects as $proj) {
				if( $proj['overview']['completion_date'] != '0000-00-00')
					$completed_projects[] = $proj;
				else
					$incomplete_projects[] = $proj;
			}
			$projects = array_merge($incomplete_projects, $completed_projects);
		}

		// Return project
		return $projects;
	}

	/**
	 * Get the disciplines related to a project
	 * @param integer $project_id A unique project ID
	 * @return array Summary of disciplines used
	 */
	function get_project_disciplines($project_id){
		$query = sprintf("SELECT discipline.*
											FROM `%s`.`project_discipline`
											LEFT JOIN `%s`.`discipline` USING(discipline_id)
											WHERE project_id=%d",
											$this->portfolio_database,
											$this->portfolio_database,
											mysql_real_escape_string($project_id));
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			$disciplines[$row['discipline_id']] = ucwords($row['discipline']);
		return $disciplines;
	}

	/**
	 * Get items related to a project
	 * @param string $key The type of ID to be used
	 * @param integer $id A ID of type $key
	 * @return array Summary of items
	 */
	function get_project_items($key = 'project_id', $id){
		if( $key == 'item_id' ){
			$query = sprintf("SELECT project_item.*
												FROM `%s`.`project_item`
												WHERE item_id = %d",
												$this->portfolio_database,
												mysql_real_escape_string($id));
		}else{
			$query = sprintf("SELECT project_item.*
												FROM `%s`.`project_item`
												WHERE project_id=%d AND rank > 0
												ORDER BY rank, item_id ASC",
												$this->portfolio_database,
												mysql_real_escape_string($id));
		}
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
			$items[] = $row;
		return $items;
	}

	/**
	 * Get items related to a project
	 * @param integer $project_id A unique project ID
	 * @return array Summary of items
	 */
	function get_project_keywords($project_id){
		$query = sprintf("SELECT keyword.*
											FROM `%s`.`project_keyword`
											LEFT JOIN `%s`.`keyword` USING(keyword_id)
											WHERE project_id=%d",
											$this->portfolio_database,
											$this->portfolio_database,
											mysql_real_escape_string($project_id));
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			$disciplines[$row['keyword_id']] = ucwords($row['keyword']);
		return $disciplines;
	}

	/**
	 * Get the disciplines related to a project
	 * @param integer $project_id A unique project ID
	 * @return array Summary of disciplines used
	 */
	function get_project_mediums($project_id){
		$query = sprintf("SELECT medium.*
											FROM `%s`.`project_medium`
											LEFT JOIN `%s`.`medium` USING(medium_id)
											WHERE project_id=%d",
											$this->portfolio_database,
											$this->portfolio_database,
											mysql_real_escape_string($project_id));
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			$disciplines[$row['medium_id']] = ucwords($row['medium']);
		return $disciplines;
	}

	/**
	 * Get the disciplines related to a project
	 * @param integer $project_id A unique project ID
	 * @return array Summary of disciplines used
	 */
	function get_related_projects($project_id){
		$query = sprintf("SELECT related_projects.project_id_A as project_id, project.title, project.url_safe_title
											FROM `%s`.`related_projects`
												LEFT JOIN `%s`.`project` ON project.project_id = related_projects.project_id_A
											WHERE project_id_B=%d",
											$this->portfolio_database,
											$this->portfolio_database,
											mysql_real_escape_string($project_id));
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			$related_projects[] = $row;
		$query = sprintf("SELECT related_projects.project_id_B as project_id, project.title, project.url_safe_title
											FROM `%s`.`related_projects`
												LEFT JOIN `%s`.`project` ON project.project_id = related_projects.project_id_B
											WHERE project_id_A=%d",
											$this->portfolio_database,
											$this->portfolio_database,
											mysql_real_escape_string($project_id));
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result,MYSQL_ASSOC))
			$related_projects[] = $row;
		return $related_projects;
	}

	/**
	 * Report the number of times that a project was viewed.
	 * @param integer $project_id A unique project ID
	 * @param string $start_date Start date in YYYY-MM-DD format (Inclusive)
	 * @param string $end_date End date in YYYY-MM-DD format (Inclusive)
	 * @return array A multidimensional array of hits and date values.
	 */
	function get_project_hits($project_id, $start_date, $end_date){
		$i_date = $start_date;
		while( strtotime($i_date) <= strtotime($end_date) ){

			$query = sprintf("SELECT hits FROM `%s`.`project_hits`
												WHERE project_id=%d
													AND `date` = '%s'",
												$this->portfolio_database,
												mysql_real_escape_string($project_id),
												$i_date );
			//echo $query."<br />";
			$result = mysql_query($query);
			if($result)
				$num_hits = mysql_result($result,0);
			if(empty($num_hits))
				$num_hits = 0;
			$hits['data'][] = $num_hits;
			$hits['labels'][] = $i_date;

			$i_date = date ("Y-m-d", strtotime("+1 day", strtotime($i_date)));
		}
		return $hits;
	}

	/**
	 * Search HTTP logs for google queries that led to a specific project
	 * @param integer $project_id A unique project ID
	 * @return array An array of query strings
	 */
	function get_project_queries($project_id = NULL){

		if( !empty($project_id) ){
		$details = $this->get_project($project_id);
		$query = sprintf("SELECT referrer
											FROM `markr34_log`.`http_request`
											WHERE resource LIKE '/project/%s%%'
												AND referrer LIKE '%%google.com%%'",
											mysql_real_escape_string($details['overview']['url_safe_title']));
		}else{
		$query = sprintf("SELECT referrer
											FROM `markr34_log`.`http_request`
											WHERE referrer LIKE '%%google.com%%'");
		}
		$result = mysql_query($query);
		while( $row = mysql_fetch_array($result,MYSQL_ASSOC) ){
		if( preg_match('/q=([.]*[^&]+)/', $row['referrer'], $matches) )
			$results[] = substr( urldecode($matches[0]), 2);
		}
		return $results;
	}

} // END Class

?>