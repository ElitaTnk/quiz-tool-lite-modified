<?php

require_once AIQUIZ_PATH.'scripts/tabs.php';

wp_enqueue_media();

/// Declare  the vars
$questionID="";
$feedback="";
$question="";
$correctFeedback ="";
$incorrectFeedback ="";
$hideIncorrectFeedback="";


if(isset($_GET['questionID']))
{	
	$questionID = $_GET['questionID']; 
}









if(isset($_GET['action']))
{
	$action=$_GET['action'];
	
	
	switch ($action) {
		case "questionEdit": 
			$feedback =  'Question updated';
			$questionID = qtl_actions::questionEdit($questionID);
			break;		
	
		case "optionUpdate":
			$feedback =  'Options updated';
			qtl_actions::responseOptionUpdate($questionID);
			break;		
			
		case "blankOptionUpdate":
			$feedback =  'Options updated';
			qtl_actions::blankResponseOptionUpdate($questionID);
			break;		
			
			
			
		case "optionDelete":
			$feedback =  'Option deleted';
			$optionID = $_GET['optionID'];
			qtl_actions::responseOptionDelete($optionID);
			break;	
			
		case "responseOrderTypeChange":
			$newOrderType = $_GET['changeTo'];
			qtl_actions::responseOptionChangeOrderType($questionID, $newOrderType);
			break;				
			
			
		default:
			if($questionID==""){$questionID = $_POST['questionID'];}
			break;		
	}
}

$potID = "";

if($questionID)
{
	
	$questionInfo = qtl_queries::getQuestionInfo($questionID);
	

	$question = qtl_utils::convertTextFromDB($questionInfo['question']);	
	$incorrectFeedback = qtl_utils::convertTextFromDB($questionInfo['incorrectFeedback']);
	$correctFeedback = qtl_utils::convertTextFromDB($questionInfo['correctFeedback']);
	$potID = $questionInfo['potID'];
	$qType = $questionInfo['qType'];
	$optionOrderType= $questionInfo['optionOrderType'];	
	
	if($optionOrderType=="")
	{
		$optionOrderType="random";	
	}
}
else
{
	$potID = $_GET['potID'];
	$qType = $_GET['qType'];	
}


$correctFeedbackLabel = 'Correct Feedback';


// Setup the basic question lables etc based on question type
if($qType=="reflection" || $qType=="reflectionText")
{
	$correctFeedbackLabel = 'Text to display after click';
	$hideIncorrectFeedback=true;
	$buttonLabel = 'Save';
	//$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=question';
}
elseif($questionID<>"")
{
	$buttonLabel = 'Save';
	//$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=question';
	
}
else
{
	$buttonLabel = 'Save and continue';
	//$questionEditFormAction = 'admin.php?page=ai-quiz-question-edit&action=questionEdit&potID='.$potID.'&questionID='.$questionID.'&qType='.$qType.'&tab=options';
}

$potInfo = qtl_queries::getPotInfo($potID);
$potName = qtl_utils::convertTextFromDB($potInfo['potName']);

?>
<script>
function submitForm(tab)
{
	document.questionEditForm.action ="admin.php?page=ai-quiz-question-edit&action=questionEdit&potID=<?php echo $potID; ?>&questionID=<?php echo $questionID; ?>&qType=<?php echo $qType; ?>&tab="+tab;
}

</script>



<div id="questionEdit">


<?php
if ( $_GET['page'] == 'ai-quiz-question-edit' )
{

   echo '<div class="formDiv">';
         ?>         
			<h2>Edit Question</h2>
            
			<a href="admin.php?page=ai-quiz-question-list&potID=<?php echo $potID?>" class="backIcon">Return to <?php echo $potName?> questions</a><br/><br/>
            
            
			<?php 
			$showResponseOptionsTab=true; // by defualt show the tab, but hide if questino ID is blank or qType is reflection 
			
			if($qType=="reflection" || $qType=="reflectionText"){$showResponseOptionsTab=false;}
			
			
			if($feedback)
			{
				echo '<div id="responseOptionFadeDiv"><div class="updated">'.$feedback.'</div></div>';
				?>
				<script>
				jQuery('#responseOptionFadeDiv').fadeIn(3000).delay(2000).fadeTo("slow",0);
				</script>
				<?php
			}				
			
			echo '<form method="post" name="questionEditForm" id="questionEditForm">';
		
			
			echo '<div id="tabs">';
			echo '<ul>';
			echo '<li><a href="#questionOverviewTab">Question</a></li>';
			echo '<li><a href="#feedbackTab">Feedback</a></li>';
			if($showResponseOptionsTab==true){
				echo '<li><a href="#responseOptionsTab">Response Options</a></li>';
			}
			echo '</ul>';
			?>
            <div id="questionOverviewTab"> <!-- first tab --->
			<h2><label for ="question">Question Text</label></h2>
            <?php
			if($qType=="blank")
			{
				echo '<div id="message" class="updated  below-h2 smallText">';
				echo 'To create your blank text boxes, type out your question and then replace each word you wish to replace with "[blank]"';
				echo '<br/>e.g. That\'s one small step for a [blank], one giant leap for [blank]';
				echo '</div>';
			}
			?>
            
            
            
			<?php wp_editor($question, 'question', '', true);	?>
			<input type="submit" value="<?php echo $buttonLabel;?>" onclick="submitForm(1);" class="button-primary" />            
            </div>
            


            <div id="feedbackTab">
            <!-- Correct Feedback General -->
			<h2><label for ="correctFeedback"><?php echo $correctFeedbackLabel?></label></h2>
			<?php wp_editor($correctFeedback, 'correctFeedback', '', true);	?>
            
            
            
            <!-- Incorrect Feedback Overall -->
            <?php
			if($hideIncorrectFeedback<>true) // Don't show the incorrect feedback stuff if its refletion
			{
			?>
                <h2><label for ="incorrectFeedback">Incorrect Feedback</label></h2>
                <?php wp_editor($incorrectFeedback, 'incorrectFeedback', '', true);	?>
            <?php
			}
			?>
            
			<input type="hidden" value="<?php echo $qType?>" name="qType" id="qType"/>
   			<input type="hidden" value="<?php echo $potID?>" name="potID" /><hr/>

			<input type="submit" value="<?php echo $buttonLabel;?>" onclick="submitForm(2);" class="button-primary" />

            
            </div> <!-- End of Feedback tab -->
            
			

         <?php
			echo '</form>'; // End of form
		 
			if($showResponseOptionsTab==true)
			{
				// Get the response options for this question
	            echo '<div id="responseOptionsTab">'; // Second Tab
												
				echo '<div id="responseOptionsDiv">';
				echo '<h2>Possible Answers</h2>';
				
				if($questionID=="")
				{
					echo 'Please <b>save</b> this question before entering response options';
				}
				elseif($qType=="radio" || $qType=="check")
				{
					qtl_draw::drawRadioCheckOptionsEditTable($questionID, $qType, $optionOrderType);
				}
				elseif($qType=="text")
				{
					qtl_draw::drawTextOptionsEditTable($questionID);
				}
				elseif($qType=="blank")
				{
					qtl_draw::drawBlankOptionsEditTable($questionID, $question);
				}
				
				

				
			
				
				echo '</div>';
			}
			
			echo '</div>'; // End of tabs div
			
			
   echo '</div>'; // end of form div
}

?>

</div>
