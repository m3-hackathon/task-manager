var TMP = TMP || {};
(function( $ ) {
	'use strict';

	TMP.extra = {

		init: function() {
			TMP.extra.userList();
			if(document.querySelector(".time_tracking")){
				TMP.extra.taskStartStop();
			}
		},


		userList: function() {
			var $window = $(window);
			var $body = $('body');
			var	$userList = $('#userList');

			if ($userList.length > 0) {
				$userList.each(function() {
					var listItem = $('#userList ul > li');
					var IDs ;
					var finalIDs = [];
					if(listItem.length){
						finalIDs = $("#userListAdded ul li span[data-id]").map(function() { return $(this).attr("data-id"); }).get();
						jQuery("#finalUserListHere").val(finalIDs);
					}
					$(document).on('input', '#userSearch',function(event) {
						var searchText = $('#userSearch').val();
						if(searchText.length){ $userList.show(); } else{ $userList.hide(); }
						var input, filter, ul, li, a, i;
						input = document.getElementById("userSearch");
						filter = input.value.toUpperCase();
						ul = document.getElementById("userList");
						li = ul.getElementsByTagName("li");
						for (i = 0; i < li.length; i++) {
							a = li[i].getElementsByTagName("a")[0];
							if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
								li[i].style.display = "";
							} else {
								li[i].style.display = "none";
							}
						}
					});
					$(document).on('click', '.tmp #userSearch', function () {
						$userList.show();
					});
					$(document).mouseup(function(e){
						var container = $(".tmp #userSearch");
						if (!container.is(e.target) && container.has(e.target).length === 0) {
							$userList.hide();
						}
					})
					$(document).on('click', '#userList ul > li a', function (e) {
						IDs = $("#userListAdded ul li span[data-id]").map(function() { return $(this).attr("data-id"); }).get();
						var id = $(this).attr("data-id");
						if(IDs.indexOf(id) === -1){
							var name = $(this).attr("data-name");
							var htmlCode = '<li><span class="tab" data-id="'+id+'">'+name+'</span> <i class="close"></i></li>';
							$('#userListAdded ul').append(htmlCode);
							finalIDs.push(id);
							$('#userList').css('display', 'none');
							$('#userSearch').val('');
						}else{
							$('#userList').css('display', 'none');
							$('#userSearch').val('');
							alert('Member Already Added.');
						}
						$("#finalUserListHere").val(finalIDs);
					});
					$(document).on('click', '#userListAdded ul > li i.close', function (e) {
						IDs = $("#userListAdded ul li span[data-id]").map(function() { return $(this).attr("data-id"); }).get();
						var did = $(this).parent().find('span').attr("data-id");
						if(IDs.indexOf(did) > -1){
							$(this).parent().remove();
							finalIDs.splice(IDs.indexOf(did), 1);
						}
						$("#finalUserListHere").val(finalIDs);
					});

				});
			}
		},

		taskStartStop: function(){
			var btnStartElement = $('[data-action="start"]');
			var btnStopElement = $('[data-action="stop"]');
			var btnResetElement = $('[data-action="reset"]');
			var minutes = $('.minutes');
			var seconds = $('.seconds');
			var hours = $('.hours');
			var timerTime = 0;
			var interval;
			var isRunning;

			var existingTime = $('.hidden_time_taken').val();
			var splitted = existingTime.match(/.{1,2}/g);
			timerTime = (parseInt(splitted[0]) ? parseInt(splitted[0]) * 3600 : 0) +  (parseInt(splitted[1]) ? parseInt(splitted[1]) * 60 : 0) + parseInt(splitted[2]);

			function start(){
				$('.tb_start').removeClass('active')
				$('.tb_stop').addClass('active')
				isRunning = true;
				interval = setInterval(incrementTimer, 1000);
				localStorage.setItem('task_time_taking', 'ok');
			}

			function stop(){
				$('.tb_stop').removeClass('active')
				$('.tb_start').addClass('active')
				isRunning = false;
				clearInterval(interval);
				localStorage.removeItem('task_time_taking');
				localStorage.removeItem('task_time_taking_total');
			}

			function reset(){
				hours.innerText = '00';
				minutes.innerText = '00';
				seconds.innerText = '00';
			}

			function pad(number){
				return (number < 10) ? '0' + number : number;
			}

			function incrementTimer(){
				timerTime++;
				var numberMinutes = Math.floor(timerTime / 60);
				var numberHours = Math.floor(numberMinutes / 60);
				var numberSeconds = timerTime % 60;
				var t_m = pad(numberMinutes)
				var t_s = pad(numberSeconds)
				var t_h = pad(numberHours)
				minutes.text(t_m);
				seconds.text(t_s);
				hours.text(t_h);
				if(timerTime % 5 === 0){
					updateTrackingTime(t_h, t_m, t_s)
				}
				localStorage.setItem('task_time_taking_total', ''+timerTime);
			}

			btnStartElement.on('click', function (){ start(); })

			btnStopElement.on('click', function (){ stop(); })

			btnResetElement.on('click', function (){ reset(); })

			function updateTrackingTime(t_h, t_m, t_s){
				var t_times = t_h + '' + t_m + '' + t_s;
				var task_id = $('input[name="task_id"]').val();
				if( !!task_id && !!t_times && typeof ajax_object.ajax_url !== 'undefined' ){
					jQuery.get(ajax_object.ajax_url + '?action=task_time_taken&task_id='+task_id+'&time_taken='+t_times);
				}
			}

			if( localStorage.getItem('task_time_taking') === 'ok' ){
				timerTime = + (localStorage.getItem('task_time_taking_total'))
				start()
			}
		},
	};

	$(document).ready( TMP.extra.init );

})( jQuery );

