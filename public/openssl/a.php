<?php   
$private_key = "/tmp/openssl/rsa_private.pem";
    $pblic_key = "/tmp/openssl/rsa_public.pem";
	$privatekey = openssl_pkey_get_private(file_get_contents($private_key));
    $publickey = openssl_pkey_get_public(file_get_contents($pblic_key));

    $content = "这是原始文件";
    $encryptData="";//秘钥字符串
    openssl_private_encrypt($content,$encryptData,$privatekey);
    $mima = base64_encode($encryptData);
    
    $data = base64_decode($mima);
    openssl_public_decrypt($data,$go,$publickey);

	echo $mima;
    echo $go;
