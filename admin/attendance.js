$(document).ready(function(){

	$("#check_all").on("change",function(){
		if (this.checked) {
			$(":checkbox").prop('checked', true);
		}
		else {
			$(":checkbox").prop('checked', false);
		}
	});

})

function SaveData() {
	this.Attendance.IsSaving.value = 1;
	this.form.submit();
}
