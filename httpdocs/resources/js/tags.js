adicionarEditarTag = function(node){
    
    $("#modalTag").find('.alert').removeClass('alert-error').removeClass('alert-success').css('display','none');
    $("#modalTag").find('#addBtn').removeAttr('disabled');
    $("#modalTag").find('#addBtn').html($("#modalTag").find('#addBtn').attr('data-label-insert'));
    $("#modalTag").find('#myModalLabel').html($("#modalTag").find('#myModalLabel').attr('data-label-insert'));
    
    if(node != null){
        $("#modalTag").find('#id').val($(node).attr('data-tag-id'));
        $("#modalTag").find('#tagName').val($(node).attr('data-tag-name'));
        $("#modalTag").find('#addBtn').html($("#modalTag").find('#addBtn').attr('data-label-update'));
        $("#modalTag").find('#myModalLabel').html($("#modalTag").find('#myModalLabel').attr('data-label-update'));
    }
    
    $("#modalTag").modal().css({
        "width": function () {
            return ($(document).width() * .5) + "px";
        },
        "margin-left": function () {
            return -($(this).width() / 2);
        }
    });

    $('#addBtn').click(function(){
        
        $("#modalTag").find('#addBtn').attr('disabled', 'disabled');
        
        $.ajax({
            url         : "/tags/edit",
            type        : 'post',
            data        : $("#modalTag").find('form').serialize(),
            dataType    : 'json',
            success     : function(a){
                if(a.redirectTo){
                   document.location=a.redirectTo;
                }else{
                    $("#modalTag").find('.alert').addClass('alert-error');
                    $("#modalTag").find('.alert-message').html(a.message);
                    $("#modalTag").find('.alert').fadeIn();
                }
            }
        });
    });
}
