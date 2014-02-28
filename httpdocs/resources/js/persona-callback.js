(function(window, $, undefined) {
    $(document).ready(function(){
	$('a.persona-signin').click(function(e) {
	    navigator.id.request();
	    e.preventDefault();
	});
	$('a.persona-signout').click(function(e) {
	    navigator.id.logout();
	    e.preventDefault();
	});
    });

    navigator.id.watch({
	loggedInUser: null,
	onlogin: function (assertion) {
	    $('#extraInfo').find('#assertion').val(assertion);
	    requestPersonaAuth();
	},
	onlogout: function () {
	    //window.location = '?logout=1';
	}
    });

    $('#personaWithFullName').click(function(){
	requestPersonaAuth();
    });

    requestPersonaAuth = function(){
	_wait({'text' : app_messages['loading_content']});
	
	assertion = $('#extraInfo').find('#assertion').val();
	roomId = $('#roomId').val();

	$.ajax({
	    type    : 'POST',
	    url	    : '/login/auth/persona',
	    data    : {personaAssertion : assertion},
	    complete : function(){
		_wait.stop();
	    },
	    success : function(personaAuthResponse){
		if(personaAuthResponse.success == '0'){
		    _alert({type : 'error', text : personaAuthResponse.msg, title: app_messages['error_loading_content']});
		}else{
		    if((typeof personaAuthResponse.extraInfo != 'undefined') && (personaAuthResponse.extraInfo == '1')){
			$('#extraInfo').modal();
			$('#extraInfo').find('#btnGo').click(function(){
			    userFullName = $('#extraInfo').find('#name').val();
			    userFullName = (userFullName != '' ? userFullName : null);
			    
			    $.ajax({
				type	: 'post',
				url	: '/login/auth/persona',
				data	: {personaAssertion : assertion, userName : userFullName},
				success	: function(a){
				    goToRoom(roomId, a.data.id, a.data.token);
				    $('#extraInfo').modal('hide');
				}
			    })
			});
		    }else{
			goToRoom(roomId, personaAuthResponse.data.id, personaAuthResponse.data.token);
		    }
		}
	    },
	    error : function(a){
		_wait.stop();
	    }
	});
    };
})(window, jQuery);


goToRoom = function(roomId, userId, token){
    /*console.log(roomId);
    console.log(userId);
    console.log(token);*/
    
    document.location = '/my-rooms/go/id/' + roomId;
}