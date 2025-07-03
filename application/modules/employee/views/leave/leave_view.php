<?php 
/** 
Purpose of file:    Leave View
Author:             Rose Anne L. Grefaldeo
System Name:        Human Resource Management Information System Version 10
Copyright Notice:   Copyright(C)2018 by the DOST Central Office - Information Technology Division
**/



$hrmodule = isset($_GET['module']) ? $_GET['module'] == 'hr' ? 1 : 0 : 0;
?>
<?=load_plugin('css', array('datepicker','timepicker','select','select2'))?>
<!-- BEGIN PAGE BAR -->
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
            <span><?=ucwords($hrmodule ? 'view' : $action)?> Leave</span>
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
                    <span class="caption-subject bold uppercase">Leave</span>
                </div>
            </div>
            <div class="portlet-body">
                
                
            <?php
                if (!$arrBalance) {
                    echo "No set leave balance. Please contact HR Unit.";
                }else{
                $permonth = date("F", strtotime($arrBalance['periodMonth'].' '.$arrBalance['periodYear']));
                $peryear = date("Y", strtotime($arrBalance['periodYear']));
                $vlBalance = count($arrBalance) > 0 ? $arrBalance['vlBalance'] : 0;
                $slBalance = count($arrBalance) > 0 ? $arrBalance['slBalance'] : 0;
                $plBalance = count($arrBalance) > 0 ? $arrBalance['plBalance'] : 0;
                $flBalance = count($arrBalance) > 0 ? $arrBalance['flBalance'] : 0;
                $mtlBalance = count($arrBalance) > 0 ? $arrBalance['mtlBalance'] : 0;

                $strLeavetype = '';
                $emp_gender = employee_details($_SESSION['sessEmpNo']);
                $emp_gender = count($emp_gender) > 0 ? $emp_gender[0]['sex'] : '';
                $leave_details = isset($arrleave) ? explode(';',$arrleave['requestDetails']) : array();

                $form = $action == 'add' ? 'employee/leave/add_leave' : 'employee/leave/edit?req_id='.$arrleave['requestID'];

            ?>
            <?=form_open_multipart($form, array('method' => 'post', 'id' => 'frmLeave'));?>
                <input class="hidden" name="txtempno" id="txtempno" value="<?=$_SESSION['sessEmpNo']?>">
                <input class="hidden" name="txttype" id="txttype">
                <input class="hidden" name="intVL" id="intVL" value="<?=!empty($arrBalance[0]['vlBalance'])?$arrBalance[0]['vlBalance']:''?>">
                <input class="hidden" name="intSL" id="intSL" value="<?=!empty($arrBalance[0]['slBalance'])?$arrBalance[0]['slBalance']:''?>">
                <input type="hidden" id="txtfilesize" name="txtfilesize">
                <input type="hidden" id="txtdgstorage" name="txtdgstorage">
                <div class="row">
                    <div class="col-sm-9">
                        <table class="table table-bordered">
                            <tr>
                                <th>LEAVE BALANCE AS OF</th>
                                <td colspan="5"><?=strtoupper($permonth)?>, <?=strtoupper($peryear)?></td>
                            </tr>
                            <tr>
                                
                                <th width="18%">Sick Leave left</th>
                                <td colspan="2" width="11%"><?=$slBalance==""?0:number_format($arrBalance['slBalance'],3)?></td>
                                <th width="18%">Vacation Leave left</th>
                                <td width="12%"><?=$vlBalance==""?0:number_format($arrBalance['vlBalance'],3)?></td>
                            </tr>
                            <tr>
                                <th width="20%">Forced Leave left</th>
                                <td colspan="2" width="11%"><?=$flBalance==""?0:number_format($arrBalance['flBalance'],3)?></td>
                                <th>Special Leave left</th>
                                <td><?=$plBalance==""?0:number_format($arrBalance['plBalance'],3)?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                           <label class="control-label"><strong>Leave Type : </strong><span class="required"> * </span></label>
                            <select name="strLeavetype" id="strLeavetype" type="text" class="form-control bs-select form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="">-- SELECT LEAVE TYPE --</option>
                                <?php foreach ($arrLeaveTypes as $leavetypesRow) { ?>
                                    <option value="<?=$leavetypesRow['leaveCode']?>"><?=$leavetypesRow['leaveType']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="wholeday_textbox" <?=$action=='add'?'hidden':''?>>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <?php 
                                $strday = 'whole day';
                                if(count($leave_details) > 9):
                                    $strday = strtolower($leave_details[9]) == 'half day' ? 'half day' : 'whole day';
                                endif;
                            ?>
                            <div class="radio-list">
                                <label class="radio-inline">
                                    <input type="radio" name="strDay" id="strDayw" value="Whole day" <?=$hrmodule ? 'disabled' : ''?>
                                        <?=$strday=='whole day' ? 'checked' : ''?>> Whole day</label>
                                <label class="radio-inline">
                                    <input type="radio" name="strDay" id="strDayf" value="Half day" <?=$hrmodule ? 'disabled' : ''?>
                                        <?=$strday=='half day' ? 'checked' : ''?>> Half day </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="leavefrom_textbox" <?=$action=='add'?'hidden':''?>>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">Leave From :  <span class="required"> * </span></label>
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control date-picker" name="dtmLeavefrom" id="dtmLeavefrom" data-date-format="yyyy-mm-dd" <?=$hrmodule ? 'disabled' : ''?>
                                    autocomplete="off" value="<?=count($leave_details) > 0 ? $leave_details[1] : ''?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">Leave To :  <span class="required"> * </span></label>
                            <div class="input-icon right">
                                <i class="fa"></i>
                                <input type="text" class="form-control date-picker" name="dtmLeaveto" id="dtmLeaveto" data-date-format="yyyy-mm-dd" <?=$hrmodule ? 'disabled' : ''?>
                                autocomplete="off" value="<?=count($leave_details) > 0 ? $leave_details[2] : ''?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">No. of Days Applied :  <span class="required"> * </span></label>
                            <div class="input-icon right">
                                <i class="fa"></i>
                               <input type="text" class="form-control" id="intDaysApplied" value="<?=count($leave_details) > 0 ? $leave_details[3] : ''?>">
                               <input type="hidden" name="intDaysApplied" id="intDaysApplied_val">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="incaseVL_textbox" <?=$action=='add'?'hidden':(count($leave_details) > 0 ? $leave_details[0] == 'vl' ? '' : 'hidden' : '')?>>
                    <?php 
                        $vl_case = '';
                        if(count($leave_details) > 0):
                            $vl_case = $leave_details[8] == '' ? '' : ($leave_details[8] == 'within the country' ? 'local' : 'abroad');
                        endif;
                     ?>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label class="control-label">In Case of Vacation Leave : </label>
                            <select name="strIncaseVL" id="strIncaseVL" type="text" class="form-control bs-select form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="">-- SELECT --</option>
                                <option value="within the country" <?=$vl_case=='local' ? 'selected' : ''?>>Within the country</option>
                                <option value="abroad" <?=$vl_case=='abroad' ? 'selected' : ''?>>Abroad</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="incaseSL_textbox" <?=$action=='add'?'hidden':(count($leave_details) > 0 ? $leave_details[0] == 'sl' ? '' : 'hidden' : '')?>>
                    <?php 
                        $sl_case = '';
                        if(count($leave_details) > 0):
                            $sl_case = $leave_details[7] == '' ? '' : ($leave_details[7] == 'in patient' ? 'in' : 'out');
                        endif;
                     ?>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label class="control-label">In Case of Sick Leave : </label>
                            <select name="strIncaseSL" id="strIncaseSL" type="text" class="form-control bs-select form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="">-- SELECT --</option>
                                <option value="in patient" <?=$sl_case=='in' ? 'selected' : ''?>>IN Patient</option>
                                <option value="out patient" <?=$sl_case=='out' ? 'selected' : ''?>>OUT Patient</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="incaseSTL_textbox" <?=$action=='add'?'hidden':(count($leave_details) > 0 ? $leave_details[0] == 'stl' ? '' : 'hidden' : '')?>>
                    <?php 
                        $stl_case = '';
                        if(count($leave_details) > 0):
                            $stl_case = $leave_details[8] == '' ? '' : ($leave_details[8] == "completion of master's degree" ? 'masters' : 'board');
                        endif;
                     ?>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label class="control-label">In Case of Study Leave : </label>
                            <select name="strIncaseSTL" id="strIncaseSTL" type="text" class="form-control bs-select form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="">-- SELECT --</option>
                                <option value="completion of master's degree" <?=$stl_case=='masters' ? 'selected' : ''?>>Completion of Master's Degree</option>
                                <option value="bar/board examination review" <?=$stl_case=='board' ? 'selected' : ''?>>BAR/Board Examination Review</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="reason_textbox" <?=$action=='add'?'hidden':''?>>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label class="control-label">Specify Remarks / Reasons:</label>
                            <textarea name="strReason" id="strReason" type="text" class="form-control" <?=$hrmodule ? 'disabled' : ''?>><?=count($leave_details) > 0 ? $leave_details[6] : ''?></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="commutation_box" <?=$action=='add'?'hidden':''?>>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <hr>
                            <label class="control-label">Commutation :</label>
                            <select name="strCommutation" id="strCommutation" type="text" class="form-control form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="">-- SELECT COMMUTATION REQUEST--</option>
                                <option value="not-requested">Not Requested</option>
                                <option value="requested">Requested</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- <div class="row" id="signatory1_textbox" <?=$action=='add'?'hidden':''?>>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <hr>
                            <label class="control-label">Authorized Official (1st Signatory) :</label>
                            <select name="str1stSignatory" id="str1stSignatory" type="text" class="form-control select2 form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="0">-- SELECT SIGNATORY--</option>
                                <?php foreach($arrEmployees as $i=>$data):
                                        $selected = count($leave_details) > 0 ? $data['empNumber'] == $leave_details[4] ? 'selected' : '' : ''?>
                                    <option value="<?=$data['empNumber']?>" <?=$selected?>><?=(strtoupper($data['surname']).', '.($data['firstname']).' '.($data['middleInitial']).' '.($data['nameExtension']))?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row" id="signatory2_textbox" <?=$action=='add'?'hidden':''?>>
                    <div class="col-sm-9">
                        <div class="form-group">
                            <label class="control-label">Authorized Official (2nd Signatory) :</label>
                            <select name="str2ndSignatory" id="str2ndSignatory" type="text" class="form-control select2 form-required" <?=$hrmodule ? 'disabled' : ''?>>
                                <option value="0">-- SELECT SIGNATORY--</option>
                                <?php foreach($arrEmployees as $i=>$data):
                                    $selected = count($leave_details) > 0 ? $data['empNumber'] == $leave_details[5] ? 'selected' : '' : ''?>
                                    <option value="<?=$data['empNumber']?>" <?=$selected?>><?=(strtoupper($data['surname']).', '.($data['firstname']).' '.($data['middleInitial']).' '.($data['nameExtension']))?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div> -->

                <div class="row" id="attachments" <?=$action=='add' || $hrmodule?'hidden':''?>>
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
                    <div class="col-sm-9">
                        <div id="upload-file-info">
                            <?php
                                if(isset($arrleave)): if($arrleave['file_location']!=''):
                                    foreach(json_decode($arrleave['file_location'], true) as $attach):
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

                <div class="row div-actions"><div class="col-sm-9"><hr></div></div>
                <div class="row div-actions">
                    <div class="col-sm-9">
                        <?php if(!$hrmodule): ?>
                            <button type="submit" class="btn btn-success" id="btn-request-leave">
                                <i class="icon-check"></i>
                                <?=$this->uri->segment(3) == 'edit' ? 'Save' : 'Submit'?></button>
                            <a href="<?=base_url('employee/leave')?>" class="btn blue"> <i class="icon-ban"></i> Cancel</a>
                        <?php else: ?>
                            <a href="<?=base_url('hr/request?request=leave')?>" class="btn blue"> <i class="icon-ban"></i> Cancel</a>
                        <?php endif; ?>
                        <!-- <button type="button" id="printreport" value="reportOB" class="btn grey-cascade pull-right"><i class="icon-magnifier"></i> Print/Preview</button> -->
                    </div>
                </div>
                <?=form_close()?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!-- begin leave form modal -->
<div id="leave-form" class="modal fade" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title bold">Leave Form</h4>
            </div>
            <div class="modal-body">
                <div class="row form-body">
                    <div class="col-md-12">
                        <div class="form-group">
                            <embed src="" id="leave-embed" frameborder="0" width="100%" height="500px">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="" id="leave-embed-fullview" class="btn blue btn-sm" target="_blank"> <i class="glyphicon glyphicon-resize-full"> </i> Open in New Tab</a>
                <button type="button" class="btn dark btn-sm" data-dismiss="modal"> <i class="icon-ban"> </i> Close</button>
            </div>
        </div>
    </div>
</div>
<!-- end leave form modal -->

<!-- begin delete attachment -->
<div id="delete-attachment" class="modal fade" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Attachment</h4>
            </div>
            <?php $reqid = isset($_GET['req_id'])?$_GET['req_id']:''; ?>
            <?=form_open('employee/leave/delete?req_id='.$reqid, array('id' => 'frmob_attach'))?>
                <div class="modal-body">
                    <div class="row form-body">
                        <div class="col-md-12">
                            <input type="hidden" name="txtleave_attach_id" id="txtleave_attach_id">
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

<script type="text/javascript" src="<?=base_url('assets/js/leave.js')?>"></script>
<?=load_plugin('js',array('form_validation','datepicker','select','select2'));?>


<script>
    $(document).ready(function() {
        $('#upload-error').hide();
        
        $('a#btn-attach').on('click',function() {
            var id = $(this).data('id');
            $('#txtleave_attach_id').val(id);
            $('#delete-attachment').modal('show');
        });

        if("<?=$action?>" == "add"){
            $('.div-actions').hide();    
        }else{
            $('.div-actions').show();
        }

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


        // $("#strLeavetype").change(function (e) { 

        //     $("#strIncaseVL").val("");
        //     $("#strIncaseSL").val("");
        //     $("#strIncaseSTL").val("");

        //     $(".btn.dropdown-toggle[data-id='strIncaseVL'] .filter-option").text("-- SELECT --");
        //     $(".btn.dropdown-toggle[data-id='strIncaseSL'] .filter-option").text("-- SELECT --");
        //     $(".btn.dropdown-toggle[data-id='strIncaseSTL'] .filter-option").text("-- SELECT --");

        // });

	$("#frmLeave").submit(function (e) {
	    $("#btn-request-leave").attr("disabled", true);

	    var total_error = 0;

	    const leaveType = $("#strLeavetype").val().toLowerCase();
	    const leaveFrom = $("#dtmLeavefrom").val();
	    const leaveTo = $("#dtmLeaveto").val();
	    const today = new Date();
	    const parsedLeaveFrom = new Date(leaveFrom);
	    const parsedLeaveTo = new Date(leaveTo);

	    // Check required fields
	    total_error += check_null('#dtmLeavefrom','Leave from must not be empty.');
	    total_error += check_null('#dtmLeaveto','Leave to must not be empty.');
	    total_error += check_null('#strCommutation',' This field must not be empty.');

	    switch (leaveType) {
	        case 'vl':
	            total_error += check_null('#strIncaseVL','This field must not be empty.');
	            total_error += check_null('#strReason','This field must not be empty.');
	            break;
		case 'fl':
		    total_error += check_null('#strReason','This field must not be empty.');
                    break;
	        case 'sl':
	            total_error += check_null('#strIncaseSL','This field must not be empty.');
	            total_error += check_null('#strReason','This field must not be empty.');
	            break;
	        case 'stl':
	            total_error += check_null('#strIncaseSTL','This field must not be empty.');
	            total_error += check_null('#strReason','This field must not be empty.');
	            break;
	        case 'sppl':
	            total_error += check_null('#strReason','This field must not be empty.');
	            break;
	    }

	    // Validation: Restrict non-SL if filed within 5 days
	    if (leaveType !== 'sl') {
	        const timeDiff = parsedLeaveFrom.getTime() - today.getTime();
	        const diffDays = Math.floor(timeDiff / (1000 * 60 * 60 * 24));

	        if (diffDays < 5) {
	            alert("Leave must be filed at least 5 days in advance.");
	            $("#btn-request-leave").attr("disabled", false);
	            e.preventDefault();
	            return;
	        }
	    }

	    // Validation: SL must be filed only up to return-to-office day
	    if (leaveType === 'sl') {
	        let returnDate = new Date(parsedLeaveTo);
	        returnDate.setDate(returnDate.getDate() + 1);

	        if (today > returnDate) {
	            alert("Sick Leave can only be filed on or before the return-to-office date.");
	            $("#btn-request-leave").attr("disabled", false);
	            e.preventDefault();
	            return;
	        }
	    }

	    // File size limit check
	    if ($('#txtdgstorage').val() && $('#txtfilesize').val()) {
	        if ($('#txtdgstorage').val() === 'MB' && parseInt($('#txtfilesize').val()) > 100) {
	            $('#upload-error').show();
	            total_error += 1;
	        }
	    }

	    if (total_error > 0) {
	        e.preventDefault();
	        $("#btn-request-leave").attr("disabled", false);
	    }
	});

        $('#printreport').click(function(){
            var leavetype=$('#strLeavetype').val();
            var day=$('#strDay').val();
            var leavefrom=$('#dtmLeavefrom').val();
            var leaveto=$('#dtmLeaveto').val();
            var daysapplied=$('#intDaysApplied').val();
            var signatory=$('#str1stSignatory').val();
            var signatory2=$('#str2ndSignatory').val();
            var reason=$('#strReason').val();
            var incaseSL=$('#strIncaseSL').val();
            var incaseVL=$('#strIncaseVL').val();
            var intVL=$('#intVL').val();
            var intSL=$('#intSL').val();

            var link = "<?=base_url('employee/reports/generate/?rpt=reportLeave')?>"+"&leavetype="+leavetype+"&day="+day+"&leavefrom="+leavefrom+"&leaveto="+leaveto+"&daysapplied="+daysapplied+"&signatory="+signatory+"&signatory2="+signatory2+"&reason="+reason+"&incaseSL="+incaseSL+"&incaseVL="+incaseVL+"&intVL="+intVL+"&intSL="+intSL;

            $('#leave-embed').attr('src',link);
            $('#leave-embed-fullview').attr('href',link);
            $('#leave-form').modal('show');
        });
    });
</script>
