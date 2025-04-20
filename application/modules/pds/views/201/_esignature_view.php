<div class="col-md-12">
 <!-- BEGIN EXAMPLE TABLE PORTLET-->
 <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-pencil font-dark"></i>
                    <span class="caption-subject bold uppercase">E-signature</span>
                </div>
            </div>
            <div class="portlet-body">

            <div class="row">
                <div class="col-md-12">
                <?php
                $strImageUrl = 'uploads/employees/esignature/'.$arrData[0]['empNumber'].'.png';
                    if(file_exists($strImageUrl))
                    {
                        $strImage = base_url('uploads/employees/esignature/'.$arrData[0]['empNumber'].'.png');
                    }
                    else 
                    {
                        $strImage = base_url('assets/images/logo.png');
                    }   
                    // $strImage = base_url('uploads/employees/'.$arrData['empNumber'].'.jpg');?>
                <img src="<?=$strImage?>" class="img-responsive pic-bordered" width="200px" alt="" />
                </div>
            </div>
            <?=form_open(base_url('hr/upload_esig/'.$this->uri->segment(3)), array('method' => 'post', 'enctype' => 'multipart/form-data'))?>
                <div class="form-body">
                    <?php //print_r($arrPost);?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label">Please select file : </label>
                                <br>
                                <div class="input-icon left">
                                    <i class="fa"></i>
                                    <div class="form-group left">
                                        <input type="file" id="userfile" name="userfile" accept="image/png">               
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="hidden" name="EmployeeId" value="<?=$this->uri->segment(3)?>">
                                <button type="submit" name="upload" class="btn blue start"><i class="fa fa-upload"></i></i> Upload</button>
                                <button class="btn btn-primary" type="reset"><i class="icon-ban"></i> Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?=form_close()?>
            </div>
        </div>
</div>

<?php load_plugin('js',array('validation'));?>
<script type="text/javascript">
    jQuery.validator.addMethod("noSpace", function(value, element) { 
  return value.indexOf(" ") < 0 && value != ""; 
}, "No space please and don't leave it empty");
var FormValidation = function () {

    // validation using icons
    var handleValidation = function() {
        // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var form2 = $('#frmEmpImage');
            var error2 = $('.alert-danger', form2);
            var success2 = $('.alert-success', form2);

            form2.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",  // validate all fields including form hidden input
                rules: {
                     strAgencyName: {
                        minlength: 1,
                        required: true
                    },
                    strAgencyCode: {
                        minlength: 1,
                        required: true,
                    }
                    
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    success2.hide();
                    error2.show();
                    App.scrollTo(error2, -200);
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    var icon = $(element).parent('.input-icon').children('i');
                    icon.removeClass('fa-check').addClass("fa-warning");  
                    icon.attr("data-original-title", error.text()).tooltip({'container': 'body'});
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass("has-success").addClass('has-error'); // set error class to the control group   
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    
                },

                success: function (label, element) {
                    var icon = $(element).parent('.input-icon').children('i');
                    $(element).closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    icon.removeClass("fa-warning").addClass("fa-check");
                },

                submitHandler: function (form) {
                    success2.show();
                    error2.hide();
                    form[0].submit(); // submit the form
                }
            });


    }

    return {
        //main function to initiate the module
        init: function () {
            handleValidation();

        }

    };

}();

jQuery(document).ready(function() {
    FormValidation.init();
});
</script>
