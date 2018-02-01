<h2>Quiz List</h2>

<a href="?page=ai-quiz-quiz-edit" class="button-primary">Add a new quiz</a>
<?php

$feedback = "";
if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	if($action=="quizDelete")
	{
		$quizID = $_GET['quizID'];
		qtl_actions::quizDelete($quizID);
		$feedback = '<div class="updated">Quiz Deleted</div>';
	}
}


if($feedback){echo $feedback;}
	global $wpdb;

$quizRS = qtl_queries::getQuizzes();
$quizCount = count($quizRS);

if($quizCount>=1)
{

	echo '<div id="quiztable">';
	echo '<table>';
	echo '<tr><th>Quiz Name</th><th>Shortcode</th><th>Participant Count</th><th></th><th></th><th></th></tr>';
	
		
	foreach ($quizRS	as $myQuizzes)
	{		
		$quizName = stripslashes($myQuizzes['quizName']);
		$quizID= $myQuizzes['quizID'];
		
		// Get the count of people who have taken the quiz
		
		$quizParticipants =  qtl_queries::getQuizResults($quizID);
		$participantCount = count($quizParticipants);
		
		echo '<tr>';
		echo '<td><a href="?page=ai-quiz-quiz-edit&quizID='.$quizID.'">'.$quizName.'</a></td>';
		echo '<td valign="top"><span class="greyText">[QTL-Quiz id='.$quizID.']</span></td>';
		echo '<td valign="top">'.$participantCount.' participant(s)</span></td>';
		echo '<td><a href="?page=ai-quiz-boundaries&quizID='.$quizID.'" class="boundaryIcon">Grade Boundaries</a></td>';
		echo '<td><a href="?page=ai-quiz-results&quizID='.$quizID.'" class="dataIcon">View results</a></td>';
		
	//	echo '<td><a href="admin.php?page=ai-quiz-quiz-list&action=quizDelete&quizID='.$quizID.'" class="deleteIcon">Delete</a></td>';
		echo '<td>';
		echo '<a href="#TB_inline?width=400&height=150&inlineId=QuizDeleteCheck'.$quizID.'" class="thickbox deleteIcon">Delete Quiz</a>';
		echo '<div id="QuizDeleteCheck'.$quizID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete quiz: '.$quizName.' ?</h2>';		
		echo '<input type="submit" value="Yes, delete this quiz" onclick="location.href=\'?page=ai-quiz-quiz-list&quizID='.$quizID.'&action=quizDelete&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
	echo '</div>';
}
else
{
	echo '<hr/><span class="greyText">Create a quiz by clicking the button above</span>';
}
?>