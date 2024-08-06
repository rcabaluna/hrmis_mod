<!-- begin modal update/add other info -->
<form id="frm-biometrics-dl">
    <div class="modal fade in" id="mdl_download_biometrics_data" tabindex="-1" role="full" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h5 class="modal-title uppercase"><b>Select Date Range</b></h5>
                </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Date From <span class="text-danger"><small>*</small></span></label>
                                <input type="date" name="datefrom" id="dtedatefrom" class="form-control" value="<?=date('Y-m-d')?>" required />
                            </div>
                            <div class="col-md-6">
                                <label>Date To <span class="text-danger"><small>*</small></span></label>
                                <input type="date" name="dateto" id="dtedateto" class="form-control" value="<?=date('Y-m-d')?>" max="<?=date('Y-m-d')?>" required />
                            </div>
                            <div class="col-md-12" style="margin-top: 1rem;">
                                <label>Appointment Code <span class="text-danger"><small>*</small></span></label>
                                <select name="appointmentcode" id="selappointmentcode" class="form-control" required>
                                    <option value="-">All</option>
                                    <?php foreach($apptCodes as $apptCodesRow): ?>
                                    <option value="<?=$apptCodesRow['appointmentCode']?>"><?=$apptCodesRow['appointmentDesc']?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div style="margin-top: 1.5rem;" class="col-md-12">
                                <label for="">File Upload <i>(for PSTO DTR .dat file)</i></label>
                                <input type="file" name="attlog_file" id="flattlog_file" class="form-control" accept=".dat">
                            </div>
                        </div>
                        <div class="m-t-25 text-center" id="dtr-info-message">
                            <span></span>
                        </div>
                        <div class="xloading-image">
                            <center><img src="<?=base_url('/assets/images/spinner-blue.gif')?>" style="max-height: 4rem;" /></center>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:location.reload();" class="btn green" id="btn-refresh-biometrics-dl">Refresh</a>
                        <button type="submit" class="btn green" id="btn-submit-biometrics-dl">Download</button>
                    </div>
            </div>
        </div>
    </div>
</form>



<script>
$(document).ready(function () {
    $("#btn-refresh-biometrics-dl").css("display", "none");
    $(".xloading-image").hide();
});

$("#frm-biometrics-dl").submit(function (e) {
    download_biometrics_data();
    e.preventDefault();
});

function download_biometrics_data(currentDate) {
    $(".xloading-image").show();
    $("#dtr-info-message").html('Downloading attendance data. Please wait...');
    $("#btn-submit-biometrics-dl").attr('disabled', 'disabled');

    var formData = new FormData($("#frm-biometrics-dl")[0]);

    $.ajax({
        url: "<?=base_url('hr/attendance/generate_biometrics_data')?>",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(data) {
        console.log(data.trim());
            if ('SUCCESS' == data.replace(/^\s+|\s+$/gm, '')) {
                $(".xloading-image").hide();
                $("#btn-refresh-biometrics-dl").css("display", "inline-block");
                $("#btn-submit-biometrics-dl").css("display", "none");
                $("#dtr-info-message").html('Download complete!');
            } else {
                $("#dtr-info-message").html("An error has occured! Please try again.");
                $("#btn-refresh-biometrics-dl").css("display", "inline-block");
                $("#btn-submit-biometrics-dl").css("display", "none");
                $(".xloading-image").hide();
            }
        }
    });
}
</script>
<!-- end modal update/add other info -->