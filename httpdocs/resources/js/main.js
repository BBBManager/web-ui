forgetPassClickHandler = function(e){
    e.stopPropagation();
    e.preventDefault();
    
    $("#modalForgetPass").modal();
    
    $('#forgetPassButton').click(function(){
        $("#modalForgetPass").find('form').submit();
    });
}

$('body').on('hidden', '.modal', function () {
    $(this).removeData('modal');
});

$('body').on('shown', '.modal', function () {
    $('input:not(input[type=button],input[type=submit]):visible,textarea:visible,select:visible',this).filter(':first').focus();
    
    window.setTimeout(
	function(){
	    $('a[data-toggle=tooltip]').tooltip();
	},
	500
    );
});

$(document).ready(function(){
    $('form.form-signin').find('[name="user"]').focus();
})