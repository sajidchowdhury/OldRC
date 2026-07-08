<?php 

?>  <!-- MESSAGE BOX-->
<div class="message-box animated fadeIn" data-sound="alert" id="mb-signout">
    <div class="mb-container">
        <div class="mb-middle">
            <div class="mb-title"><span class="fa fa-sign-out"></span> Log <strong>Out</strong> ?</div>
            <div class="mb-content">
                <p>Are you sure you want to log out?</p>                    
                <p>Press No if you want to continue work. Press Yes to logout current user.</p>
            </div>
            <div class="mb-footer">
                <div class="pull-right">
                    <a href="logout.php" class="btn btn-success btn-lg">Yes</a>
                    <button class="btn btn-default btn-lg mb-control-close">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END MESSAGE BOX-->

<div class="modal fade" id="modal_large"  data-sound="alert" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content modal-content">
            <div class="modal-header">
                <button type="button"  class="close"  onclick="location.reload();" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" >

                <div class="dash" style="background-color:#f5f5f5">
                    <!-- Content goes in here -->
                </div>

            </div>
            <div class="modal-footer">
                <button  type="button" onclick="location.reload();" class="btn btn-default" data-dismiss="modal">Close</button>
 <!-- <button  type="button" class="btn btn-danger right"  onclick="printDiv('printableArea')">Print</button>-->
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modal_large_no_need_refresh"  data-sound="alert" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content modal-content">
            <div class="modal-header">
                <button type="button"  class="close" onclick="CLOSEME()" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" >

                <div class="dash2" style="background-color:#f5f5f5">
                    <!-- Content goes in here -->
                </div>

            </div>
            <div class="modal-footer">
                <button  type="button"  class="btn btn-default" onclick="CLOSEME()" data-dismiss="modal">Close</button>
                  <!-- <button  type="button" class="btn btn-danger right"  onclick="printDiv('printableArea')">Print</button>-->

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter"  data-sound="alert" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content modal-content">
            <div class="modal-header">
                <button type="button"  class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" >

                <div id="dash22" style="background-color:#f5f5f5">
                    <!-- Content goes in here -->
                </div>

            </div>
            <div class="modal-footer">
                <button  type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
 <!-- <button  type="button" class="btn btn-danger right"  onclick="printDiv('printableArea')">Print</button>-->
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="second_exampleModalCenter"  data-sound="alert" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-ku">
        <div class="modal-content modal-content">
            <div class="modal-header">
                <button type="button"  class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body" >

                <div id="dash222" style="background-color:#f5f5f5">
                    <!-- Content goes in here -->
                </div>

            </div>
            <div class="modal-footer">
                <button  type="button"  class="btn btn-default" data-dismiss="modal">Close</button>
 <!-- <button  type="button" class="btn btn-danger right"  onclick="printDiv('printableArea')">Print</button>-->
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
<script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>                            
<script type="text/javascript" src="js/plugins/highlight/jquery.highlight-4.js"></script>

<!-- END PLUGINS -->




<!-- START THIS PAGE PLUGINS-->

<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-colorpicker.js"></script>


<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
<script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script>

<script type="text/javascript" src="js/plugins/dropzone/dropzone.min.js"></script>
        <script type="text/javascript" src="js/plugins/fileinput/fileinput.min.js"></script>    

        

<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>

<script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script>


<script type="text/javascript" src="js/plugins/smartwizard/jquery.smartWizard-2.0.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery-validation/jquery.validate.js"></script>


<script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script>
<script type="text/javascript" src="js/plugins/moment.min.js"></script>


<script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script>
<!-- END THIS PAGE PLUGINS-->
<!-- START TEMPLATE 

-->        
<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>    

<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/morris.min.js"></script>       
        <script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script>
        <script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script>
        <script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
        <script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>   

<script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
	<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>     

    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.8/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script type='text/javascript' src='js/plugins/maskedinput/jquery.maskedinput.min.js'></script>

 <!-- START TEMPLATE -->
        
        <script type="text/javascript" src="js/plugins.js"></script>        
        <script type="text/javascript" src="js/actions.js"></script>
        
        <!-- END TEMPLATE -->
<script type="text/javascript" src="js/faq.js"></script>

<!-- THIS PAGE PLUGINS -->


<script type="text/javascript" src="js/plugins/summernote/summernote.js"></script>
<!-- END PAGE PLUGINS -->



<script type="text/javascript" src="js/plugins/tocify/jquery.tocify.min.js"></script>


 <!-- jQuery -->

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<style>
.form-group.required.control-label:after{
   content: "*";
   color: red;
}
</style>

<script>
    $('.date').datepicker({  
       format: 'dd-mm-yyyy'  
     }); 

    $(".monthonly").datepicker( {
    format: "mm-yyyy",
    startView: "months", 
    minViewMode: "months"
    });


    function printMe() {
    var frame = document.getElementsByClassName('mydivclass').item(0);
    var data = frame.innerHTML;
    var win = window.open('', '', 'height=500,width=900');
    win.document.write('<style>@page{size:landscape;}</style><html><head><title></title>');
    win.document.write('</head><body >');
    win.document.write(data);
    win.document.write('</body></html>');
    win.print();
    win.close();
    return true;
}
 $(document).ready(function () {
            $('#example').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel', 'pdf', 'print'
                ]
            });
        });



</script>

