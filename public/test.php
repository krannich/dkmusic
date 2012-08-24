<?php

/*
echo "hallo";
$fullpath = '/Volumes/Music/Music Library/#/' . escapeshellcmd('112 - Dance with me remix by dj-$pecial k.mp3');
echo $fullpath;
//$fullpath = "/Volumes/Music/Music Library/#/112 - Dance with me (club mix).mp3";

		$fpcalc_data = exec('./../bin/fpcalc "' . ($fullpath) . '"');

echo $fpcalc_data;
*/


/*
$start_time = microtime(true);

$dir_path = "/Volumes/Music/Music Library/#/";
$count = count(glob($dir_path . "*"));
echo $count;

echo "time: " . sprintf('%.3f' , (microtime(true) - $start_time));

*/


$start_time = microtime(true);

$acoustid = "ed51f9cf-5c31-4a52-8db5-139f1604a637";
$fingerprint = "AQADtEkSSUqSTGrgEweO44QPnzhwHCcMHweO44Th48BxnDAM4DhOGAZwHCcMAziOE4YB-PBxwjAAHz5OGAbgw8cJwwB8-DhhGIAPnzgMA_DhE4dhAD584jAMwIdPHDh-_IJx4ocBHMYJHwAOwycOAIfhEwcAH_CJAzgM-MQBHAZ84sABAz5x4ICPwycOHIcP-MSBAz4OnzhwHD5x-MSB4_AJ-DhwHD4BHweOwyfg48Bxwj_g4wd8-DhOCgeM3zjhwzAAHz5xGAbgwyd8wDgOHz5xGMZx-PCJw_Bx4vDhE4fh48QJ44QP4Dh8-MRh-DhxGAZwHCcMAziOE4YBw8dxwjCA4zhhGIAPHycMAziOEz6MI7hx5IQhAj984vBxQPAP-McJwwB8-DhhGIAPHycMA_Dh44RhAD58nABwGD5OAAF8w0dOADpwHD8OCIcPHycMA4fhEweAw_CJA8Bh-MQBqPgDwyTxAz8eHO05aKVy2OHx41uGI42PZzh-PEd6oJqCH_LR_9gy4g8ewg8e-JBNHCcO8zgHv3jhE-eyAid8Dr_w1vBxwjhxvILf4IFfXMe-4BpxEdcKV9i5QiROfIQu_McLn0GuRDiuCNZT5JRwH18GHd_gwx3xBtZ0iA_x4CmcDRdx4odzptB5WDe-Q3yHM-EBX8MpXA2uMDBuycJxjtBH3IJ-XMTbwFRw4jhhZvgZ6DVcwWoOvTgHU4J1XHCOUxqcw5-wR0g_GXmOH5ZImMRv6Mfx4TixDz982B7eFOKLE5eIF48CS8cnXLzw4MaHDzdzuMILkyR-nIBPmIQffPhxWMOPE_5xD4dPXPjwcfCNwySNC37h3Dj2wB8hHqeBD8eh88VxE9CJH-6xHz6u8DguQoePV7jxBt_g_Pjx5zgewTng0_iGZ4MpDddx4vDxwzfcHR9OwJfgw7oFP8h9XDQs8fiN__hz-LFw7biXw86PN7iOMGc6PGi2sMrQY1dDoXvgHyV-nDgm7dDhXMSH53DwHLow_zjUH_-Q5ieYF30e4sX_gzyHk1Lg8cH2LAgVxUjGoanCBbeCZtzRK9jxH6XSHP7Bw3EWJH-OaKyJSs9xSybuC1PEpjpqnEHOD2IdIieeHOSIlgscdSOoozryDj_y4olwfehj5MULV9nRF7pvPCiX43IM5z--5xA1Hb02Mbhy1Iog6seP88GP7zA__EH34QanhbihNTqaKlMTIj8au6iF_jD7ocelhUi-qYhftMphbTV-3BqJXMc7iDV-_DhEHedw4lSP57CkFDp8OMeVHP_hHp-PG47x48OBH-Zy9HgGjdqJu8ipYZOPHidODr8RXoMWMSOOm0LYHl-Pyc7xD5dkoXB-MA85CseFG40mI8d56DYYH0djT-iPH4f24ys-HG80nIfx6fiOH4d3XD8Ox8ePU3egH26PE8d3QMyP_pDLo5qSZYZ_HMcX1YK-4HrxPeBx4luPHyf8oXuG6zjRZ5Vgi0eORz1aon4Gm7h--DmcrfDRh_jxI_yFK0vwEHrg08JFHN_h44SOP2iVHF9QMoePS4J8vDvM5dB5nBn44kvwgXp2XEpWvMcD_7jIQz98GTmJh8ej9LBe4gx--Dv6BPmDvob4Hh8tWBaO_9AvAOGsc0o465xw1jnhrHPWOeucdU4Jp4RTwinhlHBKOCWcEs464YRzxjphhbPCKeGscFY4K5xVVjjrhHBWWeGsE1Y464Rw1gnhrBPCWSeUcEoJ47wRTijhlBDOKeGcU8Ip55RwyjknnFPCKeesc9Y55Zx1TglnnTPSOmacMMIqIZRTwinhlHBKOCucEsw765S1WjgrnBXOCmeFQ9ZZJYABWIhlRCMKCyqAQAAQppjizgknGDBOOCOAcxIBBpBASgnkkBFGSEEsMgQRp6xgDDClHEJACIMUEMIwgwQ1xCqghAMOAGSQMwoZAAxQQBhhlLLIOmGFEMAYZoRDgFnnBGAIOYEcAs4Zh4CwQjqgnBTIOaGcNVYAK5RTACmFBFNKOSUMUM4YIJCkQlAhnFLIKGOdEEYgphQCAEBCmCAKGGAEVYAx4ZRizCBpgHDIEAMBQkpAIJgwDABCDDMAAuMQQYAJAhQRACAAiARWGSCVgIwABZA1EggjiAHAEKAABEA5wgAGCFHCgISGAuIcIwAhRgAjDFCmLAMMGGakIsYAhCADAjGjrDFOCSAQMwwpAxBxBCiHEBKMW-QAEVQBpChBTIBgDFFQOIKGA4QiIQIQTjhCJEACCOQkBUogByAAjElnCVJAAGUcQAgAIgYVSAlkmBGKOqCcEkQxAxQhRCBACGGACAaYMkIpZJgCjBjnoEIAEAAYsgAoSQA";

$hash = hash('sha256', $acoustid . $fingerprint);

echo $hash."---";
echo strlen($hash); // 64 Zeichen

echo "time: " . sprintf('%.3f' , (microtime(true) - $start_time));

?>
