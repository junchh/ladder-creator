<?php
/**
 * Codeforces Ladder Creator
 * @version 1.0
 * @author Jun Ho Choi Hedyatmo
 */
function extractData($link){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $data = curl_exec($ch);
    curl_close($ch);

    return json_decode($data);
}

function cmp($a, $b){
    if($a->contestId==$b->contestId){
        return ($a->index < $b->index) ? -1 : 1;
    } else {
        return ($a->contestId < $b->contestId) ? -1 : 1;
    }
}

if(isset($_GET['from']) && isset($_GET['to']) && isset($_GET['handle'])){
    $userdata = extractData('http://codeforces.com/api/user.status?handle=' . $_GET['handle']);
    if($userdata->status=="FAILED"){
        echo 'that handle doesn\'t exist, go back : <a href="http://junho.id/">Here</a>';
        exit();
    }
    $from = $_GET['from'];
    $to = $_GET['to'];

    $problem = extractData('http://codeforces.com/api/problemset.problems');
    foreach($userdata->result as $val){
        if($val->verdict=="OK"){
            $solved[$val->problem->contestId . $val->problem->index]=1;
        }
    }
    $contestData = extractData('http://codeforces.com/api/contest.list?gym=false');
    foreach($contestData->result as $val){
        $contest[$val->id]=$val->name;
    }

    if($from > $to){
        exit();
    }

    $problemdata = $problem->result->problems;
    usort($problemdata, "cmp");
    $cntproblem = 0;
} else {
    exit('invalid input, go back: <a href="http://junho.id/">Here</a>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Problem Ladder - <?php echo $_GET['handle']; ?></title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/perfect-scrollbar/perfect-scrollbar.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	<div class="limiter">
		<div class="container-table100">
			<div class="wrap-table100">
					<div class="table">

						<div class="row header">
							<div class="cell">
								Problem Title
							</div>
							<div class="cell">
								Solved
							</div>
							<div class="cell">
								Contest
							</div>
						</div>
<?php 
foreach($problemdata as $val){
    if(isset($val->rating)){
        if($val->rating >= $from && $val->rating <= $to){
            if(isset($solved[$val->contestId . $val->index])){
                echo '<div class="row solved">
        <div class="cell" data-title="Problem Title">
            <a target="_blank" href="https://codeforces.com/problemset/problem/' . $val->contestId . $val->index . '/">'  . $val->name . '</a>
        </div>
        <div class="cell" data-title="Solved">
            Yes
        </div>
        <div class="cell" data-title="Contest">
        ' . $contest[$val->contestId] . '
        </div>
    </div>';
            } else {
                echo '<div class="row">
                <div class="cell" data-title="Problem Title">
                <a target="_blank" href="https://codeforces.com/problemset/problem/' . $val->contestId .'/' . $val->index . '/">'  . $val->name . '</a>
                </div>
                <div class="cell" data-title="Solved">
                No
                </div>
                <div class="cell" data-title="Contest">
                ' . $contest[$val->contestId] . '
                </div>
            </div>';
            }
        }
    }
}
?>
				

					</div>
			</div>
		</div>
	</div>


	

<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>