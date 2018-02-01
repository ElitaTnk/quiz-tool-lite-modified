<h2>Export / Import Quiz Questions</h2>
<h4>Export Questions</h4>
<a href="admin.php?page=ai-quiz-export&download=csv" class="button-primary">Export Questions as CSV</a><br/>


<hr/>
<h4>Import Questions</h4>

<?php
AI_Quiz_importExport::checkCSVUpload(); // Check to see if a CSV file has been uploaded
?>

<form name="csvUploadForm" method="post" action="admin.php?page=ai-quiz-export" enctype="multipart/form-data">
Upload your CSV import file.<br/><br/>
<input type="file" name="csvFile" size="20"/>
<input type="hidden" name="mode" value="submit"><br/>
<input type="submit" value="Import Questions" class="button-primary">
</form>
