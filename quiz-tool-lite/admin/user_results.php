

<?php

if(isset($_GET['quizID']) && isset($_GET['username']))
{
	$quizID = $_GET['quizID'];
	$username = $_GET['username'];	
	$quizInfo = qtl_queries::getQuizInfo($quizID);
	$quizName = qtl_utils::convertTextFromDB($quizInfo['quizName']);
	echo '<h2>'.$quizName.'</h2>';
	echo '<a href="?page=ai-quiz-results&quizID='.$quizID.'" class="backIcon">Back to user results</a><hr/>';


	drawUserResults($username, $quizID);
	qtl_utils::loadDatatables();
	
}


function drawUserResults($username, $quizID)
{
	$userInfo = get_user_by( 'login', $username );
	$fullname =  $userInfo->first_name . ' ' . $userInfo->last_name;
	$userID=  $userInfo->ID;
	
	echo '<h3>'.get_avatar( $userID, 64 ).' '.$fullname.'</h3>';
	
	echo '<table id="userTable">';
	echo '<thead><tr><th>Attempt</th><th>Attempt Date</th><th>Time taken</th><th>Score</th><th>Breakdown</th></tr></thead>';
	
	
	$attemptsRS = qtl_queries::getAllUserAttemptInfo($username, $quizID);
	$i=1;
	foreach($attemptsRS as $attemptInfo)
	{
		$userAttemptID = $attemptInfo['userAttemptID'];
		$dateStarted = $attemptInfo['dateStarted'];
		$niceDateStarted = qtl_utils::formatDate($dateStarted);
		$niceDateStarted = $niceDateStarted[2];
		$dateFinished = $attemptInfo['dateFinished'];
		$score= $attemptInfo['score'];
		
		if($dateFinished)
		{
			$timeTaken = qtl_utils::dateDiff(strtotime($dateStarted), strtotime($dateFinished));
		}
		else
		{
			$timeTaken = '<span class="failText">Did not finish</span>';
		}
		
		echo '<tr>';
		echo '<td>'.$i.'</td>';
		echo '<td>'.$niceDateStarted.'</td>';
		echo '<td>'.$timeTaken.'</td>';		
		echo '<td>'.$score.'</td>';
		echo '<td><a href="?page=ai-quiz_breakdown&userAttemptID='.$userAttemptID.'">View Breakdown</a></td>';
		echo '</tr>';
		$i++;
		
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
					"order": [[1, "desc"]]
				});
			}
			
		});
	</script>	    
    
    <?php	
}


	
?>
