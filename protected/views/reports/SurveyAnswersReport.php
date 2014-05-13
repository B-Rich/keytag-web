<?php

class groupReport
{
    private $model = null;

    private $tableHeader = array();
    private $reportTitle = '';
    
    public function __construct($model, $tableHeader, $reportTitle)
    { 
        $this->model = $model;
        $this->tableHeader = $tableHeader;
        $this->reportTitle = $reportTitle;
    }
    
    public function render()
    {  
        $studentsAnswers = $this->model;
        echo "<div class='clear'></div>";

        echo  "<div id='reportsheet' class='content-black'>";
        echo "<table>"; 

        $this->renderHeader($this->reportTitle);

        $this->renderTableHeader();
        $cntRows = 0;

        foreach($studentsAnswers as $key => $studentAnswers) {

            $this->renderRow($key + 1, $studentAnswers);
            $cntRows++;
        }        

        $this->renderFooter($cntRows);
        echo "</table>";
        echo  "</div>";
		echo '<div class="clear"></div>';
    }

    private function renderHeader($title)
    {
        $numRows = count($this->tableHeader);
        echo '<tr class="program header-big" align="center">';
        echo '<td colspan="'.$numRows.'">'.$title.' /'. date('m-d-Y', time()) .'/</td>';
        echo '</tr>';
    }

    private function renderTableHeader()
    {
        echo "<tr>";
        
        foreach ($this->tableHeader as $col) {
            echo "<th class='personType'>".$col."</th>";
        }

        echo "</tr>";
    }

    private function renderRow($rowNumber, $item)
    {
        echo '<tr class="person">';
        echo '<td>'.$rowNumber.'</td>';
        echo '<td>'.$item['FirstLast'].'</td>';

        foreach ($item['userInfo'] as $info) {
            echo '<td>'.nl2br($info).'</td>';
        }

        $cntQuestion = 0;
        foreach ($item['answers'] as $key => $answer) {
            echo '<td>'.nl2br($answer).'</td>';
            $cntQuestion++;
        }

        echo '</tr>';
    }

    private function renderFooter($totalRows)
    {
        echo '<tr class="program-footer">';

        for ($i=0; $i < (count($this->tableHeader) - 3); $i++) { 
            echo '<td></td>';
        }

        echo '<td colspan="2" style="text-align: center">Number of Students: </td>';
        echo '<td>'.$totalRows.'</td>';
        echo '</tr>';
    }
}


$gp = new groupReport($model, $tableHeader, $reportTitle);
$gp->render();

?>
<script>
	$(document).ready(function(){
		$('#reportsheet').parents('.reportsWrapper').addClass('surveyReportWrapper');
	});
</script>
