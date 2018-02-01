<?php

// Define the variables


$feedback="";
$potID = $_GET['potID'];
$potInfo = qtl_queries::getPotInfo($potID);

$potName = qtl_utils::convertTextFromDB($potInfo['potName']);

// Create a drop down list of question pots for the copy function
$potRS = qtl_queries::getQuestionPots();
$potCount = count($potRS);

$copyPotStr = "";

if($potCount==1) // Only one so don't show drop down, just hidden unput
{
	$copyPotStr.= 'This will copy the question to the current question pot<br/><br/>';
	$copyPotStr.= '<input type="hidden" name="copyQuestionPot" value="'.$potID.'">';
}
else
{
	$copyPotStr.= '<select name="copyQuestionPot">';
	foreach ($potRS	as $myPots)
	{
		$copyPotName = qtl_utils::convertTextFromDB($myPots['potName']);		
		$tempPotID= $myPots['potID'];
		$copyPotStr.= '<option value="'.$tempPotID.'"';
		if($tempPotID==$potID){$copyPotStr.=' selected';}
		$copyPotStr.= '>'.$copyPotName.'</option>';
	}

	$copyPotStr.= '</select><br/><br/>';
}


if(isset($_GET['action']))
{
	$myAction = $_GET['action'];
	
	switch ($myAction) {
		case "questionDelete":
			$questionID=$_GET['questionID'];
			qtl_actions::questionDelete($questionID);
			$feedback = '<div class="updated">Question Deleted</div><br/>';
		break;
		
		case "questionCopy":
			qtl_actions::questionCopy();
			$feedback = '<div class="updated">Question copied succesfully</div><br/>';
		break;			
	}	
	
}

?>
<h2><?php echo $potName?></h2>
<a href="admin.php?page=ai-quiz-home" class="backIcon">Return to all question pots</a>
<hr/>
<?php
if($feedback)
{
	echo $feedback;
}
//echo '<a href="admin.php?page=ai-quiz-question-edit&potID='.$potID.'" class="addIcon">Add a new question</a>';
echo '<a href="admin.php?page=ai-quiz-questionType&potID='.$potID.'" class="button-primary">Add a new question</a>';

// Get the questions in this pot
$questionsRS = qtl_queries::getQuestionsInPot($potID);

$questionCount = count($questionsRS);

if($questionCount==0)
{
	echo '<br/><br/><span class="greyText">No questions found</span>';
}
else
{
	echo '<div id="quiztable">';
	echo '<table><tr><th>Question</th><th width="190">Shortcode</th><th width="100">Options</th></tr>';
	$i = 1; // Increment for question numbner. Meaningless as its randomised buy hey.
		
	foreach ( $questionsRS as $myQuestions ) 
	{
		
		$question = qtl_utils::convertTextFromDB($myQuestions['question']);
		$question = do_shortcode(wpautop($question));
		$question = qtl_utils::limitWords($question, 100);
		$questionID= $myQuestions['questionID'];
		
		echo '<tr>';
		echo '<td><b>Question '.$i.'.</b> '.$question.'</td>';
		echo '<td valign="top"><span class="greyText">[QTL-Question id='.$questionID.']</span></td>';		
		echo '<td valign="top">';
		echo '<a href="admin.php?page=ai-quiz-question-edit&questionID='.$questionID.'" class="editIcon">Edit</a><br/>';
		echo '<a href="#TB_inline?width=400&height=200&inlineId=questionCopy'.$questionID.'" class="thickbox copyIcon">Copy</a><br/>';		
		echo '<a href="#TB_inline?width=400&height=120&inlineId=questionDeleteCheck'.$questionID.'" class="thickbox deleteIcon">Delete</a>';
		
		// Copy popup
		echo '<div id="questionCopy'.$questionID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Select destination question pot</h2>';
		echo '<form method="post" action="?page=ai-quiz-question-list&potID='.$potID.'&action=questionCopy">';
		echo $copyPotStr;
		echo '<input type="hidden" name="questionToCopy" value="'.$questionID.'">';
		echo '<input type="submit" value="Copy question" class="button-primary">';
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</form>';
		echo '</div>';
		echo '</div>';
		// End copy popup	
		
		
		// Delete Popup
		echo '<div id="questionDeleteCheck'.$questionID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete question '.$i.':</h2>';
		echo '<input type="submit" value="Yes, delete this question" onclick="location.href=\'?page=ai-quiz-question-list&potID='.$potID.'&action=questionDelete&questionID='.$questionID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		// End delete popup
		
		echo '</td>';
		
		echo '</tr>';
		$i++;
	}
	echo '</table>';
	echo '</div>';
}

?>