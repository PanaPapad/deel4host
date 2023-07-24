# **WpForo Custom Fields**
This plugin is designed to extend the functionality of the WpForo plugin in wordpress.
It adds the ability to create custom fields and forms for the forum.

## **Plugin structure**
### Directories
1. **root** - plugin root directory. Contains the main plugin file, a file with global vars/functions, and the readme file.
2. **page_content** - contains files with the content of the pages of the plugin.
    1. **JS** - contains files with the JS code of the pages of the plugin.
3. **page_functions** - contains files with the functions of the pages of the plugin.

## **Plugin development logic**
- **Main file**
The main plugin file is located in the root directory. It contains the code that is executed when the plugin is activated. The code creates the menu items for the admin panel.

- **Globals**
The Globals.php file is located in the root directory. It contains global variables and functions that are used by the plugin. The file is included in the main plugin file.

- **Pages**
The HTML of each page is inside a PHP file in the page_content directory. The file contains minimal PHP code, most of which is function calls that are defined in the matching file inside the page_functions directory(see **PHP**). 

- **PHP**
The PHP code that will be executed when the page is loaded is inside the page_function directory. The PHP code does not manipulate the HTML document directly. Any changes to the HTML document are made by JavaScript code. The PHP code only generates the data that will be used by the JavaScript code. Data passing and JS function calls are done via injection of JS code into the HTML document. Injection is done by calling a function defined in the Globals.php file (root directory). The function takes a string of JS code as an argument and injects it into the HTML doc with a script tag, the code is executed at the window.onload event. After execution, the code is removed from the HTML doc.

- **JavaScript**
JavaScript files are located in the page_content/JS directory. JS files can be injected in pages by calling a function defined in the Globals.php file (root directory). The function takes a list of JS file names as an argument and injects them into the page.

--All available field types can be found in wpforo Forms.php class.--