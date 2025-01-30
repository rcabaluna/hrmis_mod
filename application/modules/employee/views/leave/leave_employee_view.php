<style type="text/css">
    th { text-align: center; }
    tr td { vertical-align: middle !important; }
</style>
<?=load_plugin('css',array('select','datatables'));?>
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?=base_url('home')?>">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Employee Module</span>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Attendance</span>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Leave Balance</span>
        </li>
    </ul>
</div>
<!-- END PAGE BAR -->
<br>
<div class="clearfix"></div>
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption font-dark">
                            <i class="icon-user font-dark"></i>
                            <span class="caption-subject bold uppercase"> Leave Balance</span>
                        </div>
                    </div>
                    <div class="loading-image"><center><img src="<?=base_url('assets/images/spinner-blue.gif')?>"></center></div>
                    <div class="portlet-body" id="employee_view" style="display: none">
                        <div class="row">
                            <div class="col-md-12" style="margin-bottom: 20px;">
                                <?=form_open('', array('class' => 'form-inline', 'method' => 'get'))?>
                                    <div class="form-group" style="display: inline-flex;margin-left: 10px;">
                                        <label style="padding: 6px;">Year</label>
                                        <select class="bs-select form-control" name="yr">
                                            <?php foreach (getYear() as $yr): ?>
                                                <option value="<?=$yr?>"
                                                    <?php 
                                                        if(isset($_GET['yr'])):
                                                            echo $_GET['yr'] == $yr ? 'selected' : '';
                                                        else:
                                                            echo $yr == date('Y') ? 'selected' : '';
                                                        endif;
                                                     ?> >  
                                                <?=$yr?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>&nbsp;&nbsp;
                                    <button type="submit" class="btn btn-primary" style="margin-top: -3px;">Search</button>
                                <?=form_close()?>
                            </div>
                        </div>
                        <!-- Vacation Leave -->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption font-dark">
                                    <span class="caption-subject bold uppercase"> Vacation Leave</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 20px;">
				                        <table class="table table-striped table-bordered table-hover" id="tblvl">
				                            <thead>
				                                <tr>
				                                	<th width="30px;">No</th>
				                                	<th>Date</th>
				                                	<th>Earned</th>
				                                	<th>Abs. Und. w/ Pay</th>
				                                	<th>Current Balance</th>
				                                	<th>Previous Balance</th>
				                                	<th>Ans. Und. w/o Pay</th>
				                                </tr>
				                            </thead>
				                            <tbody>
				                            	<?php $no=1; foreach($leave_balance as $empleave): ?>
				                                <tr>
				                                	<td align="center"><?=$no++?></td>
				                                	<td nowrap align="center"><?=date('F d, Y', strtotime($empleave['processDate']))?></td>
				                                	<td align="center"><?=$empleave['vlEarned']?></td>
				                                	<td align="center"> <?php 
                                            if (!empty($empleave['vl_wpay']) && $empleave['vl_wpay'] > 0) {
                                                echo $empleave['vl_wpay'];
                                            }

                                            if (!empty($empleave['vlAbsUndWPay']) && $empleave['vlAbsUndWPay'] > 0) {
                                                echo $empleave['vlAbsUndWPay'];
                                            }

                                            if ($empleave['vlAbsUndWPay'] == 0 && $empleave['vl_wpay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?></td>
				                                	<td align="center"><?=$empleave['vlBalance']?></td>
				                                	<td align="center"><?=$empleave['vlPreBalance']?></td>
				                                	<td align="center"><?php 
                                            if (!empty($empleave['vl_wopay']) && $empleave['vl_wopay'] > 0) {
                                                echo $empleave['vl_wopay'];
                                            }

                                            if (!empty($empleave['vlAbsUndWoPay']) && $empleave['vlAbsUndWoPay'] > 0) {
                                                echo $empleave['vlAbsUndWoPay'];
                                            }

                                            if ($empleave['vlAbsUndWoPay'] == 0 && $empleave['vl_wopay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?></td>
				                                </tr>
				                            	<?php endforeach; ?>
				                            </tbody>
				                        </table>
				                    </div>
				                </div>
				            </div>
				        </div>

				        <!-- Sick Leave -->
                        <div class="portlet light bordered">
                            <div class="portlet-title">
                                <div class="caption font-dark">
                                    <span class="caption-subject bold uppercase"> Sick Leave</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 20px;">
				                        <table class="table table-striped table-bordered table-hover" id="tblsl">
				                            <thead>
				                                <tr>
				                                	<th width="30px;">No</th>
				                                	<th>Date</th>
				                                	<th>Earned</th>
				                                	<th>Abs. Und. w/ Pay</th>
				                                	<th>Current Balance</th>
				                                	<th>Previous Balance</th>
				                                	<th>Ans. Und. w/o Pay</th>
				                                </tr>
				                            </thead>
				                            <tbody>
				                                <?php $no=1; foreach($leave_balance as $empleave): ?>
				                                <tr>
				                                	<td align="center"><?=$no++?></td>
				                                	<td nowrap align="center"><?=date('F d, Y', strtotime($empleave['processDate']))?></td>
				                                	<td align="center"><?=$empleave['slEarned']?></td>
				                                	<td align="center"><?php 
                                            if (!empty($empleave['sl_wpay']) && $empleave['sl_wpay'] > 0) {
                                                echo $empleave['sl_wpay'];
                                            }

                                            if (!empty($empleave['slAbsUndWPay']) && $empleave['slAbsUndWPay'] > 0) {
                                                echo $empleave['slAbsUndWPay'];
                                            }

                                            if ($empleave['slAbsUndWPay'] == 0 && $empleave['sl_wpay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?></td>
				                                	<td align="center"><?=$empleave['slBalance']?></td>
				                                	<td align="center"><?=$empleave['slPreBalance']?></td>
				                                	<td align="center"><?php 
                                            if (!empty($empleave['sl_wopay']) && $empleave['sl_wopay'] > 0) {
                                                echo $empleave['sl_wopay'];
                                            }

                                            if (!empty($empleave['slAbsUndWoPay']) && $empleave['slAbsUndWoPay'] > 0) {
                                                echo $empleave['slAbsUndWoPay'];
                                            }

                                            if ($empleave['slAbsUndWoPay'] == 0 && $empleave['sl_wopay'] == 0) {
                                                echo '0.000';
                                            }
                                            ?></td>
				                                </tr>
				                            	<?php endforeach; ?>
				                            </tbody>
				                        </table>
				                    </div>
				                </div>
				            </div>
				        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php load_plugin('js',array('select','datatables'));?>
<script>
    $(document).ready(function() {
    	$('#tblsl,#tblvl').dataTable( {
    	    "initComplete": function(settings, json) {
    	        $('.loading-image').hide();
    	        $('#employee_view').show();
    	    }, "pageLength": 5} );
    });
</script>