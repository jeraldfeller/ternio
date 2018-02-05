<?php
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/Dashboard.php';
require $_SERVER['DOCUMENT_ROOT'] . '/dashboard/Model/session.php';
$dashboard = new Dashboard();
$totalPublicId = json_decode($dashboard->getTotalPublicIds(), true)['totalCount'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Generated List</title>

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
        .show-info{
            cursor: pointer;
        }
        .delete-key{
            cursor: pointer;
        }

    </style>

</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
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
                <li><a href="admin-dashboard.php">Main</a></li>
                <li><a href="generate-list.php">Generate ID List</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>


<div class="container-fluid" style="margin-top: 10%;">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Admin Generated ID List </h2>
            <h4><?php echo $totalPublicId; ?> Public Id's</h4>
        </div>
    </div>
    <div class="row margin-top-24">
        <div class="col-md-12">
            <div class="col-md-2 col-md-offset-5">
                <div class="register-container">
                    <div class="form-group">
                        <label for="generateCount"># Of ID's To Generate</label>
                        <input type="number" class="form-control" id="generateCount">
                    </div>

                </div>
            </div>
            <div class="col-md-2 col-md-offset-5">
                <button class="btn btn-default generate-btn" style="width: 100%;">GENERATE</button>
            </div>
        </div>
    </div>

    <div class="table-container display-none">
        <div class="row margin-top-24margin-top-24">
            <div class="col-md-12">
                <div class="col-md-2 col-md-offset-5 margin-top-24">
                    <table class="table table-striped table-hover" width="100%" id="generated-key-table">
                        <thead>
                        <tr>
                            <th class="display-none">KEY</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row margin-top-24">
            <div class="col-md-12 text-center">
                <h5 class="generatedDate"></h5>
            </div>
        </div>
    </div>

    <div class="row margin-top-12 margin-bottom-24">
        <div class="col-md-2 col-md-offset-5">
            <a style="cursor: pointer;" class="clear-list">CLEAR LIST <span class="spinner"></span></a>
        </div>
        <div class="col-md-2 col-md-offset-5">
            <a style="cursor: pointer;" class="download-list">DOWNLOAD LIST <span class="spinner"></span></a>
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
        var table = $('#generated-key-table');
        var oTable = $('#generated-key-table').DataTable({
            fixedHeader: {
                header: true,
            },
            "bProcessing": true,
            "bServerSide": true,
            "fnDrawCallback": function( oSettings ) {
                if(oSettings.aiDisplay.length > 0){
                    $('.table-container').removeClass('display-none');
                    $('.generatedDate').html('GENERATED AT ' + oSettings.json.generatedTime + ' ON ' + oSettings.json.generatedDate);
                }

                $('.generate-btn').html('GENERATE');
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {

            },
            "sAjaxSource": "Controller/table.php?action=generated-list",
            "fnServerParams": function ( aoData ) {

            },

            responsive: {
                details: {

                }
            },
            "bPaginate": false,
            "bFilter": false,
            "iDisplayLength": 20,
            /*"lengthMenu": [
             [20, 500, 1000, -1],
             [20, 500, 1000, "All"] // change per page values here
             ],
             */
            select: {
                style: 'os',
                selector: 'td:first-child'
            },
            //dom: '<"wrapper"lBfrtip>',
            //dom: '<lB<frt>ip>',
            "dom": "<'row' <'col-md-12 pull-left'f>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"

        });

        $('.generate-btn').unbind().on('click', function(){
            $(this).html('<i class="fa fa-spinner fa-spin"></i>');
            $generateCount = $('#generateCount').val();
            if($generateCount > 0){
                $.ajax({
                    url: 'Controller/admin.php?action=generate-new-key-list',
                    type: 'post',
                    dataType: 'json',
                    success: function (data) {
                        $('.table-container').removeClass('display-none');
                        oTable.ajax.reload();
                    },
                    data: {param: JSON.stringify({count: $generateCount})}
                });
            }else{
                alert('Input must be a valid number.');
            }

        });

        $('.clear-list').unbind().on('click', function(){
            $elem = $(this).find('span');
            $elem.html('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                url: 'Controller/admin.php?action=generate-clear-list',
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    $('.table-container').addClass('display-none');
                    $elem.html('');
                    oTable.ajax.reload();
                }
            });
        });

        $('.download-list').unbind().on('click', function(){
            location.href = 'download-list.php';
        });
    });
</script>
</body>
</html>
