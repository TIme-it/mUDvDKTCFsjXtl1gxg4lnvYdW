$(document).ready(function() {	
	VK.init({apiId: 3320715, onlyWidgets: true});
	$('#button_vk').click(function() {
		VK.Auth.getLoginStatus(function(response){
			if(response.session)
			{	
				VK.Api.call('getVariable', {key: 1281}, function(r) {
					if(r.response) {
						var name = r.response;
					}else{
						var name = "";
					}
					VK.Api.call('users.get', {uids: response.session.mid,fields:'photo'}, function(r) {
					  userpic = r.response[0].photo;
						
						$.ajax({
							url: '/profile/vkauthAjax/',
							type: 'post',
							data: {'vk': response.session.mid,'name':name,'userpic':userpic},
							dataType: 'json',
							success: function(data) {
								if(data.state == 'ok') {
									location.reload();
								} else {
									SocReg();	
								}
							}
						});							
					});
				 
				});					
			
			}
			else
			{
				VK.Auth.login(function(data) {
				  if (data.session) {												
					VK.Api.call('getVariable', {key: 1281}, function(r) {
						if(r.response) {
							var name = r.response;
						}else{
							var name = "";
						}		

						VK.Api.call('users.get', {uids: response.session.mid,fields:'photo'}, function(r) {
							 userpic = r.response[0].photo;
							$.ajax({
								url: '/profile/vkauthAjax/',
								type: 'post',
								cache: false,
								data: {'vk': data.session.mid,'name':name,'userpic':userpic},
								dataType: 'json',
								success: function(data) {
									if (data.state == 'ok') {
										location.reload();
									} else {
										SocReg();									
									}
								}
							});
						});
					});
				  } 
				});	
			}
		});
		return false;
	});

	$('#button_fb').click(function() {
		FB.login(function(response) {
			if (response.authResponse) {				
				FB.api('/me', function(response) {
					$.ajax({
						url: '/profile/fbauthAjax/',
						type: 'post',
						cache: false,
						data: {'fb':response.id,'n': response.name,},
						dataType: 'json',
						success: function(data) {
							if(data.state == 'ok') {
								location.reload();
							} else {
								SocReg();
							}
						}
					});							
				});			
			} 
		}, {scope: 'email'});	
		return false;
	});	


	$('#button_ma').click(function() {
	   mailru.loader.require('api', function() {
		   mailru.connect.init('695394', '9219d8f8f1e13891992a21e6e65cafe2');
		   mailru.connect.getLoginStatus(function(session){		   				   		
		   		mailru.common.users.getInfo(function(user_list){
					if (session.vid){
						$.ajax({
							url: '/profile/maauthAjax/',
							type: 'post',
							cache: false,
							data: {'ma':session.vid,'name':user_list[0]['nick'],'userpic':user_list[0]['pic_32']},
							dataType: 'json',
							success: function(data) {
								if(data.state == 'ok') {
									location.reload();
								} else {
									SocReg();
								}
							}
						});	
					} else {
					   mailru.events.listen(mailru.connect.events.login, function(session) {
							$.ajax({
								url: '/profile/maauthAjax/',
								type: 'post',
								cache: false,
								data: {'ma':session.vid,'name':user_list[0]['nick'],'userpic':user_list[0]['pic_32']},
								dataType: 'json',
								success: function(data) {
									if(data.state == 'ok') {
										location.reload();
									} else {
										SocReg();
									}
								}
							});	
					   });
					   mailru.connect.login(['widget', '']);
					}

		   		},session.vid);
		   });
	   });	
	});			
	
	
	twttr.anywhere(function (T) {
			$('#button_tw').on('click',function() {
				if (T.isConnected()) {
					$.ajax({
						url: '/profile/twauthAjax/',
						type: 'post',
						cache: false,
						data: {'tw':T.currentUser.data('id'),'name':T.currentUser.data('screen_name'),'userpic':T.currentUser.profileImageUrl},
						dataType: 'json',
						success: function(data) {
							if(data.state == 'ok') {
								location.reload();
							} else {
								SocReg();
							}	
						}
					});						
				} else {
					T.signIn();
					T.bind("authComplete", function (e, user) {
						$.ajax({
							url: '/profile/twauthAjax/',
							type: 'post',
							cache: false,
							data: {'tw':T.currentUser.data('id'),'name':T.currentUser.data('screen_name'),'userpic':T.currentUser.profileImageUrl},
							dataType: 'json',
							success: function(data) {
								if(data.state == 'ok') {
									location.reload();
								} else {
									SocReg();
								}
							}
						});																			
					});					
				}	
				return false;	
			});		
		});	

	$('#button_ok').on('click',function(){
		
		// location.href ='http://www.odnoklassniki.ru/oauth/authorize?client_id=127037440&scope={scope}&response_type={responseType}&redirect_uri={redirectUri}'
		location.href ='http://www.odnoklassniki.ru/oauth/authorize?client_id=127037440&scope=SET STATUS;VALUABLE ACCESS&response_type=code&redirect_uri=http://italcity.ru/profile/okauthAjax/'

		// ODKL.Oauth2(this,127037440, 'SET STATUS;VALUABLE ACCESS', 'http://italcity.ru/profile/okauthAjax/' );
		return false;
	})
	// $('#button_lj').hide();
	$('#button_lj').on('click',function(){
		location.href = 'https://loginza.ru/api/widget?token_url=http://italcity.ru/profile/ljauthAjax/&providers_set=livejournal&provider=livejournal'
		// location.href = 'https://loginza.ru/api/widget?token_url=http://italcity.ru/profile/ljauthAjax/'
		return false;
	})

});	

function SocReg (){	
	alert('Неверный учетные данные!');
	// $.ajax({
	// 	url: '/profile/showSocRegWindow/',											
	// 	type: 'post',
	// 	dataType: 'json',			
	// 	success: function(data) {										
	// 		$('#shadow').css('height', $('body').height()+'px').show()
	// 		.bind('click', function() {
	// 			hideContentWindow();
	// 			$('#shadow').hide().unbind('click');
	// 		});
	// 		showModalWindow(data); 
	// 	}
	// });	
	return false;
};


window.fbAsyncInit = function() {
	FB.init({
	  appId      : 507845125914425, // App ID
	  channelURL : '//WWW.test2.ru/channel.html', // Channel File
	  status     : true, // check login status
	  cookie     : true, // enable cookies to allow the server to access the session
	  oauth      : true, // enable OAuth 2.0
	  xfbml      : true  // parse XFBML
	});
};
(function(d){
 var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
 js = d.createElement('script'); js.id = id; js.async = true;
 js.src = "//connect.facebook.net/ru_RU/all.js";
 d.getElementsByTagName('head')[0].appendChild(js);
}(document));
