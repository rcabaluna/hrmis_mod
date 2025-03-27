<?php 
/** 
Purpose of file:    Travel Order View
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/
$to_details = isset($arrto) ? explode(';',$arrto['requestDetails']) : array();
$form_action = $action=='add' ? 'employee/travel_order/submit' : 'employee/travel_order/edit?req_id='.$_GET['req_id'];
$hrmodule = isset($_GET['module']) ? $_GET['module'] == 'hr' ? 1 : 0 : 0;
?>
<!-- BEGIN PAGE BAR -->
<?=load_plugin('css', array('datetimepicker','timepicker','datepicker','select2','multi-select'))?>

<style>
    #ms-selemps{
        width: 80%;
    }
    
</style>

<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?=base_url('home')?>">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="javascript:;">Request</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span><?=ucwords($hrmodule ? 'view' : $action)?> Travel Order</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
       &nbsp;
    </div>
</div>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-settings font-dark"></i>
                    <span class="caption-subject bold uppercase">Travel Order</span>
                </div>
            </div>
            <div class="portlet-body">
                <?=form_open_multipart($form_action, array('method' => 'post', 'id' => 'frmTO'))?>
                <input class="hidden" name="strStatus" value="Filed Request">
                <input class="hidden" name="strCode" value="TO">
                <input type="hidden" id="txtfilesize" name="txtfilesize">
                <input type="hidden" id="txtdgstorage" name="txtdgstorage">
                <!-- CUSTOM -->
                        <div class="row">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label class="control-label">Employees <span class="required"> * </span></label>
                                    <select multiple="multiple" class="multi-select form-control" id="selemps" name="selemps[]">
                                        <optgroup label="SELECT ALL">
                                        <?php
                                            foreach($arrEmployees as $emp):
                                                $selected = isset($arrob_data) ? in_array($emp['empNumber'], array_column($arrob_data,'empNumber')) ? 'selected' : '' : '';
                                                echo '<option value="'.$emp['empNumber'].'">'.
                                                        getfullname($emp['firstname'],$emp['surname'],$emp['middlename'],$emp['middleInitial'],$emp['nameExtension']).'</option>';
                                            endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <textarea hidden id="json_employee"><?=json_encode($arrEmployees)?></textarea>
                        <br>
                 <!-- END CUSTOM -->
                <div class="row">
                    
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">Date From :  <span class="required"> * </span></label>
                            <div class="input-icon right">
                                <i class="fa"></i>
                               <input type="text" class="form-control date-picker" name="dtmTOdatefrom" id="dtmTOdatefrom" value="<?=count($to_details) > 0 ? $to_details[1] : '' ?>" data-date-format="yyyy-mm-dd" autocomplete="off" <?=$hrmodule ? 'disabled' : ''?>>   
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">Date To :  <span class="required"> * </span></label>
                            <div class="input-icon right">
                                <i class="fa"></i>
                               <input type="text" class="form-control date-picker" name="dtmTOdateto" id="dtmTOdateto" value="<?=count($to_details) > 0 ? $to_details[2] : '' ?>" data-date-format="yyyy-mm-dd" autocomplete="off" <?=$hrmodule ? 'disabled' : ''?>>   
                            </div>
                        </div>
                    </div>
                </div>
   
                <div class="row">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label class="control-label">Destination :  <span class="required"> * </span></label>
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="2" name="strDestination" id="strDestination" type="text" maxlength="1000" <?=$hrmodule ? 'disabled' : ''?>><?=count($to_details) > 0 ? $to_details[0] : '' ?></textarea>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <div class="form-group">
                            <label class="control-label">Purpose :  <span class="required"> * </span></label>
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <textarea class="form-control" rows="2" name="strPurpose" id="strPurpose" type="text" maxlength="1000" <?=$hrmodule ? 'disabled' : ''?>><?=count($to_details) > 0 ? $to_details[3] : '' ?></textarea>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Travel Expenses to be Incurred</th>
                                <th colspan="3">Appropriation/Fund to which travel expenses would be charged to:</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input type="radio" name="funding_source" value="General Fund" id="funding_general"> <label for="funding_general"><b>General Fund</b></label></th>
                                <th><input type="radio" name="funding_source" value="Project Funds" id="funding_project"> <label for="funding_project"><b>Project Funds</b> <small>(Please specify)</small></label></th>
                                <th><input type="radio" name="funding_source" value="Others" id="funding_others"> <label for="funding_others"><b>Others</b> <small>(e.g., sponsor/requesting agency)</small></label></th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input disabled type="text" class="form-control" name="project_fund_general" id="actual_fund_details"></th>
                                <th><input disabled type="text" class="form-control" name="project_fund_project" id="project_fund_details"></th>
                                <th><input disabled type="text" class="form-control" name="project_fund_others" id="other_fund_details"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Actual</b></td>
                                <td class="text-center"><input type="checkbox" value="general" id="actual_accommodation_general" name="actual"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="actual_accommodation_project" name="actual"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="actual_accommodation_others" name="actual"></td>
                            </tr>
                            <tr>
                                <td><b>Per Diem</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;Accommodation</td>
                                <td class="text-center"><input type="checkbox" value="general" id="perdiem_accomodation_general" name="perdiem_accomodation"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="perdiem_accomodation_project" name="perdiem_accomodation"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="perdiem_accomodation_others" name="perdiem_accomodation"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;Meals/Food</td>
                                <td class="text-center"><input type="checkbox" value="general" id="perdiem_meals_general" name="perdiem_meals"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="perdiem_meals_project" name="perdiem_meals"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="perdiem_meals_others" name="perdiem_meals"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;Incidental Expenses</td>
                                <td class="text-center"><input type="checkbox" value="general" id="perdiem_incidental_general" name="perdiem_incidental"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="perdiem_incidental_project" name="perdiem_incidental"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="perdiem_incidental_others" name="perdiem_incidental"></td>
                            </tr>
                            <tr>
                                <td><b>Transportation</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;Official Vehicle</td>
                                <td class="text-center"><input type="checkbox" value="general" id="transport_official_general" name="transport_official"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="transport_official_project" name="transport_official"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="transport_official_others" name="transport_official"></td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;&nbsp;Public Conveyance <br>&nbsp;&nbsp;&nbsp;&nbsp;<small><i>(Airplane, Bus, Taxi)</i></small></td>
                                <td class="text-center"><input type="checkbox" value="general" id="transport_public_general" name="transport_public"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="transport_public_project" name="transport_public"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="transport_public_others" name="transport_public"></td>
                            </tr>
                            <tr>
                                <td><b>Others</b></td>
                                <td class="text-center"><input type="checkbox" value="general" id="others_general" name="others_details"></td>
                                <td class="text-center"><input type="checkbox" value="project" id="others_project" name="others_details"></td>
                                <td class="text-center"><input type="checkbox" value="others" id="others_others" name="others_details"></td>
                            </tr>
                            <tr>
                                <th></th>
                                <th><input disabled type="text" class="form-control" id="others_remarks_general" name="others_details_remarks"></th>
                                <th><input disabled type="text" class="form-control" id="others_remarks_project" name="others_details_remarks"></th>
                                <th><input disabled type="text" class="form-control" id="others_remarks_others" name="others_details_remarks"></th>
                            </tr>
                        </tbody>
                    </table>



                    </div>
                </div>

                <div class="row" id="attachments" <?=$hrmodule?'hidden':''?>>
                    <br>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <a class='btn blue-madison' href='javascript:;'>
                                <i class="fa fa-upload"></i> Attach File
                                <input type="file" name ="userfile[]" id= "userfile" multiple 
                                    style='left: 16px !important;width: 108px;height: 34px;position:absolute;z-index:2;top:0;left:0;filter: alpha(opacity=0);-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";opacity:0;background-color:transparent;color:transparent;'
                                    name="file_source" size="40">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-8">
                        <div id="upload-file-info">
                            <?php
                                if(isset($arrto)): if($arrto['file_location']!=''):
                                    foreach(json_decode($arrto['file_location'], true) as $attach):
                                        $ext = explode('.',$attach['filename']);
                                        $ext = $ext[count($ext)-1];
                                        echo '<span><i></i>
                                                    <a href="'.base_url($attach['filepath']).'" target="_blank"><i class="'.check_icon($ext).'"></i> '.$attach['filename'].'</a>';
                                        if(!$hrmodule):
                                            echo '<a href="javascript:;" id="btn-attach" data-id="'.$attach['fileid'].'" class="font-red"><i class="fa fa-remove"></i></a>
                                                    </span>';
                                        endif;
                                        echo '<br>';
                                    endforeach;
                                endif; endif;
                             ?>
                        </div>
                        <span id="upload-size" class="small bold"></span><br>
                        <span id="upload-error" class="font-red small">Maximum upload must be 100MB.</span>
                    </div>
                </div>
                <div class="row"><div class="col-sm-8"><hr></div></div>
                <div class="row">
                    <div class="col-sm-8">
                        <?php if(!$hrmodule): ?>
                            <button type="submit" class="btn btn-success" id="btn-request-to">
                                <i class="icon-check"></i>
                                <?=$this->uri->segment(3) == 'edit' ? 'Save' : 'Submit'?></button>
                            <a href="<?=base_url('employee/travel_order')?>" class="btn blue"> <i class="icon-ban"></i> Cancel</a>
                        <?php else: ?>
                            <a href="<?=base_url('hr/request?request=to')?>" class="btn blue"> <i class="icon-ban"></i> Cancel</a>
                        <?php endif; ?>
                        <!-- <button type="button" id="printreport" value="reportOB" class="btn grey-cascade pull-right"><i class="icon-magnifier"></i> Print/Preview</button> -->
                    </div>
                </div>
                <?=form_close()?>
        </div>
    </div>
</div>

<!-- begin to form modal -->
<div id="to-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title bold">Travel Order</h4>
            </div>
            <div class="modal-body">
                <div class="row form-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <embed src="" id="to-embed" frameborder="0" width="100%" height="500px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="" id="to-embed-fullview" class="btn blue btn-sm" target="_blank"> <i class="glyphicon glyphicon-resize-full"> </i> Open in New Tab</a>
                <button type="button" class="btn dark btn-sm" data-dismiss="modal"> <i class="icon-ban"> </i> Close</button>
            </div>
        </div>
    </div>
</div>
<!-- end to form modal -->

<!-- begin delete attachment -->
<div id="delete-attachment" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Attachment</h4>
            </div>
            <?php $reqid = isset($_GET['req_id'])?$_GET['req_id']:''; ?>
            <?=form_open('employee/travel_order/delete?req_id='.$reqid, array('id' => 'frmob_attach'))?>
                <div class="modal-body">
                    <div class="row form-body">
                        <div class="col-md-12">
                            <input type="hidden" name="txtto_attach_id" id="txtto_attach_id">
                            <div class="form-group">
                                <label>Are you sure you want to delete this data?</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnsubmit-adj-delete" class="btn btn-sm green"><i class="icon-check"> </i> Yes</button>
                    <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"><i class="icon-ban"> </i> Cancel</button>
                </div>
            <?=form_close()?>
        </div>
    </div>
</div>
<!-- end delete attachment -->

<?=load_plugin('js',array('form_validation','datepicker'));?>

<script>
$(document).ready(function() {
    $('#upload-error').hide();
    $('.date-picker').datepicker();
    $('.date-picker').on('changeDate', function(){
        $(this).datepicker('hide');
    });

    $('a#btn-attach').on('click',function() {
        var id = $(this).data('id');
        $('#txtto_attach_id').val(id);
        $('#delete-attachment').modal('show');
    });

    $('#strDestination').on('keyup keypress change',function() {
        check_null('#strDestination','Destination must not be empty.');
    });

    $('#dtmTOdatefrom').on('keyup keypress change',function() {
        check_null('#dtmTOdatefrom','Date From must not be empty.');
    });

    $('#dtmTOdateto').on('keyup keypress change',function() {
        check_null('#dtmTOdateto','Date To must not be empty.');
    });

    $('#strPurpose').on('keyup keypress change',function() {
        check_null('#strPurpose','Purpose must not be empty.');
    });

    $('#userfile').on('keyup keypress change',function() {
        $('#upload-error').hide();
        $('#upload-file-info').html('');

        var fnames = '<ul>';
        var total_size = 0;
        for (var i = 0; i < $(this).get(0).files.length; ++i) {
            fnames = fnames + '<li>' + $(this).get(0).files[i].name + '</li>';
            total_size = total_size + $(this).get(0).files[i].size;
        }

        if(total_size < 1000000){
            $('#txtfilesize').val(Math.floor(total_size/1000));
            $('#txtdgstorage').val('KB');
            $('#upload-size').html('Total Filesize: '+Math.floor(total_size/1000)+' KB');
        }else{
            $('#txtfilesize').val(Math.floor(total_size/1000000));
            $('#txtdgstorage').val('MB');
            $('#upload-size').html('Total Filesize: '+Math.floor(total_size/1000000)+' MB');
        }
        $('#upload-file-info').html(fnames+'</ul>');

        if($('#txtdgstorage').val() == 'MB' || $('#txtdgstorage').val() == 'KB') {
            if($('#txtdgstorage').val() == 'MB') {
                if($('#txtfilesize').val() > 100){
                    $('#upload-error').show();
                }
            }
        }else{
            $('#upload-error').show();
        }

    });

    $('#btn-request-to').click(function(e) {
        var total_error = 0;

        total_error = total_error + check_null('#strDestination','Destination must not be empty.');
        total_error = total_error + check_null('#dtmTOdatefrom','Date From must not be empty.');
        total_error = total_error + check_null('#dtmTOdateto','Date To must not be empty.');
        total_error = total_error + check_null('#strPurpose','Purpose must not be empty.');
        if($('#txtdgstorage').val()!='' && $('#txtdgstorage').val()!=''){
            if($('#txtdgstorage').val() == 'MB' || $('#txtdgstorage').val() == 'KB') {
                if($('#txtdgstorage').val() == 'MB') {
                    if($('#txtfilesize').val() > 100){
                        total_error = total_error + 1;
                        $('#upload-error').show();
                    }
                }
            }else{
                total_error = total_error + 1;
                $('#upload-error').show();
            }
        }

        if(total_error > 0){
            e.preventDefault();
        }
    });

    $('#printreport').click(function() {
        var desti       = $('#strDestination').val();
        var todatefrom  = $('#dtmTOdatefrom').val();
        var todateto    = $('#dtmTOdateto').val();
        var purpose     = $('#strPurpose').val();

        var link = "<?=base_url('employee/reports/generate/?rpt=reportTO')?>"+"&desti="+desti+"&todatefrom="+todatefrom+"&todateto="+todateto+"&purpose="+purpose+"&meal="+meal;
        $('#to-embed').attr('src',link);
        $('#to-embed-fullview').attr('href',link);
        $('#to-form').modal('show');
        
    });

  

    $('#selemps').multiSelect({
        selectableOptgroup: true
    });



    // TOGGLE INPUTS WHEN CLICKED FUNDING SOURCE
    $('input[type=radio][name=funding_source]').change(function() {
        
        let selectedColumn = $(this).closest('th').index();

        
        // Clear and disable all text inputs except for the last row
        $('thead tr:nth-child(3) th input').val('').prop('disabled', true);
        $('tbody input[type=checkbox]').prop('checked', false).prop('disabled', true);

        $("tbody span").removeClass("checked");
        $("tbody input[type=text]").val('');
        $("tbody input[type=text]").prop('disabled',true);


        
        
        // Enable only the selected column's text input and checkboxes in that column
        $('thead tr:nth-child(3) th:eq(' + selectedColumn + ') input').prop('disabled', false);
        $('tbody tr td:nth-child(' + (selectedColumn + 1) + ') input[type=checkbox]').prop('disabled', false);

    });

    $('input[type=checkbox][name=others_details]').change(function() {
        
        let selectedColumn = $(this).closest('td').index();
        let textInput = $('tbody tr:last-child th:eq(' + selectedColumn + ') input');

     
        
        if ($(this).is(':checked')) {
            textInput.prop('disabled', false);
        } else {
            textInput.val('').prop('disabled', true);
        }
    });


});
</script>


<?=load_plugin('js',array('datetimepicker','timepicker','datepicker','select2','multi-select'));?>