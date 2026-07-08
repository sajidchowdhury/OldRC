<?php
include('auth.php');
include('layout/head.php');
include('function_query.php');
$conn_me = Database::getInstance();
$topbar = SETUP::TOP_BAR($_GET['page_identity'], '');

$today = date("Y-m-d");
$oneDaysAgo = date("Y-m-d", strtotime("0 days"));
$twoDaysAgo = date("Y-m-d", strtotime("-1 days"));
$threeDaysAgo = date("Y-m-d", strtotime("-2 days"));
$sevenDaysAgo = date("Y-m-d", strtotime("-6 days"));


?>

<body onload="DatewiseDashbord('<?php print $today; ?>','<?php print  $oneDaysAgo; ?>' );">

    <div class="page-container">

        <!-- START PAGE SIDEBAR -->
        <div class="page-sidebar"><?php include('layout/common_side_bar.php'); ?></div>
        <!-- END PAGE SIDEBAR -->

        <!-- PAGE CONTENT -->
        <div class="page-content">

            <?php print $topbar['top_bar_content']; ?>

            <!-- START BREADCRUMB -->
            <ul class="breadcrumb push-down-0">
                <li class="active"><a href="#">Dashbord </a></li>
            </ul>
            <!-- END BREADCRUMB -->


            <div class="page-content-wrap">

                <div class="row">
                    <div class="col-md-12">

                        <div class="panel-body">
                            <a class="label label-default" onclick="DatewiseDashbord('<?php print $today; ?>','<?php print  $oneDaysAgo; ?>' );" style="background-color:danger">TODAY</a>
                            <a class="label label-default" onclick="DatewiseDashbord('<?php print $today; ?>','<?php print $twoDaysAgo; ?>');" style="background-color:Salmon">Last 2 Days</a>
                            <a class="label label-default" onclick="DatewiseDashbord('<?php print $today; ?>','<?php print $threeDaysAgo; ?>');" style="background-color:green">Last 3 Days</a>
                            <a class="label label-default" onclick="DatewiseDashbord('<?php print $today; ?>','<?php print $sevenDaysAgo; ?>');" style="background-color:#28B463">Last 7 Days</a>

                            <select id="product_num" name="product_num" class="label label-default" data-live-search="true">
                                <?php

                                for ($i = 7; $i < 100; $i++) { ?>
                                    <option value="<?php print $i; ?>">Show <?php print $i; ?> Product</option>
                                <?php }
                                ?>
                            </select>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">

                        <div class="panel-body">

                            <div class="col-md-4">

                                <!-- START VISITORS BLOCK -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title-box">
                                            <h3>Head Office</h3>
                                            <span> <b id="donut1"> </b></span>
                                        </div>

                                    </div>
                                    <div class="panel-body padding-0">
                                        <div class="chart-holder" id="dashboard-donut-1" style="height: 200px;"></div>
                                    </div>
                                </div>
                                <!-- END VISITORS BLOCK -->

                            </div>
                            <!-- END CONTENT FRAME -->

                            <div class="col-md-4">

                                <!-- START VISITORS BLOCK -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title-box">
                                            <h3>Patuatuli Branch</h3>
                                            <span><b id="donut2"> </b> </span>
                                        </div>

                                    </div>
                                    <div class="panel-body padding-0">
                                        <div class="chart-holder" id="dashboard-donut-2" style="height: 200px;"></div>
                                    </div>
                                </div>
                                <!-- END VISITORS BLOCK -->

                            </div>
                            <!-- END CONTENT FRAME -->

                            <div class="col-md-4">

                                <!-- START VISITORS BLOCK -->
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="panel-title-box">
                                            <h3>Nowabpur Brunch  </h3>
                                            <span><b id="donut3"> </b> </span>
                                        </div>

                                    </div>
                                    <div class="panel-body padding-0">
                                        <div class="chart-holder" id="dashboard-donut-3" style="height: 200px;"></div>
                                    </div>
                                </div>
                                <!-- END VISITORS BLOCK -->

                            </div>
                            <!-- END CONTENT FRAME -->
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">

                        <!-- START VISITORS BLOCK -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>Top selling product</h3>
                                    <span> <b id="donut4"> </b> </span>
                                </div>

                            </div>
                            <div class="panel-body padding-0">

                            <div id="salesBody"></div>

                            </div>
                        </div>
                        <!-- END VISITORS BLOCK -->
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-12">

                        <!-- START VISITORS BLOCK -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>Least selling product</h3>
                                    <span> <b id="donut5"> </b> </span>
                                </div>

                            </div>
                            <div class="panel-body padding-0">

                            <div id="LeastBody"></div>

                            </div>
                        </div>
                        <!-- END VISITORS BLOCK -->
                    </div>


                </div>

            </div>
        </div>




         <!-- START PRELOADS -->
         <audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
        <audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
        <!-- END PRELOADS -->                  
        
    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>        
        <!-- END PLUGINS -->
              <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>    

        <!-- START THIS PAGE PLUGINS-->        
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>        
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        <script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script>
        
        <script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/morris.min.js"></script>       
        <script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script>
        <script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script>
        <script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
        <script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>                
        <script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script>                
        <script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script>                 
        
        <script type="text/javascript" src="js/plugins/moment.min.js"></script>
        <script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script>
        <!-- END THIS PAGE PLUGINS-->        

        <!-- START TEMPLATE -->
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>
        
        <!-- END TEMPLATE -->
    <!-- END SCRIPTS -->        
        
        <!-- END TEMPLATE -->


        <script type="text/javascript">
            function DatewiseDashbord(toDate, fromDate) {
                document.getElementById('dashboard-donut-1').innerHTML = '';
                document.getElementById('dashboard-donut-2').innerHTML = '';
                document.getElementById('dashboard-donut-3').innerHTML = '';
                document.getElementById('salesBody').innerHTML = '';
                document.getElementById('LeastBody').innerHTML = '';


                var productNumber = $('#product_num').val();

                $.ajax({
                    url: "function_tem.php",
                    type: "POST",
                    data: {
                        fromDate: fromDate,
                        toDate: toDate,
                        productNumber: productNumber,
                        action: 'GET_RELATED_ADMIN_NAME'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(html) {
                        document.getElementById('donut1').innerHTML = ' FROM : ' + fromDate + ' TO ' + toDate;
                        document.getElementById('donut2').innerHTML = ' FROM : ' + fromDate + ' TO ' + toDate;
                        document.getElementById('donut3').innerHTML = ' FROM : ' + fromDate + ' TO ' + toDate;
                        document.getElementById('donut4').innerHTML = 'Showing ' + productNumber + ' Products FROM : ' + fromDate + ' TO ' + toDate;
                        document.getElementById('donut5').innerHTML = 'Showing ' + productNumber + ' Products FROM : ' + fromDate + ' TO ' + toDate;

                        // Donut dashboard charts
                        function createDonutChart(element, labelValues) {
                            Morris.Donut({
                                element: element,
                                data: labelValues,
                                colors: ['#33414E', '#1caf9a', '#FEA223', '#e31d1d'],
                                resize: true
                            });
                        }

                        createDonutChart('dashboard-donut-1', [{
                                label: "Sales",
                                value: html.invoice_amount1
                            },
                            {
                                label: "Collection",
                                value: html.receive_amount1
                            },
                            {
                                label: "Return",
                                value: html.return_amount1
                            },
                            {
                                label: "Due",
                                value: html.customer_due1
                            }
                        ]);

                        createDonutChart('dashboard-donut-2', [{
                                label: "Sales",
                                value: html.invoice_amount2
                            },
                            {
                                label: "Collection",
                                value: html.receive_amount2
                            },
                            {
                                label: "Return",
                                value: html.return_amount2
                            },
                            {
                                label: "Due",
                                value: html.customer_due2
                            }
                        ]);

                        createDonutChart('dashboard-donut-3', [{
                                label: "Sales",
                                value: html.invoice_amount3
                            },
                            {
                                label: "Collection",
                                value: html.receive_amount3
                            },
                            {
                                label: "Return",
                                value: html.return_amount3
                            },
                            {
                                label: "Due",
                                value: html.customer_due3
                            }
                        ]);


                        document.getElementById('salesBody').innerHTML = html.top_sold;
                        document.getElementById('LeastBody').innerHTML = html.least_sold;
                        $('#myDatatable').DataTable();
                        $('#myDatatabletwo').DataTable();


                   
                    }
                });
            }
            
        </script>

</body>

</html>