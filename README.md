# Keybase Card
A way to share your Keybase profile online easily through an image implemented through PHP.

### Install
Simply clone this repository on an Apache-based web server with PHP support and it'll be up in no time.

### Usage

Simply call the function:

	generate_keybase_card(Username, Theme, Callback);


Where:

 *	Username: Username on Keybase.io
 *	Theme: (optional) Default, Dark, & Clean
 *	Callback: Function to call when generation is complete

So, for example, try test.js

	node test.js

Which contains:

```javascript
//Execute script.js
var fs = require('fs');
eval(fs.readFileSync('script.js')+'');


//Call the function
generate_keybase_card('online', 'clean', callback_func);

//This is the callback
function callback_func(data, error) {

	//Check for an error
	if (!error) {

		//Write the image to the disk named "card.png"
		fs = require('fs');
		fs.writeFile('card.png', data, 'binary');

		console.log('Works, check for card.png in this directory');
	} else {

		//Log the error on the command line
		console.log('There was an error;\n'+error);
	}
}
```

# Keybase.io Card Examples

###Default Theme:

![Default Theme Card](https://i.imgur.com/mt4Ya4s.png)

###Clean Theme:

![Clean Theme Card](https://i.imgur.com/ZVJEESB.png)

*This one's transparent*

###Dark Theme:

![Dark Theme Card](https://i.imgur.com/7wWJ6Yb.png)

# License

This project is licensed under the [MIT License](https://github.com/onlineth/Keybase.io-Card-Node.js/blob/master/LICENSE)
