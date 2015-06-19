<?php
/**
 * @package reCAPTCHA
 * @class   recaptchaType
 * @author  Serhey Dolgushev <dolgushev.serhey@gmail.com>
 * @date    09 Jun 2015
 * */
class recaptchaType extends eZDataType
{
    const DATA_TYPE_STRING = 'recaptcha';

    public function __construct()
    {
        $this->eZDataType(
            self::DATA_TYPE_STRING, ezpI18n::tr('extension/recaptcha', 'reCAPTCHA'), array('serialize_supported' => false)
        );
    }

    public function validateObjectAttributeHTTPInput($http, $base, $attribute)
    {
        return self::validateReCAPTCHA($http, $attribute);
    }

    public function isInformationCollector()
    {
        return true;
    }

    public function validateCollectionAttributeHTTPInput($http, $base, $attribute)
    {
        return self::validateReCAPTCHA($http, $attribute);
    }

    public function diff($old, $new, $options = false)
    {
        return null;
    }

    protected static function validateReCAPTCHA(eZHTTPTool $http, eZContentObjectAttribute $attribute)
    {
        // No captcha validation is requried for admin siteaccess
        $ini               = eZINI::instance();
        $additionalDesigns = $ini->variable('DesignSettings', 'AdditionalSiteDesignList');
        if (in_array('admin', $additionalDesigns)) {
            return eZInputValidator::STATE_ACCEPTED;
        }

        if ((bool) $attribute->attribute('is_required') === false) {
            return eZInputValidator::STATE_ACCEPTED;
        }

        $response = $http->variable('g-recaptcha-response', null);
        if (empty($response)) {
            $attribute->setValidationError(
                ezpI18n::tr('extension/recaptcha', 'input is missing')
            );
            return eZInputValidator::STATE_INVALID;
        }

        $data = array(
            'secret'   => eZINI::instance('recaptcha.ini')->variable('General', 'SecrectKey'),
            'response' => $response,
            'remoteip' => eZSys::clientIP()
        );

        $verifyURL = 'https://www.google.com/recaptcha/api/siteverify?';
        foreach ($data as $param => $value) {
            $verifyURL .= $param . '=' . $value . '&';
        }
        $verifyURL = trim($verifyURL, '&');

        $curl     = curl_init($verifyURL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);

        if (is_object($response) && isset($response->success) && (bool) $response->success) {
            return eZInputValidator::STATE_ACCEPTED;
        }

        $attribute->setValidationError(
            ezpI18n::tr('extension/recaptcha', 'invalid input')
        );

        return eZInputValidator::STATE_INVALID;
    }
}
eZDataType::register(recaptchaType::DATA_TYPE_STRING, 'recaptchaType');

