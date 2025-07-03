function hide_all() {
	$(
		"#wholeday_textbox,#leavefrom_textbox,#leaveto_textbox,#daysapplied_textbox,#signatory1_textbox,#signatory2_textbox,#attachments,#reason_textbox,#incaseSL_textbox,#incaseVL_textbox,.div-actions,#commutation_box,#incaseSTL_textbox"
	).hide();

	$("#strReason").attr("required", false);
}

function compute_leave_days() {
	var leave_from = $("#dtmLeavefrom").val();
	var leave_to = $("#dtmLeaveto").val();
	var total_days = 0;

	if (leave_from != "" && leave_to != "") {
		$.ajax({
			type: "get",
			url:
				baseUrl +
				"/employee/leave/getworking_days?empid=" +
				$("#txtempno").val() +
				"&datefrom=" +
				leave_from +
				"&dateto=" +
				leave_to,
			dataType: "json",
			success: function (data) {
				$("#intDaysApplied").val(data.length);
				$("#intDaysApplied_val").val(data.length);
			},
		});
	} else {
		$("#intDaysApplied").val("0");
		$("#intDaysApplied_val").val("0");
	}
}

$(document).ready(function () {
	$(".date-picker").datepicker();
	$(".date-picker").on("changeDate", function () {
		$(this).datepicker("hide");
	});

	$("#strLeavetype").on("change", function () {
		hide_all();

		var leave_type = $(this).val().toLowerCase();
		var form_action = "";

		$(
			"#wholeday_textbox,#leavefrom_textbox,#leaveto_textbox,#daysapplied_textbox,#signatory1_textbox,#signatory2_textbox,#attachments,#reason_textbox,.div-actions,#commutation_box"
		).show();

		switch (leave_type) {
			case "fl":
				$("#reason_textbox").show();
				break;
			case "ml":
			case "pl":
			case "spl":
			case "val":
			case "rpl":
			case "sel":
			case "al":
				$("#reason_textbox").hide();
				// $("#strReason").attr("required", false);
				$("#strReason").val("");
				break;
			case "sl":
				$("#incaseSL_textbox").show();
				// $("#strReason").attr("required", true);

				break;
			case "vl":
				$("#incaseVL_textbox").show();
				// $("#strReason").attr("required", true);
				break;
			case "slw":
			case "sppl":
				$("#reason_textbox").show();
				// $("#strReason").attr("required", true);
				break;
			case "stl":
				$("#incaseSTL_textbox").show();
				$("#reason_textbox").hide();
				// $("#strReason").attr("required", false);
				$("#strReason").val("");
				break;
			default:
				hide_all();
				$("#strReason").val("");
				break;
		}
		$("#txttype").val(leave_type);
	});

	$("#dtmLeavefrom").on("keyup keypress change", function () {
		check_null("#dtmLeavefrom", "Leave from must not be empty.");
		compute_leave_days();
	});

	$("#dtmLeaveto").on("keyup keypress change", function () {
		check_null("#dtmLeaveto", "Leave to must not be empty.");
		compute_leave_days();
	});
});
