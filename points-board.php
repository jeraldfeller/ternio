<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Dashboard.php';
if(isset($_SESSION['userData'])){
    $userData = $_SESSION['userData'];
}else{
    $userData = array('id' => 0);
}

$dashboard = new Dashboard();
$totalPublicId = json_decode($dashboard->getTotalPublicIds(), true)['totalCount'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Main</title>

	<link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link rel="icon" type="image/x-icon" href="" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <style>
        .margin-top-24{
            margin-top: 24px;
        }
        .margin-top-12{
            margin-top: 12px;
        }
        .margin-bottom-12{
            margin-bottom: 12px;
        }
        .margin-bottom-24{
            margin-bottom: 24px;
        }
        .display-none{
            display: none;
        }
        .input-sm{
            margin-left: 0 !important;
        }

    </style>

</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
	    <div class="dashlogo"></div>
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php
                    if($userData['id'] != 0){
                        if($userData['userLevel'] == 'admin'){
                            echo '<li><a href="admin-dashboard.php">Main</a></li>';
                        }else{
                            echo '<li><a href="index.php">Main</a></li>';
                        }
                    }

                ?>
                <li><a href="points-board">Current Points Board</a></li>
                <?php
                    if($userData['id'] == 0){
                        echo '<li><a href="login.php">Login</a></li>';
                    }else{
                        echo '<li><a href="logout.php">Logout</a></li>';
                    }
                ?>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container-fluid" style="margin-top: 10%;">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2 style="color:#FFFFFF;">Points Board </h2>
            <h4 style="color:#FFFFFF;"><?php echo $totalPublicId; ?> Public Id's</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover table-header-fixed" width="100%" id="points-board-table">
                <thead>
                <tr>
                    <th></th>
                    <th>Public Id</th>
                    <th>Points</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>


</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="assets/ajax/app.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        var table = $('#points-board-table');
        var oTable = $('#points-board-table').DataTable({
            fixedHeader: {
                header: true,
            },
            "bProcessing": true,
            "bServerSide": true,
            "fnDrawCallback": function( oSettings ) {

            },
            "sAjaxSource": "Controller/table.php?action=points-board",
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "isAdmin", "value": false } );
            },

            responsive: {
                details: {

                }
            },
            language: {
                searchPlaceholder: "Search By ID",
                "search": ""
            },
            "bPaginate": false,
            "iDisplayLength": 20,
            /*"lengthMenu": [
             [20, 500, 1000, -1],
             [20, 500, 1000, "All"] // change per page values here
             ],
             */
            "columnDefs": [
                {
                    'visible': false,
                    'targets': 0
                }
            ],
            "order": [[ 2, "desc" ]],
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            //dom: '<"wrapper"lBfrtip>',
            //dom: '<lB<frt>ip>',
            "dom": "<'row pull-left' <'col-md-12'f>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"

        });

    });
</script>
</body>
</html>
