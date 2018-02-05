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
    <title>Main</title>

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

<!-- START MODAL -->
<!-- Modal -->
<div id="showInfoModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close btn-sm" data-dismiss="modal">CLOSE</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="text-center">USER ACCOUNT</h3>
                        <h2 class="text-center margin-top-24">
                            PUBLIC ID: #<span class="show-info-modal-public-id"></span>
                        </h2>

                        <table class="table">
                            <thead>
                            <tr>
                                <th colspan="2">CONFIRMATION ID</th>
                            </tr>
                            </thead>
                            <tbody class="show-info-modal-confirmation-ids">

                            </tbody>
                        </table>

                        <h3 class="margin-top-24 text-center">
                            ACCOUNT BALANCE: <span class="show-info-modal-points"></span>
                        </h3>

                        <h4 class="margin-top-24">
                            EDIT ACCOUNT BALANCE:
                        </h4>
                        <div class="input-group">
                            <input type="text" class="form-control edit-points-input" aria-label="">
                            <div class="input-group-btn">
                                <!-- Buttons -->
                                <button class="btn btn-primary btn-md edit-add-points-btn">ADD</button>
                                <button class="btn btn-danger btn-md edit-remove-points-btn">REMOVE</button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>
</div>
<!-- END MODAL -->

<div class="container-fluid" style="margin-top: 10%;">
    <div class="row">
        <div class="col-md-12 text-center">
            <h2>Admin Dashboard </h2>
            <h4><?php echo $totalPublicId; ?> Public Id's</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-hover table-header-fixed" width="100%" id="points-board-table">
                <thead>
                <tr>
                    <th></th>
                    <th>Public Id</th>
                    <th>Private 1</th>
                    <th>Private 2</th>
                    <th>Private 3</th>
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

                $('.show-info').on('click', function(){
                    $id = $(this).attr('data-id');

                    getUserInfo($id).done(function(response){
                        if(response.success == true){
                            console.log(response);
                            $id = response.info.userId;
                            $points = response.info.points;
                            $publicKey = response.info.keys.public;
                            $privateKeys = response.info.keys.private;
                            $privateKeysHtml = '';
                            for($i = 0; $i < $privateKeys.length; $i++){
                                if($privateKeys[$i]['key'] != ''){
                                    $privateKeysHtml += '<tr><td>#'+$privateKeys[$i]['key']+'</td><td><a class="delete-key" data-id="'+$id+'" data-column="'+$privateKeys[$i]['column']+'">DELETE</a></td><tr>';
                                }

                            }

                            $('.show-info-modal-public-id').text($publicKey);
                            $('.show-info-modal-confirmation-ids').html($privateKeysHtml);
                            $('.show-info-modal-points').text($points);

                            $('#showInfoModal').modal('show');



                            // edit|remove functions

                            $('.delete-key').unbind().on('click', function(){
                                $column = $(this).attr('data-column');
                                $id = $(this).attr('data-id');
                                $row = $(this).parent().parent();
                                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                                $confirm = confirm("Are you sure do you want to delete this key?");
                                if ($confirm == true) {
                                    $.ajax({
                                        url: 'Controller/user.php?action=delete-key',
                                        type: 'post',
                                        dataType: 'json',
                                        success: function (data) {
                                            if(data == true){
                                                $row.remove();
                                                oTable.ajax.reload();
                                            }else{
                                                alert('Something went wrong please try again.');
                                            }
                                        },
                                        data: {param: JSON.stringify({id: $id, column: $column})}
                                    });
                                } else {
                                    $(this).html('DELETE');
                                }
                            });

                            $('.edit-add-points-btn').unbind().on('click', function(){
                                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                                $pointsValue = $('.edit-points-input').val();
                                $.ajax({
                                    url: 'Controller/user.php?action=edit-points',
                                    type: 'post',
                                    dataType: 'json',
                                    success: function (data) {
                                        $('.show-info-modal-points').text(data.info.points);
                                        $('.edit-points-input').val('');
                                        $('.edit-add-points-btn').html('ADD');
                                        oTable.ajax.reload();

                                    },
                                    data: {param: JSON.stringify({id: $id, action: 'add', points: $pointsValue})}
                                });


                            });

                            $('.edit-remove-points-btn').unbind().on('click', function(){
                                $(this).html('<i class="fa fa-spinner fa-spin"></i>');
                                $pointsValue = $('.edit-points-input').val();
                                if($pointsValue > parseFloat($('.show-info-modal-points').text())){
                                    alert('Invalid input value.');
                                    $('.edit-points-input').focus();
                                    $('.edit-remove-points-btn').html('REMOVE');
                                }else{
                                    $.ajax({
                                        url: 'Controller/user.php?action=edit-points',
                                        type: 'post',
                                        dataType: 'json',
                                        success: function (data) {
                                            $('.show-info-modal-points').text(data.info.points);
                                            $('.edit-points-input').val('');
                                            $('.edit-remove-points-btn').html('REMOVE');
                                            oTable.ajax.reload();

                                        },
                                        data: {param: JSON.stringify({id: $id, action: 'remove', points: $pointsValue})}
                                    });

                                }


                            });

                        }else{
                            alert('Something went wrong, please try again later');
                        }
                    });


                });
            },
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                $(nRow).addClass( 'show-info' );
                $(nRow).attr('data-id', aData[0]);
                return nRow;
            },
            "sAjaxSource": "Controller/table.php?action=points-board-admin",
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "isAdmin", "value": true } );
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
