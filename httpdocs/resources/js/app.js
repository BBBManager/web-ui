accessProfileUpdateInterval = null;
monitoraFrameInterval = null;

$(document).ready(function() {

    $('a.btn-delete').click(function(e) {
        if (!confirm('Deseja realmente excluir esse registro ?')) {
            e.stopPropagation();
            e.preventDefault();
            return false;
        }
    });

    $('a[data-toggle=tooltip]').tooltip();

    $("#accordion2").collapse({
        toggle: false
    });

    $('.datetime').datetimepicker({
    	maskInput: true,
        pickSeconds: false,
        language: $.datepicker._defaults.language,
        changeDate: function(dateText) {
		    //display("Selected date: " + dateText + "; input's current value: " + this.value);
		    console.log('selecionou');
		    console.log($(this));
		}
    });
    
    $('div.datetime[copy-to]').datetimepicker().on('changeDate', function (e) {
    	var theCopyToElement = $(this);
		var selectorStr = theCopyToElement.attr('copy-to');
		var elementDate = e.localDate;
		$(selectorStr).data('datetimepicker').setLocalDate(elementDate);
	});
	
	$('div.datetime[copy-to]').each(function() {
		var theCopyToElement = $(this);
		var selectorStr = theCopyToElement.attr('copy-to');
		
		$(selectorStr).datetimepicker().on('changeDate', function (e) {
			//theCopyToElement.val($(this).find('input').val()).change();
			var elementDate = e.localDate;
			$(theCopyToElement).data('datetimepicker').setLocalDate(elementDate);
			
		});
	});
	
    $('input.value[copy-to]').on('change', function () {
    	var theCopyToElement = $(this);
		var selectorStr = theCopyToElement.attr('copy-to');
		//var elementDate = e.localDate;
		var value = theCopyToElement.val();
		$(selectorStr).val(value);
	});
	
	$('input.value[copy-to]').each(function() {
		var theCopyToElement = $(this);
		var selectorStr = theCopyToElement.attr('copy-to');
		
		$(selectorStr).on('change', function () {
			//theCopyToElement.val($(this).find('input').val()).change();
			var value = $(this).val();
			$(theCopyToElement).val(value);
		});
	});
	
	
    $('.datepick').datetimepicker({
        pickTime: false,
        language: $.datepicker._defaults.language
    });

    $('textarea.autosize').autosize();

    $('.accordion-toggle.filhos').click(function() {

        aberto = $(this).find('i').hasClass('icon-chevron-down');

        $(this).parents('.accordion').find('i').removeClass('icon-chevron-down').addClass('icon-chevron-right');

        if (!aberto) {
            $(this).find('i').toggleClass('icon-chevron-down');
        }
    });

    $("#accordion2").find('.in').parents('.accordion-group').find('i').removeClass('icon-chevron-down').addClass('icon-chevron-down');

    /*caso o menu ativo seja um sub item do menu, abre o menu correspondente*/
    $('li.active').parents('.accordion-group').find('.accordion-toggle.filhos').trigger('click');

    $('#excluirSelecionados').click(function() {
        urlAtual = document.location.toString();
        urlDelecao = urlAtual.replace('/index', '');
        urlDelecao = urlDelecao.concat('/delete');
        selecionados = new Array();

        $('input.to-delete:checked').each(function() {
            selecionados[selecionados.length] = $(this).val();
        });

        if (selecionados.length == 0) {
            alert('Você deve selecionar os registros que deseja excluir.');
            return false;
        }

        if (!confirm('Confirma a exclusão de ' + selecionados.length + ((selecionados.length > 1) ? ' registros' : ' registro') + ' ?')) {
            return false;
        }

        $.ajax({
            url: urlDelecao,
            method: 'post',
            data: {
                selecionados: selecionados
            },
            dataType: 'json',
            success: function(a) {
                if (a.redirectTo) {
                    document.location = a.redirectTo;
                }

            }
        });
    });


    $('#selecionarTodos').click(function() {
        var teste = $('table tbody tr td input');
        teste.each(function() {
            this.checked = true;
        });
    });

    $('#clearFilters').click(function() {
        document.location = [location.protocol, '//', location.host, location.pathname].join('');
    });

    $('#btnRefreshUsersAccessProfiles').click(function() {

        $(this).attr('data-uuid', generateUUID());
        $(this).hide();
        $('#btnRefreshUsersAccessProfiles').parents('.alert').find('.alert-message').hide();

        $.ajax({
            type: 'get',
            url: '/ui/access-profiles-update',
            data: {r: $('#btnRefreshUsersAccessProfiles').attr('data-uuid')},
            beforeSend: function() {
                $('#btnRefreshUsersAccessProfiles').attr('disabled', 'disabled');

                progressBar = $('#btnRefreshUsersAccessProfiles').parents('.alert').find('.progress');
                $(progressBar).removeClass('hidden');

                progressBar = $('#btnRefreshUsersAccessProfiles').parents('.alert').find('#log').find('#status').html('<img src="/resources/img/ajax-loader.gif"/> ' + app_messages['processing']);

                refreshProgressBar(progressBar);
            }
        });
        accessProfileUpdateInterval = setTimeout(handleUsersAccessProfilesRefresh, 1000);
    });

    $('[href-origin$="export-pdf"]').click(function(e) {
        mustExport = true;
        recordsNode = new Array();

        if ($(recordsNode).length > 0) {
            if($(recordsNode).hasClass('dtables')){
                oTable = $(recordsNode).dataTable();
                recordCount = oTable.fnSettings().fnRecordsTotal();

                if (recordCount > 200) {
                    mustExport = confirm('Esta exportação de PDF poderá levar muito tempo para ser processada. Deseja realmente exportar?');
                }
            }else{
                mustExport = true;
            }
        }

        if (mustExport) {
            _wait({
                'text': app_messages['loading_content']
            });

            $.fileDownload(
                    $(this).attr('data-href'),
                    {
                        successCallback: function() {
                            _wait.stop();
                        }
                    }
            );
        }
    });

    $('#hideRoomsWithoutRecordings').click(function() {
        if($(this).is(':checked')){
            $(this).val('1');
        }else{
            $(this).val('0');
        }
        
        if($(this).hasClass('view-mode-list')){
            applyFilter($('#records'));        
        }else{
            paramName = 'hide-without-recordings';
            currentUrl = document.location.pathname;
            
            if(currentUrl.indexOf(paramName) == -1){
                newLocation = currentUrl + '/' + paramName + '/' + $(this).val();
            }else{
                currentHideWithoutRecordingsFragment = currentUrl.substr(currentUrl.indexOf('/'+paramName),('/'+paramName).length+3);
                newLocation = currentUrl.replace(currentHideWithoutRecordingsFragment, '/' + paramName + '/' + $(this).val() + '/');
            }
            document.location = newLocation;
            
        }
    });
    
    $('#recordingSelection').select2();
    $('#recordingSelection').on('change', function(e){
        setRecordingOnPlaybackFrame(e.val);
    });
    
    $('#recordingSelection').change(function(){
        _wait({
            'text': 'Obtendo lista de tags'
        });
        
        recordId = $(this).find('option[value="' + $(this).val() + '"]').attr('data-record-id');
        
        $.ajax({
            type        : 'get',
            url         : '/ui/tags/get-tags-by-recording',
            data        : {id:recordId},
            dataType    : 'json',
            success     : function(a){
                $('#viewRecordingTagSelector').parents('.row-fluid').removeClass('hidden');
                _ul = $('#viewRecordingTagSelector').find('ul');
                $(_ul).find('li').remove();
                
                if(a.collection.length == 0){
                    $('#viewRecordingTagSelector').find('a.btn').addClass('disabled');
                }

                $(a.collection).each(function(index, item){
                    $('<li><a data-target-ts="' + item.start_time + '">'+ item.name+'</a></li>').appendTo($(_ul));
                });
                
                $(_ul).find('[data-target-ts]').click(function(){
                    setTimestampRecordingOnPlaybackFrame($(this));
                });
                
                if(a.collection.length > 0){
                    $('#viewRecordingTagSelector').find('a.btn').removeClass('disabled');
                }
                
                _wait.stop();
            }
        });
    });
    
    $('a.edit-recording').not('.disabled').click(function(){
        _wait({
            'text': app_messages['loading_content']
        });
        
        _form = $('#editRecording').find('form');
        $(_form).attr('source', '/ui/recordings/info/id/' + $(this).attr('data-recording-id'));
        loadFormContent($(_form));
        $('#editRecording').removeClass('hidden');
        updateTagsContainer($(this).attr('data-recording-id'));
    });
    
    $('.send-invitatios').click(function(){
        meetingRoomId = $(this).attr('data-meeting-room-id');
        //$('#modalInvite-' + meetingRoomId).modal('show');
        
        $('#modalInvite-' + meetingRoomId).modal().css({
            'width': function() {
                return ($(document).width() * .9) + 'px';
            },
            'margin-left': function() {
                return -($(this).width() / 2);
            }
        });
        
        _modal = $('#modalInvite-' + meetingRoomId).data('modal').$element;
        
        $('#modalInvite-' + meetingRoomId).on('shown', function(){
            $(_modal).find('.save-buttons').show();
            $(_modal).find('.success-buttons').hide();
            loadFormContent($(_modal).find('form'));
            //CKEDITOR.replace('body-'+meetingRoomId);
            initCkEditor('body-'+meetingRoomId);
        });
        
        /*$(_modal).find('form').submit(function(){
            console.log($(this).serialize());
            return false;
            
            modalFormSubmit($(this), _modal);
            return false;
        });*/
        
        $(_modal).find('form').find('[type="submit"],button.confirm').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            
            _form = $(this).parents('form');
            _textarea = $(_form).find('textarea');
            _textareaId = $(_textarea).attr('id');
            _ckeditorValue = CKEDITOR.instances[_textareaId].getData();
            $(_textarea).val(_ckeditorValue);
            
            modalFormSubmit($(_form), _modal, e);
            return false;
        });
    });
    
    $(".invite_template_id").on("change", function(e) {
        meetingRoomId = $(this).attr('data-meeting-room-id');
        _modal = $(this).parents('.modal').data('modal').$element;
        _jsonData = $(this).select2("data");
        
        $(_modal).find('[name="subject"]').val(_jsonData.subject);
        $(_modal).find('[name="body"]').val(_jsonData.body);
        
        initCkEditor('body-'+meetingRoomId);
        
        //$(_modal).find('.modal-body').scrollTop(0);
        setTimeout(function(){$('#modalInvite-' + meetingRoomId).find('.modal-body').scrollTop(0);}, 100);
    });
});

refreshProgressBar = function(progressBarNode, perc) {
    if (typeof perc == undefined) {
        perc = 1;
    }

    bar = $(progressBarNode).find('.bar');

    if ($(bar).attr('data-perc') < perc) {
        $(bar).attr('data-perc', perc);
        $(bar).width(perc + '%');
    } else if ($(bar).attr('data-perc') == undefined) {
        $(bar).attr('data-perc', 0);
    }
};

handleUsersAccessProfilesRefresh = function() {
    progressBarContainer = $('#btnRefreshUsersAccessProfiles').parents('.alert').find('.progress');
    progressBar = $(progressBarContainer).find('.bar');

    $.ajax({
        type: 'get',
        url: '/ui/access-profiles-update/check',
        data: {p: $(progressBar).attr('data-perc'), r: $('#btnRefreshUsersAccessProfiles').attr('data-uuid')},
        dataType: 'json',
        success: function(a) {
            if (a.success == '1') {
                perc = parseInt(a.perc);
                refreshProgressBar(progressBarContainer, perc);

                if (a.total != null) {
                    $(progressBarContainer).parents('.alert').find('#log').find('#total').html(a.total);
                }

                if (a.done != null) {
                    $(progressBarContainer).parents('.alert').find('#log').find('#done').html(a.done);
                }

                if (a.perc >= 100) {
                    $(progressBarContainer).addClass('hidden');
                    clearTimeout(accessProfileUpdateInterval);
                    $(progressBarContainer).parents('.alert').find('#log').find('#status').html('<span style="color:#006900;font-weight:bold;">' + app_messages['completed'] + '</span>');
                }
            } else {
                clearTimeout(accessProfileUpdateInterval);
                $(progressBar).parents('.alert').find('#log').find('#status').html('<span style="color:#c00;font-weight:bold;">' + app_messages['refresh_page_message'] + '</span>');
            }
        }
    });

    accessProfileUpdateInterval = setTimeout(handleUsersAccessProfilesRefresh, 2000);
}

/// - // - // Delete all above later

$(document).ready(function() {
    $("a.btn.add").live("click", function() {
        var $tr = $(this).closest('div.row-filter');
        $tr.find('.select2').select2('destroy');
        var $clone = $tr.clone();
        $tr.after($clone);

        $clone.find('select.column:visible').val($tr.find('select.column:visible').val());
        $clone.find('select.subselect_column:visible').val($tr.find('select.subselect_column:visible').val());
        $clone.find('select.condition:visible').val($tr.find('select.condition:visible').val());
        $clone.find('.value').val('');



        var allSelect2 = $.merge($tr.find('.select2'), $clone.find('.select2'));
        allSelect2.each(function() {
            var elementSelect2 = $(this);
            if ((elementSelect2).attr('remote')) {
                elementSelect2.select2({
                    placeholder: "",
                    minimumInputLength: 4,
                    multiple: elementSelect2.hasClass('multiple'),
                    ajax: {
                        url: elementSelect2.attr('remote'),
                        dataType: 'json',
                        cache: true,
                        data: function(term, page) {
                            return {q: term, page_limit: 10};
                        },
                        results: function(data, page) {
                            return {results: data};
                        }
                    },
                    initSelection: function(element, callback) {
                        var id = $(element).val();
                        var urlRemote = $(element).attr('remote');
                        if (id !== "") {
                            $.ajax(urlRemote, {
                                data: {currvalue: id},
                                dataType: "json"
                            }).done(function(data) {
                                callback(data);
                            });
                        }
                    },
                    escapeMarkup: function(m) {
                        return m;
                    }
                });


            } else {
                elementSelect2.select2({width: '80%'});
            }
        });


        $($clone).find('.datetime').datetimepicker({
            pickSeconds: false,
            language: $.datepicker._defaults.language
        });
        $($clone).find('.datepick').datetimepicker({
            pickTime: false,
            language: $.datepicker._defaults.language
        });


        var modal = $(this).closest("#modalAdvSearch");
        if (modal.find('.row-filter a.btn.delete').size() > 1) {
            modal.find('a.btn.delete').removeAttr('disabled');
        }
    });

    $("a.btn.delete").live("click", function() {
        var modal = $(this).closest("#modalAdvSearch");
        if (modal.find('.row-filter a.btn.delete').size() == 1) {
            return;
        }
        $(this).closest('div.row-filter').remove();


        if (modal.find('.row-filter a.btn.delete').size() == 1) {
            modal.find('a.btn.delete').attr('disabled', 'disabled');
        }
    });
    
    var totalBtnDelete = $('#modalAdvSearch .row-filter a.btn.delete').size();
    if(totalBtnDelete == 1) {
    	$('#modalAdvSearch .row-filter a.btn.delete').attr('disabled', 'disabled');
    }
        

    $("select.column,select.subselect_column").live("change", function() {
        var element = $(this).find('option:selected');

        if ($(this).hasClass('column')) {
            $(this).parent().find('select.subselect_column').hide();
        }

        if (element.attr('show-subselect')) {
            var element = $(this).parent().find('select.subselect_column[name="' + element.attr('show-subselect') + '"]');
            element.show();
        }

        var name = element.val();
        var row = $(this).closest('div.row-filter');

        row.find('div.filter-values').hide();
        row.find('div.filter-values.' + name).show();
    });

    $("select.condition").live("change", function() {
        var element = $(this).find('option:selected');
        var condition = element.val();
        var thisOption = $(this).closest('div.filter-values');

        if (condition == 'empty' || condition == 'nempty') {
            var elementInput = thisOption.find('.value:enabled');
            if (elementInput.size() > 0) {
                if (elementInput.hasClass('select2')) {
                    elementInput.select2('destroy');
                    elementInput.prop('disabled', true);
                    elementInput.select2();
                } else {
                    elementInput.prop('disabled', true);
                }
            }
        } else {
            var elementInput = thisOption.find('.value:disabled');
            if (elementInput.size() > 0) {
                if (elementInput.hasClass('select2')) {
                    elementInput.select2('destroy');
                    elementInput.prop('disabled', false);
                    elementInput.select2();
                } else {
                    elementInput.prop('disabled', false);
                }
            }
        }
    });


    $("button[modal-open],a[modal-open]").live("click", function(e) {
        var modalSelector = $(this).attr('modal-open');

        e.stopPropagation();
        e.preventDefault();
        $(modalSelector).modal().css({
            "width": function() {
                return ($(document).width() * .95) + "px";
            },
            "margin-left": function() {
                return -($(this).width() / 2);
            }
        });
    });

    $('#auth-type').change(function() {
        gerenciarTipoUsuario();
    });

    dtablesDefaultOptions = {
        //sDom: "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        //bPaginate : false,
        //bLengthChange : false,
        iDisplayLength: 10,
        sPaginationType: "full_numbers",
        //bFilter : false,
        //oSearch : {"sSearch": "Initial search"},

        bFilter: true,
        bSort: true,
        bInfo: false,
        bAutoWidth: false
    };
	
	if(typeof dtables_default_oLanguage != "undefined") {
		dtablesDefaultOptions.oLanguage = dtables_default_oLanguage;
	}
    


    $('.dtables').each(function() {
        if ($(this).find('tbody').find('tr').length <= 1 && !$(this).attr('ajaxsource')) {
            return;
        }

        dtablesOptions = dtablesDefaultOptions;

        aoColumns = new Array();
        iSort = null;

        $(this).find('th').each(function(i, curr) {
            sortable = ($(this).hasClass('no-sort')) ? false : true;
            searchable = ($(this).hasClass('no-search')) ? false : true;
            iSort = ($(this).hasClass('sort-desc')) ? null : i;

            aoColumns.push({
                'bSortable': sortable,
                "bSearchable": searchable
            });
        });

        if (iSort != null) {
            rSort = new Array();
            rSort[rSort.length] = iSort;
            rSort[rSort.length] = 'desc';

            dtablesOptions.aaSorting = new Array();
            dtablesOptions.aaSorting[dtablesOptions.aaSorting.length] = rSort;
        }

        dtablesOptions.aoColumns = aoColumns;

        if ($(this).attr('ajaxsource')) {
            dtablesOptions.bProcessing = "true";
            dtablesOptions.sAjaxSource = $(this).attr('ajaxsource');
        }


        if ($(this).attr('data-fnInitComplete')) {
            initCompleteCallback = window[$(this).attr('data-fnInitComplete')];
            if (typeof initCompleteCallback == 'function') {
                dtablesOptions.fnInitComplete = initCompleteCallback;
            }
        }

        $(this).dataTable(dtablesOptions);

        if ($(this).hasClass('rows-selectable')) {
            $(this).find('tbody').delegate("tr", "click", function(e) {

                if (e.target.nodeName.toLowerCase() == 'a' || e.target.nodeName.toLowerCase() == 'i')
                    return;

                var checkbox = $(this).find('input.cboxSelectRow');
                if (checkbox.size() == 0 || $(checkbox).is(':disabled'))
                    return;

                if ($(this).hasClass('row_selected')) {
                    $(this).removeClass('row_selected');
                    checkbox.removeAttr('checked');
                } else {
                    $(this).addClass('row_selected');
                    checkbox.attr('checked', 'checked');
                }
            });

            $(this).find('input.cboxSelectRow').change(function() {
                //$('input.cboxSelectAll').removeAttr('checked');
                if ($(this).attr('checked')) {
                    $(this).parent('tr').addClass('row_selected');
                } else {
                    $(this).parent('tr').removeClass('row_selected');
                }
            });
        }

        //disable export button when filtering
        $('#records_filter.dataTables_filter input').bind('keyup', function() {
            var value = $(this).val();
            if (value.length == 0) {
                $('button.btn-export').removeAttr('disabled');
            } else {
                $('button.btn-export').attr('disabled', 'disabled');
            }
        });

    });
});


nomeSalaDup = function(idSalaOrigem) {
    $("#modalRoomName").modal().css({
        "width": function() {
            return ($(document).width() * .5) + "px";
        },
        "margin-left": function() {
            return -($(this).width() / 2);
        }
    });

    $("#modalRoomName").find('#salaIdOrigem').val(idSalaOrigem);

    $('#addBtn').click(function() {
        $.ajax({
            url: '/ui/rooms/duplicate',
            method: 'post',
            data: $('#dadosNovaSala').serialize(),
            dataType: 'json',
            success: function(a) {
                if (a.redirectTo) {
                    document.location = a.redirectTo;
                }
            }
        });
    });
};

$(document).ajaxSuccess(function(event, xhr, settings) {
    try {
        var data = jQuery.parseJSON(xhr.responseText);
        if (typeof data.success != undefined) {
            if (data.success == '-1') {
                _redirect('/login/auth/logout');
            }
        }

        //if(typeof data.redirect != undefined) {
        //	_redirect(data.redirect);
        //}

    } catch (e) {
        //alert('invalid json');
    }
});


selectAll = function(tableSelector) {
    table = $(tableSelector);
    $('#records').find('input.cboxSelectRow').not(':disabled').attr('checked', 'checked').parents('tr').addClass('row_selected');
};

unselectAll = function(tableSelector) {
    table = $(tableSelector);
    $('#records').find('input.cboxSelectRow').not(':disabled').removeAttr('checked').parents('tr').removeClass('row_selected');
};

deleteAllSelected = function(tableSelector, urlApi) {
    table = $(tableSelector);
    oTable = $(tableSelector).dataTable();
    selectedRecords = new Array();

    $(table).find('input.cboxSelectRow:checked').each(function() {
        selectedRecords[selectedRecords.length] = $(this).val();
    });

    if (selectedRecords.length == 0) {
        return false;
    }

    _confirm({
        title: app_messages['confirm_single_delete_title'],
        text: selectedRecords.length + ' ' + ((selectedRecords.length > 1) ? app_messages['confirm_deletes'] : app_messages['confirm_delete']),
        ok: app_messages['remove'],
        cancel: app_messages['cancel']
    }).done(function() {
        url = urlApi + selectedRecords.join(',');

        for (i = 0; i < selectedRecords.length; i++) {
            $('#row_' + selectedRecords[i]).addClass('removing');
        }
        $.ajax({
            url: url,
            type: 'delete',
            dataType: 'json',
            success: function(a) {
                if (a.success == '1') {
                    for (i = 0; i < selectedRecords.length; i++) {
                        var rowToDelete = oTable.$('#row_' + selectedRecords[i]);
                        oTable.fnDeleteRow(rowToDelete[0]);
                    }
                } else {
                    _alert({
                        type: 'error',
                        title: 'Error',
                        text: a.msg
                    });
                    for (i = 0; i < selectedRecords.length; i++) {
                        $('#row_' + selectedRecords[i]).removeClass('removing');
                    }
                }
            }
        });

    }).fail(function() {
        // your optional alert code
    });
};

deleteRow = function(tableSelector, rowId, urlApi) {
    oTable = $(tableSelector).dataTable();
    oRow = oTable.$('#' + rowId);
    row = $('#' + rowId);

    _confirm({
        title: app_messages['confirm_single_delete_title'],
        text: app_messages['confirm_single_delete_text'],
        ok: app_messages['remove'],
        cancel: app_messages['cancel']
    }).done(function() {
        $(row).addClass('removing');
        //row.addClass('removing');
        $.ajax({
            url: urlApi,
            type: 'delete',
            dataType: 'json',
            success: function(a) {
                if (a.success) {
                    oTable.fnDeleteRow(oRow[0]);
                } else {
                    _alert({
                        type: 'error',
                        title: 'Error',
                        text: a.msg
                    });
                    //row.removeClass('removing');
                    $(row).removeClass('removing');
                }
            }
        });
    }).fail(function() {
        // your optional alert code
    });

    return false;
};


duplicateRow = function(form_data) {
    var form = $('form#formDuplicate');

    for (name in form_data) {
        element = form.find('input[name="' + name + '"],select[name="' + name + '"],textarea[name="' + name + '"]');
        if (element.length > 0) {
            if (element.is('input') && element.attr('type') == 'text') {
                element.val(form_data[name]);
            } else if (element.is('input') && element.attr('type') == 'hidden') {
                element.val(form_data[name]);
            } else if (element.is('input') && element.attr('type') == 'radio') {
                $('input[name="' + name + '"][value="' + form_data[name] + '"]').attr('checked', 'checked');
            } else if (element.is('select')) {
                element.val(form_data[name]);
            } else if (element.is('textarea')) {
                element.html(form_data[name]);
            }
        }
    }


    form.find('.modal-footer .save-buttons').show();
    form.find('.modal-footer .alert-error').html('').hide();
    form.find('.modal-footer .loading').hide();

    $("#modalDuplicate").modal().css({
        "width": function() {
            return ($(document).width() * .5) + "px";
        },
        "margin-left": function() {
            return -($(this).width() / 2);
        }
    });

};

parseRestResponse = function() {
};


$('form[filter]').submit(function() {
    var tableSelector = $(this).attr('filter');
    var context = $(this).attr('context') ? $(this).attr('context') : null;

    applyFilter(tableSelector, context);
    return false;
});

applyFilter = function(tableSelector, contextSelector) {
    if (typeof (contextSelector) == 'string' && contextSelector.length > 0) {
        _parent = $(contextSelector);
    } else {
        _parent = $('div.span9');
        
        if($(_parent).length == 0){
            _parent = $('div.span12');
        }
    }
    table = $(tableSelector);
    oTable = $(tableSelector).dataTable();

    //var count = 0;

    var objectFilters = new Object;
    objectFilters.musthave = $(_parent).find('input[name="musthave"]:checked').val();
    objectFilters.q = [];
    
    /*
    $(_parent).find('#frmMainFilter div.row-filter').each(function() {
        var obj = $(this).find('input.column,select.column option:selected').get(0);
        var column = $(obj).val();
        var divValues = $(this).find('div.filter-values.' + column);
        var value = $(divValues).find('input.value,select.value').val();
        if (value != null && typeof value == 'object')
            value = value.join();
        objectFilters[column] = value;
        objectFilters[column + '_c'] = $(divValues).find('input.condition,select.condition').val();

        if (objectFilters[column + '_c'] == 'b') {
            objectFilters[column + '_2'] = $(divValues).find('input.value_2').val();
        }
    });
    */
    
    $(_parent).find('#frmAdvFilter div.row-filter').each(function() {
        var objColumn = new Object;
        var obj = $(this).find('input.column,select.column option:selected').get(0);
        if ($(obj).attr('show-subselect')) {
            var obj = $(this).find('select.subselect_column[name="' + $(obj).attr('show-subselect') + '"] option:selected').get(0);
        }
        var name = $(obj).val();
        var divValues = $(this).find('div.filter-values.' + name);

        objColumn.n = $(obj).val();
        objColumn.c = $(divValues).find('input.condition,select.condition').val();
        objColumn.v = $(divValues).find('input.value,select.value').val();
        if (objColumn.v != null && typeof objColumn.v == 'object')
            objColumn.v = objColumn.v.join();

        if (objColumn.c == 'b') {
            objColumn.u = $(divValues).find('input.value_2').val();
        }

        objectFilters.q[objectFilters.q.length] = objColumn;
    });
    
    //objectFilters.count = count;
    strFilters = '?' + jQuery.param(objectFilters);

    oTable.fnReloadAjax(table.attr('ajaxsource-nofilters') + strFilters);

    if (table.attr('uri-nofilters') != undefined && typeof window.history.pushState != "undefined") {
        window.history.pushState({
            url: table.attr('uri-nofilters') + strFilters
        }, $(document).attr('title'), table.attr('uri-nofilters') + strFilters);
    }

    var linksExport = $('a[export="' + table.attr('id') + '"]');
    linksExport.each(function() {

        var lnkExport = $(this);
        if (!lnkExport.attr('href-origin')) {
            lnkExport.attr('href-origin', lnkExport.attr('href'));
        }

        lnkExport.attr('href', lnkExport.attr('href-origin') + strFilters);
    });
};

loadFormContent = function(form) {
    var url = form.attr('source');
    var loading = form.hasClass('no-loading') ? false : true;

    $.ajax({
        url: url,
        method: 'get',
        dataType: 'json',
        success: function(a) {
            if (a.success) {
                if (a.form) {
                    form_data = a.form;
                    for (name in form_data) {
                        element = $('input[name="' + name + '"],select[name="' + name + '"],textarea[name="' + name + '"]');
                        if (element.length > 0) {
                            if (element.is('input') && element.attr('type') == 'text') {
                                element.val(form_data[name]);

                                if (element.attr('data-format')) {
                                    element.change();
                                }

                            } else if (element.is('input') && element.attr('type') == 'hidden') {
                                element.val(form_data[name]);
                            } else if (element.is('input') && element.attr('type') == 'radio') {
                                $('input[name="' + name + '"][value="' + form_data[name] + '"]').attr('checked', 'checked');
                            } else if (element.is('select')) {
                                element.val(form_data[name]);
                            } else if (element.is('textarea')) {
                                element.html(form_data[name]);

                                if (typeof CKEDITOR != 'undefined' && CKEDITOR && typeof CKEDITOR.instances[name] != 'undefined') {
                                    CKEDITOR.instances[name].setData(form_data[name]);
                                }

                                if (element.hasClass('autosize')) {
                                    element.trigger('autosize.resize');
                                }
                            }
                        }
                    }
                }


                if (a.select2) {
                    select2 = a.select2;
                    for (name in select2) {
                        source = select2[name];

                        collection = $('input.select2[source="' + name + '"]');
                        if (collection.length > 0) {
                            collection.each(function() {
                                element = $(this);
                                value = element.val();
                                element.select2({
                                    data: source,
                                    multiple: element.hasClass('multiple')
                                }).val(value.split());
                            });
                        }
                    }

                    form.prop('select2options', select2);
                }

                if (a.select) {
                    select = a.select;
                    for (name in select) {
                        collection = $('select[source="' + name + '"].select2');
                        if (collection.length > 0) {
                            collection.each(function() {
                                var element = $(this);
                                var value = element.attr('data-value');
                                element.html(select[name]);
                                if (value != null) {
                                    element.val(value.split(','));
                                }
                                element.select2({
                                    width: 'resolve'
                                });
                            });
                        }
                    }
                }

                $('input.select2[remote]').each(function() {
                    var elementSelect2 = $(this);
                    elementSelect2.select2({
                        placeholder: "",
                        minimumInputLength: 4,
                        multiple: elementSelect2.hasClass('multiple'),
                        ajax: {
                            url: elementSelect2.attr('remote'),
                            dataType: 'json',
                            cache: true,
                            data: function(term, page) {
                                return {q: term, page_limit: 10};
                            },
                            results: function(data, page) {
                                return {results: data};
                            }
                        },
                        initSelection: function(element, callback) {
                            var id = $(element).val();
                            var urlRemote = $(element).attr('remote');
                            if (id !== "") {
                                $.ajax(urlRemote, {
                                    data: {currvalue: id},
                                    dataType: "json"
                                }).done(function(data) {
                                    callback(data);
                                });
                            }
                        },
                        escapeMarkup: function(m) {
                            return m;
                        }
                    });
                });

                form.addClass('content_loaded');
            } else {
                _alert({
                    type: 'error',
                    text: a.msg,
                    title: app_messages['error_loading_content']
                });
            }

        },
        beforeSend: function() {
            if (loading)
                _wait({
                    'text': app_messages['loading_content']
                });
        },
        complete: function() {
            if (loading)
                _wait.stop();
            
            formLoadComplete(form);
        },
        error: function() {
            _alert({
                type: 'error',
                text: app_messages['refresh_page_message'],
                title: app_messages['error_loading_content']
            });
        }
    });
};

var controlKeys = [8, 9, 13, 35, 36, 37, 39, 0]; // Backspace, tab, enter, end, home, left, right
var numberKeys = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57];

$(document).ready(function() {
    $('form[readonly="readonly"], div[readonly="readonly"]').each(function() {
        var form = $(this);
        form.find('input[type="text"],input[type="hidden"],select,textarea').attr('readonly', 'readonly');
        form.find('input[type="radio"],select').attr('disabled', 'disabled');
    });

    $.mask.definitions['#'] = '[0-9.]';
    $('input[mask]').each(function() {
        $(this).mask($(this).attr('mask'), {
            placeholder: ""
        });
    });

    $("input.int").not('.int_binded').keypress(function(event) {
        if (controlKeys.join(",").match(new RegExp(event.which)) || numberKeys.join(",").match(new RegExp(event.which)))
            return;
        else
            event.preventDefault();
    }).addClass('.int_binded');

    $("input.numeric").not('.numeric_binded').keypress(function(event) {
        if (controlKeys.join(",").match(new RegExp(event.which)) || numberKeys.join(",").match(new RegExp(event.which)) || "44,46,45".match(new RegExp(event.which)))
            return;
        else
            event.preventDefault();
    }).addClass('numeric_binded');

    $("input.ipaddress").not('.ipaddress_binded').keypress(function(event) {
        if (controlKeys.join(",").match(new RegExp(event.which)) || numberKeys.join(",").match(new RegExp(event.which)) || "46".match(new RegExp(event.which)))
            return;
        else
            event.preventDefault();
    }).addClass('ipaddress_binded');

    $('form[source]').not('.load-onclick, .content_loaded').each(function() {
        var form = $(this);
        loadFormContent(form);
    });
    
    $('form.ajaxsubmit').submit(function() {
        if (typeof CKEDITOR != 'undefined' && CKEDITOR) {
            for (var instanceName in CKEDITOR.instances)
                CKEDITOR.instances[instanceName].updateElement();
        }

        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(a) {
                if (a.success == 1) {
                    if (a.redirect)
                        _redirect(a.redirect);
                    if (a.msg)
                        _alert({
                            type: 'success',
                            title: app_messages['success_title'],
                            text: a.msg
                        });
                } else {
                    _alert({
                        type: 'error',
                        title: app_messages['error_saving_title'],
                        text: a.msg
                    });
                }
            },
            complete: function() {
                _wait.stop();
            },
            beforeSend: function() {
                _wait({
                    'text': app_messages['saving_data']
                });
            }
        });

        return false;
    });

    $('form#formDuplicate').submit(function() {
        var form = $(this);
        var url = form.attr('action');
        var data = form.serialize();

        form.find('.modal-footer .alert-error').html('').hide();
        ;

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'JSON',
            success: function(a) {
                if (a.success == 1) {
                    form.find('#btn_edit_copy').click(function() {
                        _redirect(a.edit_url);
                        return false;
                    });

                    form.find('#btn_close_copy').click(function() {
                        $("#modalDuplicate").modal('hide');
                        $('#records').dataTable().fnReloadAjax();
                        return false;
                    });

                    form.find('.modal-footer .success-buttons').show();
                } else {
                    form.find('.modal-footer .alert-error').html(a.msg).show();
                    form.find('.modal-footer .save-buttons').show();
                }
            },
            complete: function() {
                form.find('.modal-footer .loading').hide();
            },
            fail: function() {
                form.find('.modal-footer .alert-error').html('refresh_page_message').show();
                form.find('.modal-footer .save-buttons').show();
            },
            beforeSend: function() {
                form.find('.modal-footer .save-buttons').hide();
                form.find('.modal-footer .loading').show();
            }
        });

        return false;
    });

    $('div.span9').delegate("a[open-modal-form]", "click", function(e) {
        var modal = $($(this).attr('open-modal-form'));
        var form = modal.find('form:first');

        modal.find('.modal-footer .alert-error').html('').hide();
        modal.find('.modal-footer .success-buttons').hide();
        modal.find('.modal-footer .success-buttons .alert-success').html('');
        modal.find('.modal-footer .save-buttons').show();

        if (!form.hasClass('no-reset')) {
            form[0].reset();

            if (typeof CKEDITOR != 'undefined' && CKEDITOR) {
                var textareas = form.find('textarea');
                textareas.each(function() {
                    var textarea = $(this);
                    if (typeof CKEDITOR.instances[textarea.attr('id')] != 'undefined') {
                        CKEDITOR.instances[textarea.attr('id')].setData('');
                    }
                });
            }
        }

        //populate form
        if ($(this).attr('form-data')) {
            var elementData = $(this).attr('form-data');
            var jsonData = JSON.parse(elementData);

            for (prop in jsonData) {
                if (jsonData[prop] == null || jsonData[prop].length == 0)
                    continue;

                var element = form.find('[name="' + prop + '"]');
                if (element.size() > 0) {
                    element.val(jsonData[prop]);

                    if (element.hasClass('select2')) {
                        var arrValues = jsonData[prop].split();
                        element.select2("val", arrValues);
                    }
                }
            }

            if (typeof prop['id'] != 'undefined' && prop['id'] != 'new') {
                //$("#modalTag").find('#addBtn').html($("#modalTag").find('#addBtn').attr('data-label-update'));
                modal.find('#myModalLabel').html($("#modalTag").find('#myModalLabel').attr('data-label-update'));
            } else {
                //$("#modalTag").find('#addBtn').html($("#modalTag").find('#addBtn').attr('data-label-insert'));
                modal.find('#myModalLabel').html($("#modalTag").find('#myModalLabel').attr('data-label-insert'));
            }
        }

        //$(modal).modal();
        $(modal).modal().css({
            'width': function() {
                return ($(document).width() * .9) + 'px';
            },
            'margin-left': function() {
                return -($(this).width() / 2);
            }
        });

        $(form).not('.submitbinded').submit(function() {
            //$("#modalTag").find('#addBtn').attr('disabled', 'disabled');
            
            modalFormSubmit(form, modal);
            
            return false;
        }).addClass('submitbinded');

        return false;
    });

    $('#csvImport').click(function(e) {
        e.stopPropagation();
        e.preventDefault();

        handleCsvImport(this);
    });

    $('.csvImportUser').click(function(e) {
        e.stopPropagation();
        e.preventDefault();

        handleCsvImport(this);
    });
});


_confirm = function(options) {
    if (!options) {
        options = {};
    }

    var show = function(el, text) {
        if (text) {
            el.html(text);
            el.show();
        } else {
            el.hide();
        }
    };

    var url = options.url ? options.url : '';
    var data = options.data ? options.data : '';
    var ok = options.ok ? options.ok : app_messages['ok'];
    var cancel = options.cancel ? options.cancel : app_messages['cancel'];
    var title = options.title;
    var text = options.text;
    var dialog = $('#confirm-dialog');
    var header = dialog.find('.modal-header');
    var footer = dialog.find('.modal-footer');

    show(dialog.find('.modal-body'), text);
    show(dialog.find('.modal-header h3'), title);
    footer.find('.btn-danger').unbind('click').html(ok);
    footer.find('.btn-cancel').unbind('click').html(cancel);
    dialog.modal('show');

    var $deferred = $.Deferred();
    var is_done = false;
    footer.find('.btn-danger').on('click', function(e) {
        is_done = true;
        dialog.modal('hide');
        if (url) {

            _wait();

            $.ajax({
                url: url,
                data: data,
                type: 'POST'
            }).done(function(result) {
                $deferred.resolve(result);
            }).fail(function() {
                $deferred.reject();
            }).aways(function() {
                _wait.stop();
            });
        } else {
            $deferred.resolve();
        }
    });
    dialog.on('hide', function() {
        if (!is_done) {
            $deferred.reject();
        }
    });

    return $deferred.promise();
};

_wait = function(options) {
    if (!options) {
        options = {};
    }

    var dialog = $('#wait-dialog');

    var title = options.title ? options.title : app_messages['wait'];
    if (title.length > 0) {
        dialog.find('.modal-header h3').html(title).show();
    } else {
        dialog.find('.modal-header h3').html('').hide();
    }

    var text = options.text ? options.text : ''; //'Content is being loaded';;
    if (text.length > 0) {
        dialog.find('.modal-body div.text').html(text).show();
    } else {
        dialog.find('.modal-body div.text').html('').hide();
    }


    dialog.modal('show');
};

_wait.stop = function() {
    var dialog = $('#wait-dialog');
    dialog.modal('hide');
};

_alert = function(options) {
    if (!options) {
        options = {};
    }

    var dialog = $('#alert-dialog');

    var title = options.title ? options.title : app_messages['alert'];
    if (title.length > 0) {
        dialog.find('.modal-header h3').html(title).show();
    } else {
        dialog.find('.modal-header h3').html('').hide();
    }

    var text = options.text ? options.text : ''; //'Content is being loaded';;
    if (text.length > 0) {
        dialog.find('.modal-body').html(text).show();
    } else {
        dialog.find('.modal-body').html('').hide();
    }

    var type = options.type ? options.type : '';

    var header = dialog.find('.modal-header');
    header.removeClass();
    if (type == 'error') {
        header.addClass('modal-header alert alert-error');
    } else if (type == 'success') {
        header.addClass('modal-header alert alert-success');
    } else if (type == 'info') {
        header.addClass('modal-header alert alert-info');
    } else {
        header.addClass('modal-header');
    }

    dialog.modal('show');
};

_redirect = function(url) {
    if (!url || url == 'undefined' || url == '')
        url = window.location.protocol + '//' + window.location.host;
    window.location = url;
};


$(document).ready(function() {
    if ($('#counter').length > 0) {
        $('#counter').countdown({
            image: '/resources/js/jquery/plugins/countdown/img/digits.png',
            startTime: $('#counter').attr('data-countdown'),
            timerEnd: function() {
                window.location.reload();
            }
        });
    }
});

var mustRefresh = false;

handleCsvImport = function(srcElement) {
    $("#modalCsvImport").modal();

    modalDomNode = $("#modalCsvImport").data('modal').$element;
    hiddenElements = $(modalDomNode).find('form').find('input[type="hidden"]').not('.files');

    $(hiddenElements).each(function() {
        hiddenElementName = $(this).attr('name');
        attribName = 'data-' + hiddenElementName;

        if ($(srcElement).attr(attribName) != undefined) {
            $('[name="' + hiddenElementName + '"]').val($(srcElement).attr(attribName));
        }
    });

    $("#modalCsvImport").on('shown', function() {
        modalDomNode = $(this).data('modal').$element;
        fileList = $(modalDomNode).find('#fileContainer');
        form = $(modalDomNode).find('form');
        messagesContainer = $(modalDomNode).find('.alert');

        $(fileList).empty();
        $(form).find('input[type="hidden"].files').remove();
        $(modalDomNode).find('[data-widget="import"]').attr('disabled', 'disabled');
        $(messagesContainer).addClass('hidden').find('.alert-message').addClass('hidden');
        $(modalDomNode).find('.resume').empty();

        $('#fileupload').fileupload({
            dataType: 'json',
            change: function(e, data) {
                $(fileList).empty();
                $(modalDomNode).find('.resume').empty();
                $(form).find('input[type="hidden"].files').remove();
                $(modalDomNode).find('.alert').addClass('hidden');
                $(modalDomNode).find('[data-widget="import"]').attr('disabled', 'disabled');
                $(modalDomNode).find('[data-widget="import-users"]').attr('disabled', 'disabled');
            },
            done: function(e, data) {
                $.each(data.result.files, function(index, file) {
                    $('<p/>').text(file.displayName).appendTo(fileList);
                    $('<input type="hidden" name="files[]" class="files"/>').val(file.url).appendTo(form);
                    $(modalDomNode).find('[data-widget="import"]').removeAttr('disabled');
                });
            }
        });
    });

    /*$("#modalCsvImport").on('hide', function(){
     if(mustRefresh == true){
     document.location.reload();
     }
     });*/
};

csvUpload = function(srcElement) {
    modalElement = $(srcElement).parents('.modal');
    formElement = $(modalElement).find('form');
    messagesContainer = $(modalElement).find('.alert');

    $.ajax({
        type: 'post',
        url: $(formElement).attr('data-import-url'),
        data: $(formElement).serialize(),
        dataType: 'json',
        beforeSend: function() {
            $(messagesContainer).removeClass('hidden');
            $(messagesContainer).find('.alert-message.processing').removeClass('hidden');
        },
        complete: function() {
            $(messagesContainer).find('.alert-message.processing').addClass('hidden');
            $(modalDomNode).find('[data-widget="import"]').attr('disabled', 'disabled');

        },
        error: function(a, b, c) {
        },
        success: function(a) {
            $(messagesContainer).find('.alert-message.import-result').removeClass('hidden').html(a.msg);
            $(modalElement).find('[data-widget="import-users"]').removeAttr('disabled');

            if (!$(modalElement).hasClass('room-users-import')) {
                $('#records').dataTable().fnReloadAjax();
            } else {
                if ($(formElement).find('input[name="step"]').length == 0) {
                    $('<input type="hidden" class="files"/>').attr('name', 'step').appendTo(formElement);
                }
                $(modalElement).find('input[name="step"]').val('import');

                if ($(modalElement).find('.resume').length == 0) {
                    $('#fileContainer').after('<div class="resume"></div>');
                }
                $(modalElement).find('.resume').empty();
                $(formElement).find('input[name="users[]"]').remove;

                if (a.data.valid != undefined) {
                    $(modalElement).find('.resume').append('<h4>Usuários encontrados</h4><ul class="valid"></ul>');
                    for (idx in a.data.valid) {
                        $(modalElement).find('.resume').find('ul.valid').append('<li>' + a.data.valid[idx] + '</li>');
                        $(formElement).find('input[name="permission"]').after('<input type="hidden" class="files" name="users[]" value="' + idx + '"/>');
                    }
                }

                if (a.data.invalid != undefined) {
                    $(modalElement).find('.resume').append('<h4>Usuários não encontrados</h4><ul class="invalid"></ul>');
                    for (idx in a.data.invalid) {
                        $(modalElement).find('.resume').find('ul.invalid').append('<li>' + a.data.invalid[idx] + '</li>');
                    }
                }
            }
        }
    });
};

importRoomUsers = function(srcElement) {
    modalElement = $(srcElement).parents('.modal');
    formElement = $(modalElement).find('form');
    messagesContainer = $(modalElement).find('.alert');

    permission = $(formElement).find('input[name="permission"]');
    validUsers = $(formElement).find('input[name="users[]"]');
    validUserIds = new Array();

    currentUsersSelector = '';

    if ($(permission).val() == '1') {
        currentUsersSelector = '#user_admin_ldap';
    } else if ($(permission).val() == '2') {
        currentUsersSelector = '#user_moderator_ldap';
    } else if ($(permission).val() == '3') {
        currentUsersSelector = '#user_presenter_ldap';
    } else if ($(permission).val() == '4') {
        currentUsersSelector = '#user_attendee_ldap';
    }

    validUserIds = $(currentUsersSelector).select2('val');

    $(validUsers).each(function() {
        validUserIds[validUserIds.length] = $(this).val();
    });

    $(currentUsersSelector).select2('val', validUserIds);

    $("#modalCsvImport").modal('hide');

    /*$.ajax({
     type	    : 'post',
     url	    : $(formElement).attr('data-import-url'),
     data	    : $(formElement).serialize(),
     dataType    : 'json',
     beforeSend : function() {
     $(messagesContainer).removeClass('hidden');
     $(messagesContainer).find('.alert-message.processing').removeClass('hidden');
     },
     complete : function() {
     $(messagesContainer).find('.alert-message.processing').addClass('hidden');
     $(modalDomNode).find('[data-widget="import-users"]').attr('disabled', 'disabled');
     
     },
     error : function(a,b,c) {
     },
     success : function(a){
     $(messagesContainer).find('.alert-message.import-result').removeClass('hidden').html(a.msg);
     mustRefresh = true;
     }
     });*/
}




window.timeoutList = new Array();
window.intervalList = new Array();

window.oldSetTimeout = window.setTimeout;
window.oldSetInterval = window.setInterval;
window.oldClearTimeout = window.clearTimeout;
window.oldClearInterval = window.clearInterval;

window.setTimeout = function(code, delay) {
    var retval = window.oldSetTimeout(code, delay);
    window.timeoutList.push(retval);
    return retval;
};
window.clearTimeout = function(id) {
    var ind = window.timeoutList.indexOf(id);
    if (ind >= 0) {
        window.timeoutList.splice(ind, 1);
    }
    var retval = window.oldClearTimeout(id);
    return retval;
};
window.setInterval = function(code, delay) {
    var retval = window.oldSetInterval(code, delay);
    window.intervalList.push(retval);
    return retval;
};
window.clearInterval = function(id) {
    var ind = window.intervalList.indexOf(id);
    if (ind >= 0) {
        window.intervalList.splice(ind, 1);
    }
    var retval = window.oldClearInterval(id);
    return retval;
};
window.clearAllTimeouts = function() {
    for (var i in window.timeoutList) {
        window.oldClearTimeout(window.timeoutList[i]);
    }
    window.timeoutList = new Array();
};
window.clearAllIntervals = function() {
    for (var i in window.intervalList) {
        window.oldClearInterval(window.intervalList[i]);
    }
    window.intervalList = new Array();
};

generateUUID = function() {
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random() * 16) % 16 | 0;
        d = Math.floor(d / 16);
        return (c == 'x' ? r : (r & 0x7 | 0x8)).toString(16);
    });
    return uuid;
};

confirmMeetingDelete = function(tableSelector, rowId, urlApi, recordingsCount){
    oTable = $(tableSelector).dataTable();
    oRow = oTable.$('#' + rowId);
    row = $('#' + rowId);
    
    var recordingDeleteOptions = {
        title: app_messages['confirm_single_delete_title'],
        text: app_messages['confirm_recording_deleting'],
        ok: app_messages['remove'],
        cancel: app_messages['cancel'],
        okClick : function(){
            deleteMeeting(oTable, oRow, row, urlApi);
        }
    }
    
    var roomDeleteOptions = {
        title: app_messages['confirm_single_delete_title'],
        text: app_messages['confirm_meeting_room_deleting'],
        ok: app_messages['remove'],
        cancel: app_messages['cancel']
    }

    
    if(recordingsCount > 0){
        roomDeleteOptions.okClick = function(){
            $('#confirm-dialog').data('modal', null);
            $('.modal-backdrop').remove();
            onlyConfirm(recordingDeleteOptions);
        };
    }else{
        roomDeleteOptions.okClick = function(){
            deleteMeeting(oTable, oRow, row, urlApi);
        };
    }
    
    onlyConfirm(roomDeleteOptions);
}

onlyConfirm = function(options){
    var show = function(el, text) {
        if (text) {
            el.html(text);
            el.show();
        } else {
            el.hide();
        }
    };

    var ok = options.ok ? options.ok : app_messages['ok'];
    var cancel = options.cancel ? options.cancel : app_messages['cancel'];
    var title = options.title;
    var text = options.text;
    var dialog = $('#confirm-dialog');
    var header = dialog.find('.modal-header');
    var footer = dialog.find('.modal-footer');

    show(dialog.find('.modal-body'), text);
    show(dialog.find('.modal-header h3'), title);
    footer.find('.btn-danger').unbind('click').html(ok);
    footer.find('.btn-cancel').unbind('click').html(cancel);
    dialog.modal('show');
    
    footer.find('.btn-danger').on('click', function(e) {
        options.okClick.apply();
    });
};

deleteMeeting = function(oTable, oRow, row, urlApi){
    $('#confirm-dialog').modal('hide');
    $(row).addClass('removing');
    //row.addClass('removing');
    $.ajax({
        url: urlApi,
        type: 'delete',
        dataType: 'json',
        success: function(a) {
            if (a.success) {
                oTable.fnDeleteRow(oRow[0]);
            } else {
                _alert({
                    type: 'error',
                    title: 'Error',
                    text: a.msg
                });
                //row.removeClass('removing');
                $(row).removeClass('removing');
            }
        }
    });

};

setRecordingOnPlaybackFrame = function(recordingUrl){
    $('[name="playback-frame"]').attr('src', recordingUrl);
}

$.fn.treeTableExpandAll = function() {
    $(this).find("tr").removeClass("collapsed").addClass("expanded").each(function() {
        $(this).expand();
    });
};

$.fn.treeTableCollapseAll = function() {
    $(this).find("tr").removeClass("expanded").addClass("collapsed").each(function() {
        $(this).collapseTreeTable();
        ;
    });
};

$(document).ready(function(){
    if($("#categoriesTree tbody tr").length > 0){
        $("#categoriesTree").treeTable({treeColumn: 0, clickableNodeNames: true, indent: 25});
        $("#categoriesTree").treeTableExpandAll();
        
        $("#categoriesTree").find('.select-category').click(function(){
            $(this).parents('tbody').find('.select-category').removeClass('active');
            $(this).parents('tbody').find('.select-category').each(function(){
                $(this).parents('span').removeClass('badge').removeClass('badge-info');
            });
            
            $(this).addClass('active');
            $(this).parents('span').addClass('badge').addClass('badge-info');
            
            $('#category-name').val($(this).parents('tr').attr('data-description'));
            $('#category_id').val($(this).parents('tr').attr('data-row-id'));
        });
    }
});

formLoadComplete = function(form){
    if($('#category_id').val()){
        selectedCategoryNode = $("#categoriesTree").find('tr[data-row-id="' + $('#category_id').val() + '"]');
        $('#category-name').val($(selectedCategoryNode).attr('data-description'));
        
        $(selectedCategoryNode).find('a').addClass('active');
        $(selectedCategoryNode).find('span').addClass('badge').addClass('badge-info');
    }
    
    if($(form).attr('data-form-invite')){
        _textarea = $(form).find('textarea');
        nodeId = $(_textarea).attr('id');
        initCkEditor(nodeId);
    }
};

addTagSuccessCallback = function(form){
    _modal = $(form).parents('.modal').data('modal');
    $(_modal).modal('hide');
    
    updateTagsContainer();
}

updateTagsContainer = function(recordId){
    _form = $('#tagsContainer').parents('form');
    _table = $('#tagsContainer').find('table');
    $('#tagsContainer').find('.alert').hide();
    $(_table).find('tbody').find('tr').not('.refresh').remove();
    $(_table).removeClass('hidden');
    $(_table).find('tr.refresh').removeClass('hidden');
    
    if(recordId == null){
        recordId = $(_form).find('#record_id').val();
    }
    
    $.ajax({
        type        : 'get',
        url         : '/ui/tags/get-tags-by-recording',
        data        : {id:recordId,asHtml:'1'},
        success : function(a){
            if(a.success == '1'){
                $(_table).find('tbody').find('tr.refresh').addClass('hidden');
                
                if(a.html == ''){
                    $('#tagsContainer').find('.alert').show();
                }
                
                $(a.html).each(function(){
                    
                    $(this).find('.tag-delete').click(function(){
                        tagId = $(this).attr('data-tag-id');
                        _confirm({
                            title: app_messages['confirm_single_delete_title'],
                            text: '1 ' + app_messages['confirm_delete'],
                            ok: app_messages['remove'],
                            cancel: app_messages['cancel']
                        }).done(function() {
                            $('a[data-tag-id="' + tagId + '"]').parents('tr').addClass('removing');
                            
                            $.ajax({
                                url: '/ui/tags/delete',
                                data:{id:tagId},
                                type: 'post',
                                dataType: 'json',
                                success: function(a) {
                                    if (a.success == '1') {
                                        $('a[data-tag-id="' + tagId + '"]').parents('tr').remove();
                                    } else {
                                        _alert({
                                            type: 'error',
                                            title: 'Error',
                                            text: a.msg
                                        });
                                        $('a[data-tag-id="' + tagId + '"]').parents('tr').removeClass('removing');
                                    }
                                }
                            });

                        }).fail(function() {
                            // your optional alert code
                        });

                    });
                    
                    $(this).appendTo($(_table));
                });
                
                if($(_table).find('tbody').find('tr').not('.refresh').length > 0){
                    $(_table).find('tbody').find('tr.refresh').addClass('hidden');
                }
            }
        }
    });
}

initCkEditor = function(instanceId){
    var editor = CKEDITOR.instances[instanceId];
    if (editor) { editor.destroy(true); }
    CKEDITOR.replace(instanceId);
}

setTimestampRecordingOnPlaybackFrame = function(ts){
    milliseconds = $(ts).attr('data-target-ts') / 1000;
    window.frames['playback-frame'].goToSlide(milliseconds);
}

modalFormSubmit = function(form, modal, evt){
    var url = form.attr('action');
    var data = form.serialize();
    var inviteConfirmation = false;
    
    if(form.attr('data-form-invite')){
        if(evt){
            submitTriggeredBy = $(evt.target);
            
            $(submitTriggeredBy).attr('disabled', 'disabled');
            
            if($(submitTriggeredBy).hasClass('confirm')){
                data = data + "&max_rcpt_confirmed=1";
                inviteConfirmation = true;
            }
        }
    }

    $.ajax({
        url: url,
        type: 'post',
        data: data,
        dataType: 'json',
        success: function(a) {
            if (a.success == '1') {
                /*
                 form.find('#btn_edit_copy').click(function() {
                 _redirect(a.edit_url);
                 return false;
                 });
                 */

                if (form.attr('source')) {
                    if(! form.attr('data-form-invite'))
                        loadFormContent(form);
                }

                if(form.attr('data-success-callback')){
                    successCallbackFunction = window[form.attr('data-success-callback')];
                    successCallbackFunction.apply(this, [form]);
                }

                $(modal).one('hidden', function() {
                    if (a.redirect) {
                        _wait({
                            'text': app_messages['wait']
                        });
                        _redirect(a.redirect);
                    }

                    if ($('#records').size() > 0) {
                        $('#records').dataTable().fnReloadAjax();
                    }
                });
                
                modal.find('.modal-footer .save-buttons').hide();
                
               if(form.attr('data-form-invite')){
                    if(a.mustConfirm){
                        modal.find('.modal-footer .success-buttons').find('div.alert').removeClass('alert-success').addClass('alert-warning');
                        modal.find('.modal-footer .success-buttons').find('.confirm').removeClass('hidden');
                    }
                }

                if (typeof a.msg != 'undefined') {
                    modal.find('.modal-footer .success-buttons .alert').html(a.msg);
                }
                modal.find('.modal-footer .success-buttons').show();
            } else {
                modal.find('.modal-footer .alert').html(a.msg).show();
            }
        },
        fail: function() {
            modal.find('.modal-footer .alert').html('refresh_page_message').show();
        },
        complete: function() {
            modal.find('.modal-footer button').removeAttr('disabled');
            modal.find('.modal-footer .loading').hide();
            if(inviteConfirmation){
                modal.find('.modal-footer button.confirm').addClass('hide');
            }
        },
        beforeSend: function() {
            modal.find('.modal-footer .alert-error').html('').hide();
            modal.find('.modal-footer button').attr('disabled', 'disabled');
            modal.find('.modal-footer .loading').show();
        }
    });
}

checkWebMSupport = function(){
    if(! isWebMSupported()){
        _confirm({
            title: app_messages['attention'],
            text: app_messages['webm_support'],
            ok: app_messages['understand_continue_anyway'],
            cancel: app_messages['back']
        }).done(function() {
            $("#recordingSelection").trigger("change");
        }).fail(function() {
            history.back();
        });
    }else{
        $("#recordingSelection").trigger("change");
    }
}

isWebMSupported = function(){
    return (document.createElement('video').canPlayType('video/webm') != "");
}