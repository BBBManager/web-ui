manageAuthType = function(){
    if($('#auth-type').val() == 2){
        $('#full-name').attr('disabled','disabled').attr('readonly', 'readonly');
        $('#user-email').attr('disabled','disabled').attr('readonly', 'readonly');
        $('#password').parents('.control-group').hide();
    }else{
        $('#full-name').removeAttr('disabled').removeAttr('readonly');
        $('#user-email').removeAttr('disabled').removeAttr('readonly');
        $('#password').parents('.control-group').show();
    }
}


$(document).ready(function(){
    $('#userResetPassword').click(function(){
        $('#modalConfirmChangePass').modal();
    });
});