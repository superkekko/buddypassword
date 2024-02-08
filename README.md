# buddypassword
Buddypassword is a simple personal and shared password manager self hosted

## Install and upgrade
### Requirements
- apache 2.4 (or Nginx)
- php 8.0

### Installation
Copy the files in the folder and give access to public. Default user is superadmin/superadmin

### Updates
Just copy fhe new version files

## Security
To ensure the security of stored passwords, two levels of encryption are applied in addition to the system login.

During password storage, AES-CBC (SHA-256) encryption is applied with a key stored only on the local device (by saving an MD5-encrypted cipher in a cookie) through JavaScript (using the Web Crypto API module). This encrypted information is then further encrypted during storage in the database using another protocol, AES-256-CBC, with the key stored on the server (randomly generated during the installation process).

### Firefox
https://addons.mozilla.org/it/firefox/addon/buddypassword/

### Chrome
to be inserted

## To do

## Modules
- Fat-Free Framework 3.8.2 (https://fatfreeframework.com)
- Font Awesome Free 6.3.0 (https://fontawesome.com)
- Bootstrap 5.0.2 (https://getbootstrap.com/)
- jQuery 3.6.0 (https://jquery.com/)
- jQuery UI 1.13.1 (https://jqueryui.com/)
- List.js 2.3.1 (https://listjs.com/)
- Select2 4.0.13 (https://select2.org/)
- TippyJs 6.3.7 (https://atomiks.github.io/tippyjs/)
- jshashes 1.0.8 (https://github.com/h2non/jshashes)
