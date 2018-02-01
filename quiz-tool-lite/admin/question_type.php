<?php
require_once AIQUIZ_PATH.'scripts/tabs.php';

/*session_start();

if(isset($_SESSION['questionid']))
{
	unset($_SESSION['questionid']);
}
*/
$potID = $_GET['potID'];
$potInfo = qtl_queries::getPotInfo($potID);
$potName = qtl_utils::convertTextFromDB($potInfo['potName']);

$homeURL =  network_home_url();

if($homeURL =="")
{
	$homeURL = home_url();	
}

$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/';

?>
<h2><?php echo $potName." - Add a new question"?></h2>
<a href="admin.php?page=ai-quiz-home" class="backIcon">Return to all question pots</a>
<hr/>

<h2>Please select a question type from the list.</h2>


    <div id="tabs">
        <ul>
        <li><a href="#multichoice">Multiple Choice</a></li>
        <li><a href="#text">Text</a></li>
        <li><a href="#reflective">Reflective</a></li>    
        </ul>
        
        <div id="multichoice">
            <table>
            <tr>
            <td valign="top" width="400px">
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=radio&tab=question&potID='.$potID.'" >';
            echo 'Single Answer (radio buttons)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/radio_example.gif">';
            echo '</a><br/>';
            ?>
            </td>
            <td valign="top">
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=check&tab=question&potID='.$potID.'" >Multiple Answer (check boxes)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/check_example.gif">';
            echo '</a><br/>';
            ?>
            </td>
            </tr>
            </table>
        </div>
        
        <div id="text">
        
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=text&tab=question&potID='.$potID.'" >Free Text<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/text_example.gif">';
            echo '</a><br/>';
            ?>    
        
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=blank&tab=question&potID='.$potID.'" >Fill in the blanks<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/blank_example.png">';
            echo '</a><br/>';
            ?>    
            
            
        
        </div>
    
        <div id="reflective">
        	<div  id="message" class="error smallText">
	        Reflective questions are not marked - instead, answers can be saved and displayed to students on a seperate page.<br/>
            See the <a href="admin.php?page=ai-quiz-help#shortcodes">shortcode reference</a> for help with this.
            </div>
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflection&tab=question&potID='.$potID.'" >Reflection (no textbox)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/reflection_example.gif">';
            echo '</a><br/>';
            ?>
            <hr/>
            <?php
            echo '<a href="admin.php?page=ai-quiz-question-edit&qType=reflectionText&tab=question&potID='.$potID.'" >Reflection (with textbox)<br/>';
            echo '<img src="'.$imgDir =  $homeURL.'/wp-content/plugins/quiz-tool-lite/images/reflection_box_example.gif">';
            echo '</a><br/>';
            ?>
        </div>
    </div>

