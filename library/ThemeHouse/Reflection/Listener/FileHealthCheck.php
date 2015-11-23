<?php

class ThemeHouse_Reflection_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/Reflection/Class.php' => 'da4d87dde94f110508d828efb79e93bb',
                'library/ThemeHouse/Reflection/ControllerAdmin/Abstract.php' => '7b4392e8082f2015f0d7113d1999142b',
                'library/ThemeHouse/Reflection/ControllerAdmin/MethodTemplate.php' => '7699b08c94d2bb7e957372a21ad94c5c',
                'library/ThemeHouse/Reflection/DataWriter/MethodTemplate.php' => 'be2fcdbe65597fb4745eb1392e723dd8',
                'library/ThemeHouse/Reflection/File.php' => '888098719fe64845950013039221bec8',
                'library/ThemeHouse/Reflection/Helper/Phrase.php' => '3dc4b9a97f384e5fe1a46b3097de3f2e',
                'library/ThemeHouse/Reflection/Helper/Template.php' => '89b1fd5daeb20e2fc90e9c724a5eea45',
                'library/ThemeHouse/Reflection/Install/Controller.php' => '490cceb5de32d8121e46211e335955ea',
                'library/ThemeHouse/Reflection/Listener/ContainerAdminParams.php' => 'c7774d9055778225a981faab69d8d5a0',
                'library/ThemeHouse/Reflection/Method.php' => '9c3e7dc729686ae9eaea1ce808e769b2',
                'library/ThemeHouse/Reflection/MethodTemplate/Abstract.php' => '66edcc5996dcf26ffa58342377b4fe0a',
                'library/ThemeHouse/Reflection/Model/MethodTemplate.php' => '387780fe4155d730c2745bac5db944b7',
                'library/ThemeHouse/Reflection/Model/Reflection.php' => '4878442690f6af77eb446768030d5be2',
                'library/ThemeHouse/Reflection/ReflectionHandler/Abstract.php' => '5f69bf18981864f8613e71482d1e61f7',
                'library/ThemeHouse/Reflection/Route/PrefixAdmin/MethodTemplates.php' => '908dbdfaa6420ee9982b7921b0e932c9',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/ContainerParams.php' => '43bf59af9f140f58f665be373ac07320',
                'library/ThemeHouse/Listener/ContainerParams/20150106.php' => '36fa6f85128a9a9b2b88210c9abe33bd',
                'library/ThemeHouse/PhpFile.php' => 'fa173b61cf237a57389d62f86d86fbaa',
                'library/ThemeHouse/PhpFile/Constant.php' => 'eee81a1ac0217ed1278ce790330dd43f',
                'library/ThemeHouse/PhpFile/Function.php' => '54b10d3a50700e37cf6fcd7bc4b8fff6',
                'library/ThemeHouse/PhpFile/Variable.php' => '4095622c1752593fd23725764fa243fc',
                'library/ThemeHouse/PhpFile/20150123.php' => '118900e176a0142f7d101aa02194d8ec',
                'library/ThemeHouse/PhpFile/Constant/20150106.php' => '13b0520a53a1852ac495331faa451c08',
                'library/ThemeHouse/PhpFile/Function/20150106.php' => '747a75d3d3f88ec7f6e4b4460c7fab6d',
                'library/ThemeHouse/PhpFile/Variable/20150106.php' => '6adb30e90581220a9f1f7d22e2f73f31',
            ));
    }
}