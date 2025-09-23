<?php

class MyEncryption {
    public $pubkey = '-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDN9JOkha3yXFvWV+BRbSdV0lF7
lyhipRpx5MFlTt7TP6lI9ZupqYtOnH7gVvMFRPBhRMfqn3vvlA1NDCY/9qjEBAs6
Yqr7xQFYlnTDwLjybw43E/fS8xqXcbg78zQseesrCyFku7ea4FlEb5HaNAAIH8cX
e6ip/9OmlBePZF605wIDAQAB
-----END PUBLIC KEY-----';
    public $privkey = '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDN9JOkha3yXFvWV+BRbSdV0lF7lyhipRpx5MFlTt7TP6lI9Zup
qYtOnH7gVvMFRPBhRMfqn3vvlA1NDCY/9qjEBAs6Yqr7xQFYlnTDwLjybw43E/fS
8xqXcbg78zQseesrCyFku7ea4FlEb5HaNAAIH8cXe6ip/9OmlBePZF605wIDAQAB
AoGAQftNIbRCGhdExNK6ZtvVckVDHZk7sv66Dir/Wnl+IaePkHQ/Poe2vaSdcTnp
+ZIwgLkMYzpc9lA0Qq7VwkA/qJjhbcjcZOT6jhgk8xQDebv9t2h6U2CxXf1+iMSi
EHn5QKK/pn/BEda0O1/oj4KiW9RbXqi/+vYRwtIREi8BdIECQQD8/OySdYUa4FMA
6gBsCA7aEPDB+XVmMU30YvOMA2V+xs0BiRuGAQwl/WKakKOoP0QAyAhPb2Jfi9Bf
qtlCV1oXAkEA0GhNlx62+h7WoiL8xYQxeeEXnpeOuUizF9V5hp0JKb8Jqc4EQS36
kFd3p8ts74QdC+/B0fcA1Yeq2Ws+yfPNsQJAW1g7Vvplz/l1HtxewKL8MdJyC6e1
sutUeUwNId3MFMVVGhvWO0E/kKv3oVVeMg590EZpcb0G6PbDivdWMLT3iwJBAJ+Y
MErYt5CkywKQvndXOzg1WowVTbOv644F6TFf0mOIqxLA9Fshpa6hfL0fOAXXaxL4
ALoUaCwc34Xt7cBjlpECQHzkgAiD5tKCOHfLgwZRDSTPhqzyEIcUceCE2sagmGK0
A7P8vrvr/vpn/qxH9hbbYWuz9GH+2PAVmvqhx3yOSlo=
-----END RSA PRIVATE KEY-----';

    // 패스워드 암호화
    public function pw_encrypt($data)
    {
        $data = base64_encode(hash('sha256', $data, true));
        return $data;
    }

    public function encrypt($data)
    {
        if (openssl_public_encrypt($data, $encrypted, $this->pubkey))
            $data = base64_encode($encrypted);
        else
            throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');

        return $data;
    }

    public function decrypt($data)
    {
        if (openssl_private_decrypt(base64_decode($data), $decrypted, $this->privkey))
            $data = $decrypted;
        else
            $data = '';
        return $data;
    }
}
 
?>