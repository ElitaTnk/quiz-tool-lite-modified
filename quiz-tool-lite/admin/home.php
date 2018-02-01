<h2>Question Pots</h2>
<form action="admin.php?page=ai-quiz-home&action=potCreate" method="post" name="newPotForm">
<a href="javascript:toggleLayerVis('newPotDiv'); document.newPotForm.potName.focus();" class="button-primary">Create a new question pot</a>
<div id="newPotDiv" style="display:none; padding-top:5px;">
<input type="text" name="potName" id="potName" placeholder="Enter Quiz Name"  style="width:250px"/>
<input type="submit" value="Create Question Pot" class="button-secondary"/>
</div>
</form>

<hr/>
<?php
// Get the current question pots
$feedback="";

if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	switch ($action) {
		case "potCreate":
			$feedback = qtl_actions::questionPotCreate();
			break;
			
		case "potEdit":
			$feedback = qtl_actions::questionPotEdit();
			break;	
			
		case "potDelete":
			$potID = $_GET['potID'];
			$feedback = qtl_actions::potDelete($potID);
			break;	       
				
	}
}


if($feedback)
{
	echo $feedback;
}


$potRS = qtl_queries::getQuestionPots();
$potCount = count($potRS);

if($potCount>=1)
{
	echo '<div id="quiztable">';
	foreach ($potRS	as $myPots)
	{
		$potName = qtl_utils::convertTextFromDB($myPots['potName']);		
		$potID= $myPots['potID'];	
		
		// Get the question Count from those pots
		$questionRS = qtl_queries::getQuestionsInPot($potID);
		$questionCount = count($questionRS);

		echo '<div class="questionPotAdminDiv">';
		echo '<div id="pot'.$potID.'">';
		echo '<h2><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'">'.$potName.'</a></h2>';
		echo '</div>';
		echo '<div id="potEdit'.$potID.'" style="display:none;">';
		echo '<form action="admin.php?page=ai-quiz-home&action=potEdit" method="post">';
		echo '<input name="potName" value="'.$potName.'" style="width:250px">';
		echo '<input name="potID" type="hidden" value="'.$potID.'">';	
		echo '<input type="submit" value="Update" class="button-primary">';
		echo '<input type="submit" value="Cancel" onclick="toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\'); return false" class="button-secondary">';
		echo '</form>';		
		echo '</div>';
		if($questionCount>1 || $questionCount==0){$plural='s';}else{$plural='';}
		echo $questionCount.' question'.$plural.'<br/><br/>';
		echo '<span class="addIcon greyLink"><a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'">Add / edit questions</a></span> | ';		
		echo '<span class="editIcon greyLink"><a href="javascript:toggleLayerVis(\'potEdit'.$potID.'\');toggleLayerVis(\'pot'.$potID.'\');">Change Pot Name</a></span> | ';
		echo '<span class="deleteIcon greyLink"><a href="#TB_inline?width=400&height=190&inlineId=QuestionPotDeleteCheck'.$potID.'" class="thickbox">Delete this question pot</a></span>';

		echo '</div>';
		
//		echo '<a href="admin.php?page=ai-quiz-question-list&potID='.$potID.'" class="editIcon">Add / Edit Questions</a>';


		echo '<div id="QuestionPotDeleteCheck'.$potID.'" style="display:none">';
		echo '<div style="text-align:center">';
		echo '<h2>Are you sure you want to delete the question pot<br>"'.$potName.'" ?</h2>';
		echo '<span class="failText">This will delete all questions in this pot and cannot be undone!</span><br/><br/>';
		echo '<input type="submit" value="Yes, delete this question pot" onclick="location.href=\'?page=ai-quiz-home&potID='.$potID.'&action=potDelete&potID='.$potID.'&tab=options\'" class="button-primary">';			
		echo '<input type="submit" value="Cancel" onclick="self.parent.tb_remove();return false" class="button-secondary">';	
		echo '</div>';
		echo '</div>';
		echo '<hr/>';
	}

	echo '</div>';
}
else
{
	echo '<h4>Welcome to Quiz Tool Lite</h4>';
	echo '<span class="greyText">All questions you create need to be within a question "pot" e.g. "Maths Questions" or "Difficult Questions"<br/><br/>Create a question pot by clicking the button above.';
	echo '<hr/>For more information and help please read the <a href="admin.php?page=ai-quiz-help">help pages</a> or visit the <a href="https://wordpress.org/support/plugin/quiz-tool-lite">support forum</a></span>';
}



?>

