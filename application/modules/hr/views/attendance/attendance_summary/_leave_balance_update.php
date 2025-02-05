<?php
    $month = isset($_GET['month']) ? $_GET['month'] == 'all' ? date('m') : $_GET['month'] : date('m');
    $yr = isset($_GET['yr']) ? $_GET['yr'] : date('Y');?>
<div class="tab-pane active" id="tab_1_3">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <span class="caption-subject bold uppercase"> Leave Balance</span>
                </div>
            </div>
            <div class="portlet-body">
                <?php if(count($arrLatestBalance) < 1): ?>
                    <a href="<?=base_url('hr/attendance_summary/leave_balance_set/'.$this->uri->segment(4)).'?month='.$month.'&yr='.$yr?>" class="btn blue"><i class="fa fa-pencil"></i> Set Leave Balance</a>
                    <br><br>
                <?php endif; ?>
                <div class="row">
                    <div class="tabbable-line tabbable-full-width col-md-12">
                        <table class="table table-striped table-bordered table-condensed  table-hover table-checkable order-column" id="tblleave-balance" data-title="Vacation Leave">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="text-align: center;vertical-align: middle;">Month</th>
                                    <th rowspan="2" style="text-align: center;vertical-align: middle;">Earned</th>
                                    <th colspan="3" style="text-align: center;">Vacation Leave</th>
                                    <th colspan="3" style="text-align: center;">Sick Leave</th>
                                    <th style="text-align: center;vertical-align: middle;">Force Leave</th>
                                    <th  style="text-align:center;vertical-align: middle;">Special Privilege <br> Leave</th>
                                    <th rowspan="2" style="text-align: center;vertical-align: middle;"> Action </th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">ABS/ UND/ <br> W/P</th>
                                    <th style="text-align: center;">BALANCE</th>
                                    <th style="text-align: center;">ABS/ UND/ <br> WOP</th>
                                    <th style="text-align: center;">ABS/ UND/ <br> W/P</th>
                                    <th style="text-align: center;">BAL</th>
                                    <th style="text-align: center;">ABS/ UND/ <br> WOP</th>
                                    <th style="text-align: center;">BAL</th>
                                    <th style="text-align: center;">BAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($arrLeaveBalance as $leave_bal): ?>
                                    <tr class="odd gradeX">
                                        <td align="center">
                                            <?=date('M', mktime(0, 0, 0, $leave_bal['periodMonth'], 10)).' '.$yr?></td>
                                        <td align="center">
                                            <?=count($leave_bal) > 0 ? $leave_bal['vlEarned'] : ''?></td>
                                        <td align="center">
                                            <?php 
                                            if (!empty($leave_bal['vl_wpay']) && $leave_bal['vl_wpay'] > 0) {
                                                echo $leave_bal['vl_wpay'];
                                            }

                                            if (!empty($leave_bal['vlAbsUndWPay']) && $leave_bal['vlAbsUndWPay'] > 0) {
                                                echo $leave_bal['vlAbsUndWPay'];
                                            }

                                            if ($leave_bal['vlAbsUndWPay'] == 0 && $leave_bal['vl_wpay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?>
                                        </td>
                                        <td align="center">
                                            <?=count($leave_bal) > 0 ? $leave_bal['vlBalance'] : ''?></td>
                                            <td align="center">
                                            <?php 
                                            if (!empty($leave_bal['vl_wopay']) && $leave_bal['vl_wopay'] > 0) {
                                                echo $leave_bal['vl_wopay'];
                                            }

                                            if (!empty($leave_bal['vlAbsUndWoPay']) && $leave_bal['vlAbsUndWoPay'] > 0) {
                                                echo $leave_bal['vlAbsUndWoPay'];
                                            }

                                            if ($leave_bal['vlAbsUndWoPay'] == 0 && $leave_bal['vl_wopay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?>
                                        </td>
                                        <td align="center">
                                            <?php 
                                            if (!empty($leave_bal['sl_wpay']) && $leave_bal['sl_wpay'] > 0) {
                                                echo $leave_bal['sl_wpay'];
                                            }

                                            if (!empty($leave_bal['slAbsUndWPay']) && $leave_bal['slAbsUndWPay'] > 0) {
                                                echo $leave_bal['slAbsUndWPay'];
                                            }

                                            if ($leave_bal['slAbsUndWPay'] == 0 && $leave_bal['sl_wpay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?>
                                        </td>
                                        <td align="center">
                                            <?=count($leave_bal) > 0 ? $leave_bal['slBalance'] : ''?></td>
                                        <td align="center">
                                            <?php 
                                            if (!empty($leave_bal['sl_wopay']) && $leave_bal['sl_wopay'] > 0) {
                                                echo $leave_bal['sl_wopay'];
                                            }

                                            if (!empty($leave_bal['slAbsUndWoPay']) && $leave_bal['slAbsUndWoPay'] > 0) {
                                                echo $leave_bal['slAbsUndWoPay'];
                                            }

                                            if ($leave_bal['slAbsUndWoPay'] == 0 && $leave_bal['sl_wopay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?>
                                        </td>
                   
                                        <td class="text-center"><?php
                                            if ($leave_bal['flBalance'] > 0) {
                                                echo $leave_bal['flBalance'];
                                            }else{
                                                echo '0.000';
                                            }
                                        ?>
                                        </td>
                          
                  
                                        <td class="text-center">
                                        <?php
                                            if ($leave_bal['plBalance'] > 0) {
                                                echo $leave_bal['plBalance'];
                                            }else{
                                                echo '0.000';
                                            }
                                        ?>
                                        </td>
                          
                                        <td align="center" style="width: 16%;" nowrap>
                                            <button class="btn btn-sm blue" id="btn-leavebal" data-json='<?=json_encode($leave_bal,0)?>'
                                                data-action="view" data-leave_earned="<?=$_ENV['leave_earned']?>">
                                                <i class="fa fa-eye"></i> View</button>
                                            <?php if($arrLatestBalance['lb']['periodMonth'] == $leave_bal['periodMonth']): ?>
                                                <button class="btn btn-sm green" id="btn-leavebal-override" data-json='<?=json_encode($leave_bal,0)?>'
                                                    data-action="override" data-leave_earned="<?=$_ENV['leave_earned']?>">
                                                    <i class="fa fa-edit"></i> Override</button>
                                            <?php else: ?>
                                                <button class="btn btn-sm green" disabled><i class="fa fa-edit"></i> Override</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <span class="caption-subject bold uppercase"> Leave Balance for the Month of <?=date('F', mktime(0, 0, 0, count($arrLatestBalance) > 0 ? $arrLatestBalance['lb']['periodMonth']+1 : date('m'), 10)).' '.count($arrLatestBalance) > 0 ? $arrLatestBalance['lb']['periodYear'] : date('Y')?></span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="tabbable-line tabbable-full-width col-md-12">
                        <small>"If the employee reach the compulsory retirement age of 65 but the service has been extended, the employee will NO LONGER EARN leave credits."</small><br>
                        <small class="bold" style="color: red;">WARNING:  You are about to update the LEAVE BALANCE for the month of <?=date('F', mktime(0, 0, 0, count($arrLatestBalance) > 0 ? $arrLatestBalance['lb']['periodMonth']+1 : date('m'), 10)).' '.count($arrLatestBalance) > 0 ? $arrLatestBalance['lb']['periodYear'] : date('Y')?>. Please check that all Leaves, OBs, Flag Ceremonies, Time-in and Time-out has been overriden correctly. Blank attendance records shall be considered Vacation Leaves.</small><br>
                        <br>
                        <p style="text-align: center;">
                            <button class="btn red <?=(count($arrLatestBalance) < 1) ? 'disabled' : '' ?>" id="btn-update-leavebal"
                                    data-latest_lb='<?=json_encode($arrLatestBalance,0)?>'
                                    data-att_summ='<?=json_encode($arrAttendance_summary,0)?>'
                                    data-leave_earned="<?=$_ENV['leave_earned']?>">
                                <i class="fa fa-pencil"></i> &nbsp;Update Leave Balance</button>
                            <button class="btn blue" data-toggle="modal" data-backdrop="static" data-keyboard="false" href="#modal-rollback" id="btn-rollback">
                                <i class="fa fa-refresh"></i> &nbsp;Rollback</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('modals/_leave_balance_update_modal'); ?>
<?php load_plugin('js', array('form_validation')) ?>
<script src="<?=base_url('assets/js/custom/leave_balance.js')?>"></script>

