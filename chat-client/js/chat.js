var con = new WebSocket('ws://localhost:8080');

$(function() {
	
	var chatBox = $('#chat_box');

	con.onopen = function(e) {
		console.log('Connection established!');
	}

	con.onmessage = function(chat) {
		chatBox.append( createChat(chat.data) );
	}
	
	$('#chat_input').keydown(function(e) {
		
		// Don't mind me... just grabbing the key...
		// Rhyme all the time.... Rhyme on the dime...
		var key = e.which || e.keyCode;
		
		// If the user pressed enter
		if ( key === 13 ) {
			// Send the chat
			sendChat( $(this).val() );
			
			// Clear the input
			$(this).val('');
		}
	});

});

function sendChat(content) {
	
	// Get the username...
	var username = $('#chat_username').val();
	
	// JSON encode data
	var messageData = JSON.stringify({
		'username': username,
		'content': content
	});
	
	$('#chat_box').append(createChat( messageData ));
	
	// Send over socket
	con.send( messageData );
}

function createChat(chat) {
	
	chat = JSON.parse(chat);
	
	// Set variables for easy access
	var username = chat.username;
	var content = chat.content;
	
	/* 
		No templating because:
		A. So little content.
		B. Both values are user set.
	*/
	var HTML = '<div class="message">\
						<span class="message_username">' + username + ' said:</span></br><hr>\
						<span class="message_content">' + content + '</span>\
					</div>';
	
	return HTML;
}