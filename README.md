recaptcha
=========

General description
-------------------

eZ Publish 4.x extension, which implements Google reCAPTCHA


Installation
------------
1. Download and enable recaptcha extension
2. Clear eZ Publish caches:
```
$ cd EZP-ROOT
$ php bin/php/ezcache.php --clear-all
```
3. Get reCAPTCHA SiteKey and SecrectKey and update recaptcha.ini settings file.
4. Add "reCATPCHA" attribute to required content classeses (and mark it as required)
