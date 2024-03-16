$(document).ready(function(){

	$("input").on("focus", function(){
		this.setAttribute('autocomplete', 'none');
	});
})

function SaveData() {
	this.DataRecord.IsSaving.value = 1;
	this.form.submit();
}

function DeleteRecord(RecordType) {
	if (confirm(`Are you sure you want to delete this ${RecordType}?`)) {
		this.DataRecord.IsSaving.value = 2;
		this.form.submit();
	}
}

