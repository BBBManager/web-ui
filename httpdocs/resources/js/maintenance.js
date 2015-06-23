$(document).ready(function(){
    $('button[name="activate"]').click(function(e){
        if($(this).val() == 1){
            showMaintenanceDialog(e);
        }
    });
    
    $('#update_maintenance_message').click(function(e){
        showMaintenanceDialog(e);
    });
});

showMaintenanceDialog = function(e){
    e.stopPropagation();
    e.preventDefault();
    
    $('#saveBtn').removeAttr('disabled');

    $("#modalMaintenance").modal().css({
            "width": function () {
                return ($(document).width() * .5) + "px";
            },
            "margin-left": function () {
                return -($(this).width() / 2);
    }});

    CKEDITOR.replace("maintenance_message");

    $('#saveBtn').click(function(){
        $('#saveBtn').attr('disabled', 'disabled');

        $.ajax({
            url         : "/ui/maintenance/index",
            type        : 'post',
            data        : {activate : '1', maintenance_messsage : CKEDITOR.instances.maintenance_message.getData()},
            dataType    : 'json',
            success     : function(a){
                if(a.redirectTo){
                   document.location=a.redirectTo;
                }

            }
        });
    });
}