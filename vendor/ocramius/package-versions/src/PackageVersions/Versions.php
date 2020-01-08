<?php

declare(strict_types=1);

namespace PackageVersions;

/**
 * This class is generated by ocramius/package-versions, specifically by
 * @see \PackageVersions\Installer
 *
 * This file is overwritten at every run of `composer install` or `composer update`.
 */
final class Versions
{
    public const ROOT_PACKAGE_NAME = 'pantheon-systems/example-wordpress-composer';
    /**
     * Array of all available composer packages.
     * Dont read this array from your calling code, but use the \PackageVersions\Versions::getVersion() method instead.
     *
     * @var array<string, string>
     * @internal
     */
    public const VERSIONS          = array (
  'a5hleyrich/wp-background-processing' => '1.0.1@1f070aab5058dbaf45d5435a343033ddd8a641b1',
  'composer/installers' => 'v1.7.0@141b272484481432cda342727a427dc1e206bfa0',
  'cul-it/advanced-custom-fields-pro' => 'v5.8.7@8e7154a17c8bda8ca5a6fa783ab490f617d078b7',
  'cul-it/ares_wordpress' => 'v1.0.6@6b7a5748d5d0d7da8d7d2ffaf996f6c33740a2b9',
  'cul-it/culu' => 'v1.0.31@30b54d1e422e421a4f6787fb4a412a455bc123a7',
  'cul-it/draw-attention-pro' => 'v1.9.12.1@0492fa0f7cb38c50e4d2ed9a7f7541d5c4885779',
  'cul-it/elementor-pro' => 'v2.7.3@f13946e92c06f9caafcbb3235965ada06f38b8a2',
  'cul-it/facetwp' => 'v3.4.0.1@d8f775a95c1b910a486634367bbd8e4ad7507da3',
  'cul-it/facetwp-conditional-logic' => 'v1.3.0.1@a46ca59e97cd2f28ee80fa4bfa09cf9ea4651f57',
  'cul-it/wp-libcal-hours' => 'v1.0.11@619400bd60615b02790eae63a544288ca20d6ca0',
  'cul-it/wp-rss-aggregator' => 'v4.15.2.1@eab095f7a189ba6a197f2a483dc9823ed60d3abe',
  'cul-it/wp-rss-categories' => 'v1.3.3.2@caf7fa1747a7cd36daa87c3f60829a2e07b5a93c',
  'cul-it/wp-rss-keyword-filtering' => 'v1.6.3.1@353229555e768e7debf3b9b236a3af0950e87e66',
  'cul-it/wp-rss-templates' => '0.1.1@995b761fd41770e01b0ce4420f1f0ee1c6dfe4ba',
  'cul-it/wp-ultimate-csv-importer-pro' => 'v6.0.1.10@59fc98b68c68fb7c311bd9e1893908db3daf61c9',
  'cweagans/composer-patches' => '1.6.7@2e6f72a2ad8d59cd7e2b729f218bf42adb14f590',
  'gettext/gettext' => 'v4.8.2@e474f872f2c8636cf53fd283ec4ce1218f3d236a',
  'gettext/languages' => '2.6.0@38ea0482f649e0802e475f0ed19fa993bcb7a618',
  'guzzlehttp/guzzle' => '6.3.3@407b0cb880ace85c9b63c5f9551db498cb2d50ba',
  'guzzlehttp/promises' => 'v1.3.1@a59da6cf61d80060647ff4d3eb2c03a2bc694646',
  'guzzlehttp/psr7' => '1.6.1@239400de7a173fe9901b9ac7c06497751f00727a',
  'johnpbloch/wordpress-core-installer' => '0.2.1@a04c2c383ef13aae077f36799ed2eafdebd618d2',
  'onelogin/php-saml' => '3.4.1@5fbf3486704ac9835b68184023ab54862c95f213',
  'pantheon-systems/quicksilver-pushback' => '1.0.1@32c65effd6802bdf829f1c68fb75ade2bd5894a0',
  'pantheon-systems/wordpress-composer' => '5.3.2@b44925dcc067897febb0a017d0011c67ab1f885a',
  'pantheon-systems/wp-saml-auth' => 'v0.8.1@127e6f75f7e26f5b65d29fdef7309c2b4e80687b',
  'phpfastcache/riak-client' => '3.4.3@d771f75d16196006604a30bb15adc1c6a9b0fcc9',
  'phpmailer/phpmailer' => 'v6.1.4@c5e61d0729507049cec9673aa1a679f9adefd683',
  'phpoption/phpoption' => '1.7.2@77f7c4d2e65413aff5b5a8cc8b3caf7a28d81959',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.2@446d54b4cb6bf489fc9d75f55843658e6f25d801',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'robrichards/xmlseclibs' => '3.0.4@0a53d3c3aa87564910cae4ed01416441d3ae0db5',
  'roots/wp-password-bcrypt' => '1.0.0@5cecd2e98ccc3193443cc5c5db9b3bc7abed5ffa',
  'rvtraveller/qs-composer-installer' => '1.1@20d6f4397e4b77599646767ad030092a60f5f92a',
  'simplesamlphp/composer-module-installer' => 'v1.1.6@b70414a2112fe49e97a7eddd747657bd8bc38ef0',
  'simplesamlphp/saml2' => 'v4.1.3@4ef98dcf868312d11ea6c1acbebfe80483970d7f',
  'simplesamlphp/simplesamlphp' => 'v1.18.2@22394b68fd41e2e5399c60e69874e4fc8f845eab',
  'simplesamlphp/simplesamlphp-module-adfs' => 'v0.9.5@3ac7d15825e609152ca04faceea80ee0db3afcb1',
  'simplesamlphp/simplesamlphp-module-authcrypt' => 'v0.9.1@cc2950cf710933063192e883ba2804321b8af6db',
  'simplesamlphp/simplesamlphp-module-authfacebook' => 'v0.9.2@661cc25ac21ea422552a2394ea35ce9e8873ce39',
  'simplesamlphp/simplesamlphp-module-authorize' => 'v0.9.1@648954077e1cb8dd7b694eebfca7c619c401145b',
  'simplesamlphp/simplesamlphp-module-authtwitter' => 'v0.9.1@29a15e58061222632fea9eb2c807aef5e2c0d54a',
  'simplesamlphp/simplesamlphp-module-authwindowslive' => 'v0.9.1@f40aecec6c0adaedb6693309840c98cec783876e',
  'simplesamlphp/simplesamlphp-module-authx509' => 'v0.9.1@32f4fb3822b4325fdccbff824996e82fa1042e0d',
  'simplesamlphp/simplesamlphp-module-authyubikey' => 'v0.9.1@8c27bfeb4981d2e6fa40a831e945f40c5a4ad3d2',
  'simplesamlphp/simplesamlphp-module-cas' => 'v0.9.1@63b72e4600550c507cdfc32fdd208ad59a64321e',
  'simplesamlphp/simplesamlphp-module-cdc' => 'v0.9.1@16a5bfac7299e04e5feb472af328e07598708166',
  'simplesamlphp/simplesamlphp-module-consent' => 'v0.9.5@700f4c6abfdcd7ebd75a0c405d386758eff6e65e',
  'simplesamlphp/simplesamlphp-module-consentadmin' => 'v0.9.1@466e8d0d751f0080162d78e63ab2e125b24d17a1',
  'simplesamlphp/simplesamlphp-module-discopower' => 'v0.9.3@c892926e8186d0a2c638f7032dfc30540c1f92fb',
  'simplesamlphp/simplesamlphp-module-exampleattributeserver' => 'v1.0.0@63e0323e81c32bc3c9eaa01ea45194bb10153708',
  'simplesamlphp/simplesamlphp-module-expirycheck' => 'v0.9.3@59c59cdf87e2679257b46c07bb4c27666a11cc20',
  'simplesamlphp/simplesamlphp-module-ldap' => 'v0.9.4@21301b3fcd7bc6147acdc673ada9e17e5282e908',
  'simplesamlphp/simplesamlphp-module-memcachemonitor' => 'v0.9.1@0e08e87707cd7b1fb91bbcf65cc454d8849571b0',
  'simplesamlphp/simplesamlphp-module-memcookie' => 'v1.2.2@39535304e8d464b7baa1e82cb441fa432947ff57',
  'simplesamlphp/simplesamlphp-module-metarefresh' => 'v0.9.4@478e52f33c725aea10b493d574b4b42b62c5dbed',
  'simplesamlphp/simplesamlphp-module-negotiate' => 'v0.9.4@08998d51b38592c5e90bfdcb61c91a8255b35f5f',
  'simplesamlphp/simplesamlphp-module-oauth' => 'v0.9.1@17450420b5d4c1810055b8ab655cc4d045a0c477',
  'simplesamlphp/simplesamlphp-module-preprodwarning' => 'v0.9.1@925ef60b51a7230286b390c0abc0e815d8b9768e',
  'simplesamlphp/simplesamlphp-module-radius' => 'v0.9.3@36bd0f39f9a13f7eb96ead97c97c3634aa1c3f2d',
  'simplesamlphp/simplesamlphp-module-riak' => 'v0.9.1@c1a9d9545cb4e05b9205b34624850bb777aca991',
  'simplesamlphp/simplesamlphp-module-sanitycheck' => 'v0.9.0@1efbeab5df8e616522690bcc6e49a99436a748b9',
  'simplesamlphp/simplesamlphp-module-smartattributes' => 'v0.9.1@b45d3ecd916e359a9cae05f9ae9df09b5c42f4e6',
  'simplesamlphp/simplesamlphp-module-sqlauth' => 'v0.9.1@31bce8763ad97f4b4473e4ad4a5a96ddc136ef6b',
  'simplesamlphp/simplesamlphp-module-statistics' => 'v0.9.4@1bb1e46921d8dc84707bc9cd3c307c8abd723ac7',
  'simplesamlphp/twig-configurable-i18n' => 'v2.2@b036c134157ce40ed66da2fc9d01f63e3b1d3abd',
  'symfony/config' => 'v4.4.2@6911d432edd5b50822986604fd5a5be3af856d30',
  'symfony/debug' => 'v4.4.2@5c4c1db977dc70bb3250e1308d3e8c6341aa38f5',
  'symfony/dependency-injection' => 'v4.4.2@79b0358207a3571cc3af02a57d0321927921f539',
  'symfony/error-handler' => 'v4.4.2@6d7d7712a6ff5215ec26215672293b154f1db8c1',
  'symfony/event-dispatcher' => 'v4.4.2@b3c3068a72623287550fe20b84a2b01dcba2686f',
  'symfony/event-dispatcher-contracts' => 'v1.1.7@c43ab685673fb6c8d84220c77897b1d6cdbe1d18',
  'symfony/filesystem' => 'v5.0.2@1d71f670bc5a07b9ccc97dc44f932177a322d4e6',
  'symfony/http-foundation' => 'v4.4.2@fcae1cff5b57b2a9c3aabefeb1527678705ddb62',
  'symfony/http-kernel' => 'v4.4.2@fe310d2e95cd4c356836c8ecb0895a46d97fede2',
  'symfony/mime' => 'v5.0.2@0e6a4ced216e49d457eddcefb61132173a876d79',
  'symfony/polyfill-ctype' => 'v1.13.1@f8f0b461be3385e56d6de3dbb5a0df24c0c275e3',
  'symfony/polyfill-intl-idn' => 'v1.13.1@6f9c239e61e1b0c9229a28ff89a812dc449c3d46',
  'symfony/polyfill-mbstring' => 'v1.13.1@7b4aab9743c30be783b73de055d24a39cf4b954f',
  'symfony/polyfill-php72' => 'v1.13.1@66fea50f6cb37a35eea048d75a7d99a45b586038',
  'symfony/polyfill-php73' => 'v1.13.1@4b0e2222c55a25b4541305a053013d5647d3a25f',
  'symfony/routing' => 'v4.4.2@628bcafae1b2043969378dcfbf9c196539a38722',
  'symfony/service-contracts' => 'v2.0.1@144c5e51266b281231e947b51223ba14acf1a749',
  'symfony/var-dumper' => 'v5.0.2@d7bc61d5d335fa9b1b91e14bb16861e8ca50f53a',
  'symfony/yaml' => 'v4.4.2@a08832b974dd5fafe3085a66d41fe4c84bb2628c',
  'twig/extensions' => 'v1.5.4@57873c8b0c1be51caa47df2cdb824490beb16202',
  'twig/twig' => 'v2.12.3@97b6311585cae66a26833b14b33785f5797f7d39',
  'vlucas/phpdotenv' => 'v3.3.3@dbcc609971dd9b55f48b8008b553d79fd372ddde',
  'webmozart/assert' => '1.5.0@88e6d84706d09a236046d686bbea96f07b3a34f4',
  'whitehat101/apr1-md5' => 'v1.0.0@8b261c9fc0481b4e9fa9d01c6ca70867b5d5e819',
  'wpackagist-plugin/acf-better-search' => '3.4.3@trunk',
  'wpackagist-plugin/advanced-access-manager' => '6.2.1@tags/6.2.1',
  'wpackagist-plugin/akismet' => '4.1.3@tags/4.1.3',
  'wpackagist-plugin/all-in-one-wp-migration' => '7.14@tags/7.14',
  'wpackagist-plugin/better-font-awesome' => '1.7.1@tags/1.7.1',
  'wpackagist-plugin/capability-manager-enhanced' => '1.8.1@tags/1.8.1',
  'wpackagist-plugin/classic-editor' => '1.5@tags/1.5',
  'wpackagist-plugin/code-snippets' => '2.13.3@trunk',
  'wpackagist-plugin/coming-soon' => '5.1.0@tags/5.1.0',
  'wpackagist-plugin/custom-icons-for-elementor' => '0.3.1@tags/0.3.1',
  'wpackagist-plugin/custom-post-type-ui' => '1.7.1@tags/1.7.1',
  'wpackagist-plugin/elementor' => '2.8.3@tags/2.8.3',
  'wpackagist-plugin/google-analytics-dashboard-for-wp' => '5.3.10@tags/5.3.10',
  'wpackagist-plugin/granular-controls-for-elementor' => '1.0.5@tags/1.0.5',
  'wpackagist-plugin/intuitive-custom-post-order' => '3.1.2@tags/3.1.2',
  'wpackagist-plugin/json-content-importer' => '1.3.6@tags/1.3.6',
  'wpackagist-plugin/kirki' => '3.0.45@tags/3.0.45',
  'wpackagist-plugin/pantheon-advanced-page-cache' => '0.3.1@tags/0.3.1',
  'wpackagist-plugin/simple-social-icons' => '3.0.1@tags/3.0.1',
  'wpackagist-plugin/siteimprove' => '1.1.0@tags/1.1.0',
  'wpackagist-plugin/wp-cfm' => '1.6@tags/1.6',
  'wpackagist-plugin/wp-mail-smtp' => '1.8.1@tags/1.8.1',
  'wpackagist-plugin/wp-native-php-sessions' => '0.9.0@tags/0.9.0',
  'wpackagist-plugin/wp-rss-aggregator' => '4.17.2@tags/4.17.2',
  'wpackagist-theme/twentynineteen' => '1.4@1.4',
  'antecedent/patchwork' => '2.0.9@cab3be4865e47f1dc447715e76c7b616e48b005d',
  'behat/behat' => 'v3.5.0@e4bce688be0c2029dc1700e46058d86428c63cab',
  'behat/gherkin' => 'v4.6.0@ab0a02ea14893860bca00f225f5621d351a3ad07',
  'behat/mink' => 'v1.7.1@e6930b9c74693dff7f4e58577e1b1743399f3ff9',
  'behat/mink-browserkit-driver' => '1.3.3@1b9a7ce903cfdaaec5fb32bfdbb26118343662eb',
  'behat/mink-extension' => '2.3.1@80f7849ba53867181b7e412df9210e12fba50177',
  'behat/mink-goutte-driver' => 'v1.2.1@8b9ad6d2d95bc70b840d15323365f52fcdaea6ca',
  'behat/mink-selenium2-driver' => 'v1.3.1@473a9f3ebe0c134ee1e623ce8a9c852832020288',
  'behat/transliterator' => 'v1.2.0@826ce7e9c2a6664c0d1f381cbb38b1fb80a7ee2c',
  'brain/monkey' => '1.5.0@44b2ea87147803227154c990fa01fd5e82a6bb61',
  'container-interop/container-interop' => '1.2.0@79cbf1341c22ec75643d841642dd5d6acd83bdb8',
  'doctrine/instantiator' => '1.3.0@ae466f726242e637cebdd526a7d991b9433bacf1',
  'fabpot/goutte' => 'v3.2.3@3f0eaf0a40181359470651f1565b3e07e3dd31b8',
  'hamcrest/hamcrest-php' => 'v1.2.2@b37020aa976fa52d3de9aa904aa2522dc518f79c',
  'instaclick/php-webdriver' => '1.4.6@bd9405077ca04129a73059a06873bedb5e138402',
  'mockery/mockery' => '0.9.11@be9bf28d8e57d67883cba9fcadfcff8caab667f8',
  'myclabs/deep-copy' => '1.9.4@579bb7356d91f9456ccd505f24ca8b667966a0a7',
  'ocramius/package-versions' => '1.5.1@1d32342b8c1eb27353c8887c366147b4c2da673c',
  'ocramius/proxy-manager' => '2.0.4@a55d08229f4f614bf335759ed0cf63378feeb2e6',
  'paulgibbs/behat-wordpress-extension' => 'v0.8.0@04aaa4e2262f3678c4cf76829331b99051bf0ff9',
  'phar-io/manifest' => '1.0.1@2df402786ab5368a0169091f61a7c1e0eb6852d0',
  'phar-io/version' => '1.0.1@a70c0ced4be299a63d32fa96d9281d03e94041df',
  'phpdocumentor/reflection-common' => '2.0.0@63a995caa1ca9e5590304cd845c15ad6d482a62a',
  'phpdocumentor/reflection-docblock' => '5.0.0-alpha7@c5a39003d5c0ef8e605d123d23355b1cb4112735',
  'phpdocumentor/type-resolver' => '1.0.1@2e32a6d48972b2c1976ed5d8967145b6cec4a4a9',
  'phpspec/prophecy' => '1.10.1@cbe1df668b3fe136bcc909126a0f529a78d4cbbc',
  'phpunit/php-code-coverage' => '5.3.2@c89677919c5dd6d3b3852f230a663118762218ac',
  'phpunit/php-file-iterator' => '1.4.5@730b01bc3e867237eaac355e06a36b85dd93a8b4',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '1.0.9@3dcf38ca72b158baf0bc245e9184d3fdffa9c46f',
  'phpunit/php-token-stream' => '2.0.2@791198a2c6254db10131eecfe8c06670700904db',
  'phpunit/phpunit' => '6.5.14@bac23fe7ff13dbdb461481f706f0e9fe746334b7',
  'phpunit/phpunit-mock-objects' => '5.0.10@cd1cf05c553ecfec36b170070573e540b67d3f1f',
  'roave/security-advisories' => 'dev-master@67ac6ea8f4a078c3c9b7aec5d7ae70f098c37389',
  'sebastian/code-unit-reverse-lookup' => '1.0.1@4419fcdb5eabb9caa61a27c7a1db532a6b55dd18',
  'sebastian/comparator' => '2.1.3@34369daee48eafb2651bea869b4b15d75ccc35f9',
  'sebastian/diff' => '2.0.1@347c1d8b49c5c3ee30c7040ea6fc446790e6bddd',
  'sebastian/environment' => '3.1.0@cd0871b3975fb7fc44d11314fd1ee20925fce4f5',
  'sebastian/exporter' => '3.1.2@68609e1261d215ea5b21b7987539cbfbe156ec3e',
  'sebastian/global-state' => '2.0.0@e8ba02eed7bbbb9e59e43dedd3dddeff4a56b0c4',
  'sebastian/object-enumerator' => '3.0.3@7cfd9e65d11ffb5af41198476395774d4c8a84c5',
  'sebastian/object-reflector' => '1.1.1@773f97c67f28de00d397be301821b06708fca0be',
  'sebastian/recursion-context' => '3.0.0@5b0cd723502bac3b006cbf3dbf7a1e3fcefe4fa8',
  'sebastian/resource-operations' => '1.0.0@ce990bb21759f94aeafd30209e8cfcdfa8bc3f52',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'sensiolabs/behat-page-object-extension' => 'v2.1.0@bd2a34221ba65ea8c86d8e693992d718de03dbae',
  'squizlabs/php_codesniffer' => '3.4.2@b8a7362af1cc1aadb5bd36c3defc4dda2cf5f0a8',
  'symfony/browser-kit' => 'v4.4.2@e19e465c055137938afd40cfddd687e7511bbbf0',
  'symfony/class-loader' => 'v3.4.36@e212b06996819a2bce026a63da03b7182d05a690',
  'symfony/console' => 'v4.4.2@82437719dab1e6bdd28726af14cb345c2ec816d0',
  'symfony/css-selector' => 'v3.4.36@f819f71ae3ba6f396b4c015bd5895de7d2f1f85f',
  'symfony/dom-crawler' => 'v4.4.2@36bbcab9369fc2f583220890efd43bf262d563fd',
  'symfony/translation' => 'v4.4.2@f7669f48a9633bf8139bc026c755e894b7206677',
  'symfony/translation-contracts' => 'v2.0.1@8cc682ac458d75557203b2f2f14b0b92e1c744ed',
  'theseer/tokenizer' => '1.1.3@11336f6f84e16a720dae9d8e6ed5019efa85a0f9',
  'wp-coding-standards/wpcs' => 'dev-master@f90e8692ce97b693633db7ab20bfa78d930f536a',
  'zendframework/zend-code' => '3.1.0@2899c17f83a7207f2d7f53ec2f421204d3beea27',
  'zendframework/zend-eventmanager' => '3.2.1@a5e2583a211f73604691586b8406ff7296a946dd',
  'pantheon-systems/example-wordpress-composer' => 'v1.2.38@f89fea2795c5bde2a445a1d5d59cc52e69fbdfff',
);

    private function __construct()
    {
    }

    /**
     * @throws \OutOfBoundsException If a version cannot be located.
     *
     * @psalm-param key-of<self::VERSIONS> $packageName
     */
    public static function getVersion(string $packageName) : string
    {
        if (isset(self::VERSIONS[$packageName])) {
            return self::VERSIONS[$packageName];
        }

        throw new \OutOfBoundsException(
            'Required package "' . $packageName . '" is not installed: check your ./vendor/composer/installed.json and/or ./composer.lock files'
        );
    }
}
