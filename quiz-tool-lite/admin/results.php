
<h2>Quiz Results</h2>
<span class="greyText">Please note : Results are only saved from logged in users</span><br/>

<?php
$quizID="";
if(isset($_GET['quizID']))
{
	$quizID = $_GET['quizID'];
	echo '<a href="?page=ai-quiz-quiz-list" class="backIcon">Pick a different quiz</a>';
	$quizInfo = qtl_queries::getQuizInfo($quizID);
	$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
	echo '<h2>'.$quizName.'</h2>';
	//displaySearchForm();	
	drawUserResults($quizID);
}


function drawUserResults($quizID)
{
	//dataTables js
	qtl_utils::loadDataTables();
	

	// Get an array of results with username as key
	// Get the results
	$quizResults = qtl_queries::getQuizResults($quizID);
	$quizAttemptArray = array();
	foreach($quizResults as $attemptInfo)
	{
		$username = $attemptInfo['username'];
		$attemptCount = $attemptInfo['attemptCount'];
		$highestScore = $attemptInfo['highestScore'];	
		$attemptID= $attemptInfo['attemptID'];	
		$quizAttemptArray[$username] = array
		(
			"attemptCount" => $attemptCount,
			"highestScore" => $highestScore
		);
	}	
	
	echo '<table id="userTable">';
	echo '<thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Highest Score</th><th>Number of attempts</th><th></th></tr></thead>';
		
	$blogusers = get_users();
	

	// Array of WP_User objects.
	foreach ( $blogusers as $userInfo )
	{
		$fullname = esc_html( $userInfo->display_name );
		$firstName= esc_html( $userInfo->first_name );
		$surname= esc_html( $userInfo->last_name );		
		$username = $userInfo->user_login;
		$roles = $userInfo->roles;
		if($roles)
		{
			$userlevel = $roles[0];
		}
		else
		{
			$userlevel = "";	
		}	
		
		// Get the attempt info from the lookup table
		$userAttemptInfo = $quizAttemptArray[$username];
		$highestScore = $userAttemptInfo['highestScore'];
		$attemptCount = $userAttemptInfo['attemptCount'];
		
		if(!$highestScore){$highestScore = "-";}
		if(!$attemptCount){$attemptCount = "0";}		

		echo '<tr>';
		echo '<td>'.$surname.', '.$firstName.'</td>';
		echo '<td>'.$username.'</td>';
		echo '<td>'.$userlevel.'</td>';
		echo '<td>'.$highestScore.'</td>';
		echo '<td>'.$attemptCount.'</td>';
		echo '<td><a href="?page=ai-user-results&quizID='.$quizID.'&username='.$username.'">View Answers</a></td>';		
		echo '</tr>';
	}	
	
	echo '</table>';
		
		
		

	
	?>
	<script>
		jQuery(document).ready(function(){	
			if (jQuery('#userTable').length>0)
			{
				jQuery('#userTable').dataTable({
					"bAutoWidth": true,
					"bJQueryUI": true,
					"sPaginationType": "full_numbers",
					"iDisplayLength": 50, // How many numbers by default per page
					"order": [[2, "desc"]]
				});
			}
			
		});
	</script>	            
	<?php
	
	
}






	
?>
