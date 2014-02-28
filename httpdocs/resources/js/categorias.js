$(document).ready(function(){
    if($("#tblTree tbody tr").length > 0){
        $("#tblTree").treeTable({treeColumn: 1, clickableNodeNames: true, indent: 25});
    }
    
    $("#tblTree").find('input[type="checkbox"]').live('click', function(){
        nodeId = $(this).parents('tr').attr('id');
        thisIsChecked = $(this).attr('checked');
        
        childNodes = $("#tblTree").find('tr[class^="child-of-' + nodeId + '"]');
        $(childNodes).each(function(){
            if(thisIsChecked == 'checked'){
                $(this).find('input[type="checkbox"]').attr('checked', 'checked');    
            }else{
                $(this).find('input[type="checkbox"]').removeAttr('checked');
            }
        })
    });
});

adicionarCategoria = function(idPai){
    $("#modalCategory").modal().css({
            "width": function () {
                return ($(document).width() * .5) + "px";
            },
            "margin-left": function () {
                return -($(this).width() / 2);
    }});
    $('#addBtn').click(function(){
        catName = $("#catName").val();
        parentId = idPai;
        $.ajax({
            url         : "/categories/edit",
            method      : 'post',
            data        : {
                catName : catName,
                parentId: parentId
            },
            dataType    : 'json',
            success     : function(a){
                if(a.redirectTo){
                   document.location=a.redirectTo;
                }

            }
        });
    });
}

editarCategoria = function(idGrupo){
    if((!idGrupo) || (idGrupo == undefined)){
        return;
    }       

    $.get('/financeiro/lancamento-grupo/get-form',{id: idGrupo}, function(data){
        $('#modal-from-js').modal({
            title   : 'Editar Grupo de Lançamento',
            content : data,
            clean : true,
            buttons : {
                'Confirmar' : {
                    type: 'primary',
                    onClick: (function(){
                        callbackParams = {
                            hdnId: idGrupo,
                            edNomeGrupo: $(this).find('#edNomeGrupo').val()
                        };
                        confirmCallback(callbackParams);
                        $(this).modal('hide');
                    })
                },
                'Cancelar' : {
                    onClick: (function(){
                        $(this).modal('hide');
                    })
                }
            }
        }); 
    });
}


$('#buscaCategorias').typeahead({
    updater : function(a,b){
        $('tr[data-description="' + a + '"]').reveal();
    },
    source : nomesCategorias
});


$('#selecionarTodos').click(function(e){
    $("#tblTree").find('tbody').find('tr').expand();
    $("#tblTree").find('tbody').find('tr:last').reveal();
});


confirmaDelecaoCategoria = function(node){
    _tr = $(node).parents('tr');
    _tbody = $(_tr).parents('tbody');
    _description = $(_tr).attr('data-description');
    _trId = $(_tr).attr('id');
    
    _hasChild = ($(_tbody).find('tr[class^="child-of-' + _trId + '"]').length > 0);
    
    if(_hasChild){
        if(confirm('Você está prestes a excluir a categoria ' + _description + '. Todas as suas subordinadas serão excluídas. Você deseja continuar?')){
            alert('Exclusão categorias + filhas');
        }
    }else{
        if(confirm('Você está prestes a excluir a categoria  ' + _description + '. Você deseja continuar?')){
            alert('Exclusão da categoria');
        }
    }
}