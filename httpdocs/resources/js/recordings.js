$(document).ready(function(){
    if($("#tblTree tbody tr").length > 0){
        $("#tblTree").treeTable({treeColumn: 0, clickableNodeNames: true, indent: 25});
    }
    
    $('[name="tipo-sala"]').click(function(){
        recordingAccessManager(this);
    });
    
    recordingAccessManager($('[name="tipo-sala"]:checked'));
    
    if(typeof nomesCategorias != 'undefined'){
        $('#buscaCategorias').typeahead({
            updater : function(a,b){
                $('tr[data-description="' + a + '"]').next().reveal();
            },
            source : nomesCategorias
        });
    }
});

recordingAccessManager = function(node){
    if($(node).val() == 1){
        $('#access_container').show();
    }else{
        $('#access_container').hide();
    }
}

