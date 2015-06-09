recaptcha
=========

General description
-------------------

eZ Publish 4.x extension, which implements Google reCAPTCHA


Installation
------------
1. Download and enable recaptcha extension
2. Get reCAPTCHA SiteKey and SecrectKey and update recaptcha.ini settings file.
3. Add "reCATPCHA" attribute to required content classeses (and mark it as required)
4. Clear eZ Publish caches:
```
$ cd EZP-ROOT
$ php bin/php/ezcache.php --clear-all
```
