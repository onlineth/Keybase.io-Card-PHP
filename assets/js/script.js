//SSL!
if (window.location.protocol != "https:")
    window.location.href = "https:" + window.location.href.substring(window.location.protocol.length);

//Clear anything and focus on the input box
$('#maininput').val('');
$('#maininput').focus();

//Check to see if there is any paramaters and if so, check to see if it's a username
if(window.location.href.substring(window.location.href.lastIndexOf("/") + 1) != '') {
	if (window.location.href.substring(window.location.href.lastIndexOf("#") + 1) != window.location.href) {
		if (window.location.href.substring(window.location.href.lastIndexOf("#") + 1)) {
			if ((window.location.href.substr(window.location.href.length-1)) != "#") {
			    $('#maininput').val(window.location.href.substring(window.location.href.lastIndexOf("#") + 1));
			    start();
			}
		}
	}
}

//Bind the Go button and bind the enter key on the input box
$('#GO').click(function() {
    start();
});
$('#maininput').keypress(function(e) {
    if (e.which == 13) {
        start();
    }
});

//Check to see if the url has changed
window.onpopstate = function (event) {
  if (event.state) {
    //This script changed the url
  } else {
  	//The user chaned the url
    if(window.location.href.substring(window.location.href.lastIndexOf("/") + 1) != '') {
        $('#maininput').val(window.location.href.substring(window.location.href.lastIndexOf("/") + 2));
        start();
    }
  }
}

//Start (close alert box, update image urls, add code, link images, and update url)
function start(){
    $('#alertBox').hide();
    $('#GO').addClass('active');
    document.getElementById('default-theme-card').src='https://keybase.onlineth.com/'+$("#maininput").val()+'.png?theme=default';
    document.getElementById('clean-theme-card').src='https://keybase.onlineth.com/'+$("#maininput").val()+'.png?theme=clean';
    document.getElementById('dark-theme-card').src='https://keybase.onlineth.com/'+$("#maininput").val()+'.png?theme=dark';
    $('#default-theme-code').val('<a href="https://keybase.io/'+$("#maininput").val()+'"><img src="https://keybase.onlineth.com/'+$("#maininput").val()+'.png" width="210" height="58" alt="keybase.io profile for '+$("#maininput").val()+'"></a>');
    $('#clean-theme-code').val('<a href="https://keybase.io/'+$("#maininput").val()+'"><img src="https://keybase.onlineth.com/'+$("#maininput").val()+'.png?theme=clean" width="210" height="58" alt="keybase.io profile for '+$("#maininput").val()+'"></a>');
    $('#dark-theme-code').val('<a href="https://keybase.io/'+$("#maininput").val()+'"><img src="https://keybase.onlineth.com/'+$("#maininput").val()+'.png?theme=dark" width="210" height="58" alt="keybase.io profile for '+$("#maininput").val()+'"></a>');
    $('#default-theme-card-a').attr("href", 'https://keybase.io/'+$("#maininput").val());
    $('#clean-theme-card-a').attr("href", 'https://keybase.io/'+$("#maininput").val());
    $('#dark-theme-card-a').attr("href", 'https://keybase.io/'+$("#maininput").val());
    history.pushState('', $("#maininput").val() + ' Keybase.io Card', location.protocol + '//' + location.host + location.pathname + '#' + $("#maininput").val());
}

//Show images
function checkImageLoad() {
    $('#cards').show('400');
    $('#GO').removeClass('active');
}

//Show an error message
function reportError(message) {
    $('#GO').removeClass('active');
    $('#alertMessage').html(message);
    $('#alertBox').show('400');
}

//Image didn't load
function imgError() {
    reportError("There was an error (probably the username you typed in doesn't exist).");
    $('#cards').hide();
}