<?php 
/** 
Purpose of file:    List page for Duties and Responsibilities Library
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
?>
<?php load_plugin('css',array('datepicker','datatables'));?>
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?=base_url('home')?>">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="<?=base_url('libraries/agency_profile')?>">Libraries</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Duties and Responsibilities</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
       &nbsp;
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject bold uppercase"> DUTIES AND RESPONSIBILITIES</span>
                </div>
                
            </div>
            <div class="loading-image"><center><img src="<?=base_url('assets/images/spinner-blue.gif')?>"></center></div>
            <div class="portlet-body" id="div-responsibility" style="display: none">
                <div class="table-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="btn-group">
                                <a href="<?=base_url('libraries/duties_responsibilities/add')?>"><button id="sample_editable_1_new" class="btn sbold btn-primary"> <i class="fa fa-plus"></i> Add New
                                    
                                </button></a>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <table class="table table-striped table-bordered table-condensed  table-hover table-checkable order-column" id="libraries_duties_responsibilities">
                    <thead>
                        <tr>
                            <th> No. </th>
                            <th> Position </th>
                            <th> Duties and Responsibilities </th>
                            <th> Action </th>
                    </thead>
                    <tbody>
                    <?php 
                    $i=1;
                    foreach($arrDuties as $duty):?>
                        <tr class="odd gradeX">
                            <td> <?=$i?> </td>
                            <td> <?=$duty['positionCode']?></td>
                            <td> <?=$duty['duties']?></td>
                            <td style="white-space: nowrap;text-align:center;">
                                <a href="<?=base_url('libraries/duties_responsibilities/edit/'.$duty['duties_index'])?>"><button class="btn btn-sm btn-success"><span class="fa fa-edit" title="Edit"></span> Edit</button></a>
                                <a href="<?=base_url('libraries/duties_responsibilities/delete/'.$duty['duties_index'])?>"><button class="btn btn-sm btn-danger"><span class="fa fa-trash" title="Delete"></span> Delete</button></a>
                            </td>
                        </tr>
                    <?php 
                    $i++;
                    endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
<?php load_plugin('js',array('datatables'));?>


<script>
    $(document).ready(function() {
        $('#libraries_duties_responsibilities').dataTable( {
            "initComplete": function(settings, json) {
                $('.loading-image').hide();
                $('#div-responsibility').show();
            }} );
    });
</script>