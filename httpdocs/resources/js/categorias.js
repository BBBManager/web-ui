$(document).ready(function(){
    if($("#records tbody tr").length > 0){
        $("#records").treeTable({treeColumn: 1, clickableNodeNames: true, indent: 25});
    }
    
    $("#records").find('input[type="checkbox"]').live('click', function(){
        nodeId = $(this).parents('tr').attr('id');
        thisIsChecked = $(this).attr('checked');
        
        childNodes = $("#records").find('tr[class^="child-of-' + nodeId + '"]');
        $(childNodes).each(function(){
            if(thisIsChecked == 'checked'){
                $(this).find('input[type="checkbox"]').attr('checked', 'checked');    
            }else{
                $(this).find('input[type="checkbox"]').removeAttr('checked');
            }
        })
    });
    
    $('.category-edit-trigger').click(function(){
        categoryManagement(this, false);
    });
    
    $('.category-add-trigger').click(function(){
        categoryManagement(this, true);
    });
    
    $('.category-delete-trigger').click(function(){
        categoryDelete(this);
    });
});


categoryManagement = function(node, isNew){
    $("#modalCategory").modal().css({
            "width": function () {
                return ($(document).width() * .5) + "px";
            },
            "margin-left": function () {
                return -($(this).width() / 2);
    }});

    $("#modalCategory").find('input').val('');

    if(isNew == false){
        $("#modalCategory").find('#category-name').val($(node).attr('data-category-name'));
        $("#modalCategory").find('#category-parent-id').val($(node).attr('data-category-parent-id'));
        $("#modalCategory").find('#category-id').val($(node).attr('data-category-id'));
    }else{
        if($(node).attr('data-category-parent-id')){
            $("#modalCategory").find('#category-parent-id').val($(node).attr('data-category-parent-id'));
        }
    }
    
    $('#addBtn').unbind('click');
    
    $('#addBtn').click(function(){
        $('#addBtn').attr('disabled', 'disabled');
        
        postData = {
            name : $("#modalCategory").find('#category-name').val(),
            parent_id : $("#modalCategory").find('#category-parent-id').val(),
        }
        
        if(isNew == false){
            postData.id = $("#modalCategory").find('#category-id').val();
        }
        
        $.ajax({
            url             : "/ui/categories/form-post",
            type            : 'post',
            data            : postData,
            dataType        : 'json',
            success         : function(a){
                if(a.success == '1'){
                   document.location.reload();
                }else{
                    _alert({
                        type: 'error',
                        title: 'Error',
                        text: a.msg
                    });
                }
            }
        });
    });
}

$('#buscaCategorias').typeahead({
    updater : function(a,b){
        $('tr[data-description="' + a + '"]').reveal();
    },
    source : categoriesName
});


$('#selectAll').click(function(e){
    $("#records").find('tbody').find('tr').expand();
    $("#records").find('tbody').find('tr:last').reveal();
});

categoryDelete = function(node){
    confirmOptions = {
        title: app_messages['confirm_single_delete_title'],
        text: app_messages['confirm_category_deleting'],
        ok: app_messages['remove'],
        cancel: app_messages['cancel']
    };
    
    isChild = false;
    parentNodeId = '';
    
    console.log(node);
    
    if($(node).hasClass('category-delete-trigger-all') == false){
        _tr = $(node).parents('tr');
        _tbody = $(_tr).parents('tbody');
        
        subcategories = $(_tbody).find('tr[class^="child-of-' + $(_tr).attr('id') + '"]');

        if($(subcategories).length > 0){
            confirmOptions.text = app_messages['confirm_category_with_sub_deleting'];
        }
        
        elementCssClass = $(_tr).attr('class').split(' ');

        $(elementCssClass).each(function(a, b){
            if(b.indexOf('child-of-') != -1){
                isChild = true;
                parentNodeId = b.substring(9+(b.indexOf('child-of-')), b.length);
            }
        });
        
        idToDelete = $(node).attr('data-category-id');
    }else{
        isChild = false;
        
        toDeleteCollection = new Array();
        
        $('.cboxSelectRow:checked').each(function(){
            toDeleteCollection[toDeleteCollection.length] = $(this).val();
        });
        
        idToDelete = toDeleteCollection.join(',');
    }
    
    _confirm(
        confirmOptions
    ).done(function() {
        _wait({
            'text': app_messages['loading_content']
        });

        $.ajax({
            url: '/ui/categories/delete',
            data : {id:idToDelete},
            type: 'post',
            dataType: 'json',
            success: function(a) {
                if (a.success == '1') {
                    window.location.reload();
                } else {
                    _alert({
                        type: 'error',
                        title: 'Error',
                        text: a.msg
                    });
                    
                    _wait.stop();
                }
            }
        });
    }).fail(function() {
        // your optional alert code
    });
};