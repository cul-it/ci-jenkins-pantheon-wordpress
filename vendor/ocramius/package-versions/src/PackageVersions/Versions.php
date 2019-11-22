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
  'cul-it/ares_wordpress' => 'v1.0.4@21d13559f37743d53714b0b357972050adf5c87e',
  'cul-it/culu' => 'v1.0.26@a78bea8b2dfbed9f63c29007720c54642caa7b58',
  'cul-it/draw-attention-pro' => 'v1.9.12.1@0492fa0f7cb38c50e4d2ed9a7f7541d5c4885779',
  'cul-it/facetwp' => 'v3.4.0.1@d8f775a95c1b910a486634367bbd8e4ad7507da3',
  'cul-it/facetwp-conditional-logic' => 'v1.3.0.1@a46ca59e97cd2f28ee80fa4bfa09cf9ea4651f57',
  'cul-it/wp-libcal-hours' => 'v1.0.8@9d78e4b9a6922fc4ba6bbe4e7152d8ade3b9e0c1',
  'cul-it/wp-rss-aggregator' => 'v4.15.2.1@eab095f7a189ba6a197f2a483dc9823ed60d3abe',
  'cul-it/wp-rss-categories' => 'v1.3.3.2@caf7fa1747a7cd36daa87c3f60829a2e07b5a93c',
  'cul-it/wp-rss-keyword-filtering' => 'v1.6.3.1@353229555e768e7debf3b9b236a3af0950e87e66',
  'cul-it/wp-rss-templates' => '0.1.1@995b761fd41770e01b0ce4420f1f0ee1c6dfe4ba',
  'cul-it/wp-ultimate-csv-importer-pro' => 'v6.0.1.10@59fc98b68c68fb7c311bd9e1893908db3daf61c9',
  'cweagans/composer-patches' => '1.6.7@2e6f72a2ad8d59cd7e2b729f218bf42adb14f590',
  'gettext/gettext' => 'v4.8.1@494237c1315438e13777f327271a0dd99fbb6e09',
  'gettext/languages' => '2.6.0@38ea0482f649e0802e475f0ed19fa993bcb7a618',
  'guzzlehttp/guzzle' => '6.3.3@407b0cb880ace85c9b63c5f9551db498cb2d50ba',
  'guzzlehttp/promises' => 'v1.3.1@a59da6cf61d80060647ff4d3eb2c03a2bc694646',
  'guzzlehttp/psr7' => '1.6.1@239400de7a173fe9901b9ac7c06497751f00727a',
  'johnpbloch/wordpress-core-installer' => '0.2.1@a04c2c383ef13aae077f36799ed2eafdebd618d2',
  'onelogin/php-saml' => '3.4.0@8ac96798a6e85627d7fae5a08b1a56487eea82dc',
  'pantheon-systems/quicksilver-pushback' => '1.0.1@32c65effd6802bdf829f1c68fb75ade2bd5894a0',
  'pantheon-systems/wordpress-composer' => '5.3@3f0b53bec673fe6746be1b38399c2bfdb7d3c7f0',
  'pantheon-systems/wp-saml-auth' => 'v0.8.0@ddbf287d54d1dd09feb2a9bd56ccd01c09e452b0',
  'phpfastcache/riak-client' => '3.4.3@d771f75d16196006604a30bb15adc1c6a9b0fcc9',
  'phpmailer/phpmailer' => 'v6.1.3@a25ae38e03de4ee4031725498a600012364787c7',
  'phpoption/phpoption' => '1.5.2@2ba2586380f8d2b44ad1b9feb61c371020b27793',
  'psr/container' => '1.0.0@b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.2@446d54b4cb6bf489fc9d75f55843658e6f25d801',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'robrichards/xmlseclibs' => '3.0.4@0a53d3c3aa87564910cae4ed01416441d3ae0db5',
  'roots/wp-password-bcrypt' => '1.0.0@5cecd2e98ccc3193443cc5c5db9b3bc7abed5ffa',
  'rvtraveller/qs-composer-installer' => '1.1@20d6f4397e4b77599646767ad030092a60f5f92a',
  'simplesamlphp/composer-module-installer' => 'v1.1.6@b70414a2112fe49e97a7eddd747657bd8bc38ef0',
  'simplesamlphp/saml2' => 'v4.1.1@2f753c70e3dc5e958e9b391daf70b7ec9100db3e',
  'simplesamlphp/simplesamlphp' => 'v1.18.0@ccb6c43bb856dab2b71b2fcaa4b55c22084b689e',
  'simplesamlphp/simplesamlphp-module-adfs' => 'v0.9.4@edb1bbc59734875e33990c976917594b55fa412d',
  'simplesamlphp/simplesamlphp-module-authcrypt' => 'v0.9.0@46feb5ef2a24bf46870e851318d7152c7515388e',
  'simplesamlphp/simplesamlphp-module-authfacebook' => 'v0.9.1@20c8153efc06e9e736046962489328e225059f19',
  'simplesamlphp/simplesamlphp-module-authorize' => 'v0.9.0@470470373f352d0682b7e57ff41626fd0453c7c3',
  'simplesamlphp/simplesamlphp-module-authtwitter' => 'v0.9.0@7af50a5ff86ce18b00524f770ba31825aa0f3367',
  'simplesamlphp/simplesamlphp-module-authwindowslive' => 'v0.9.0@62db69c5c8b8b625a4940e6dca7a8da7e4c237af',
  'simplesamlphp/simplesamlphp-module-authx509' => 'v0.9.0@eb680d69fd0f31e7e52b40713dbaaaa3d730ba88',
  'simplesamlphp/simplesamlphp-module-authyubikey' => 'v0.9.0@a9e4710befefc496d5c6b1bf67871c9d8c41363f',
  'simplesamlphp/simplesamlphp-module-cas' => 'v0.9.0@5a26f3109fc08bb594645cd1136ef1e034ba02a1',
  'simplesamlphp/simplesamlphp-module-cdc' => 'v0.9.0@c7f566051ec69d6ac87dea3138d04ad9ea14bd20',
  'simplesamlphp/simplesamlphp-module-consent' => 'v0.9.3@8dc75896a6630f78c4521f098dd2a1818a1bba0a',
  'simplesamlphp/simplesamlphp-module-consentadmin' => 'v0.9.0@849da4adb293e3a3cb93483fa755a1c6ef9dc5b0',
  'simplesamlphp/simplesamlphp-module-discopower' => 'v0.9.0@61d73770724774635a02cee9cdff6cbe4192afaf',
  'simplesamlphp/simplesamlphp-module-exampleattributeserver' => 'v1.0.0@63e0323e81c32bc3c9eaa01ea45194bb10153708',
  'simplesamlphp/simplesamlphp-module-expirycheck' => 'v0.9.0@fb3fb0d45c25cf67aae2c9a6fbb1c2ed9e59c6ec',
  'simplesamlphp/simplesamlphp-module-ldap' => 'v0.9.3@e783f9ed29e4e324e402546c8fe90e0e6d87ba69',
  'simplesamlphp/simplesamlphp-module-memcachemonitor' => 'v0.9.0@6eef1e86ad7d2c9325b37c5004813947ace90c50',
  'simplesamlphp/simplesamlphp-module-memcookie' => 'v1.2.2@39535304e8d464b7baa1e82cb441fa432947ff57',
  'simplesamlphp/simplesamlphp-module-metarefresh' => 'v0.9.1@567ef07a0908745810180ee4cd237a996e0d76ba',
  'simplesamlphp/simplesamlphp-module-negotiate' => 'v0.9.4@08998d51b38592c5e90bfdcb61c91a8255b35f5f',
  'simplesamlphp/simplesamlphp-module-oauth' => 'v0.9.0@a980d8974f85f410fe4c14c8b3c5446c9c89ea4e',
  'simplesamlphp/simplesamlphp-module-preprodwarning' => 'v0.9.0@b63b256082e482e7f1a449d02f82c9326e1857fc',
  'simplesamlphp/simplesamlphp-module-radius' => 'v0.9.3@36bd0f39f9a13f7eb96ead97c97c3634aa1c3f2d',
  'simplesamlphp/simplesamlphp-module-riak' => 'v0.9.0@c45b3c6cf2b2dfd6d32f4f722e73c67d1c602af9',
  'simplesamlphp/simplesamlphp-module-sanitycheck' => 'v0.9.0@1efbeab5df8e616522690bcc6e49a99436a748b9',
  'simplesamlphp/simplesamlphp-module-smartattributes' => 'v0.9.0@8fa9aaa550129f233944dd024eac95179cf52a38',
  'simplesamlphp/simplesamlphp-module-sqlauth' => 'v0.9.0@c0201d6f894ef3b28b9e5855b92168a4fa7fd026',
  'simplesamlphp/simplesamlphp-module-statistics' => 'v0.9.3@eb6dfe18e81034ab66998836b58f69cf42a6dd4f',
  'simplesamlphp/twig-configurable-i18n' => 'v2.2@b036c134157ce40ed66da2fc9d01f63e3b1d3abd',
  'symfony/config' => 'v4.4.0@f08e1c48e1f05d07c32f2d8599ed539e62105beb',
  'symfony/debug' => 'v4.4.0@b24b791f817116b29e52a63e8544884cf9a40757',
  'symfony/dependency-injection' => 'v4.4.0@d4439814135ed1343c93bde998b7792af8852e41',
  'symfony/error-handler' => 'v4.4.0@e1acb58dc6a8722617fe56565f742bcf7e8744bf',
  'symfony/event-dispatcher' => 'v4.4.0@ab1c43e17fff802bef0a898f3bc088ac33b8e0e1',
  'symfony/event-dispatcher-contracts' => 'v1.1.7@c43ab685673fb6c8d84220c77897b1d6cdbe1d18',
  'symfony/filesystem' => 'v5.0.0@0bf75c37a71ff41f718b14b9f5a0a7d6a7dd9b84',
  'symfony/http-foundation' => 'v4.4.0@502040dd2b0cf0a292defeb6145f4d7a4753c99c',
  'symfony/http-kernel' => 'v4.4.0@5a5e7237d928aa98ff8952050cbbf0135899b6b0',
  'symfony/mime' => 'v5.0.0@76f3c09b7382bf979af7bcd8e6f8033f1324285e',
  'symfony/polyfill-ctype' => 'v1.12.0@550ebaac289296ce228a706d0867afc34687e3f4',
  'symfony/polyfill-intl-idn' => 'v1.12.0@6af626ae6fa37d396dc90a399c0ff08e5cfc45b2',
  'symfony/polyfill-mbstring' => 'v1.12.0@b42a2f66e8f1b15ccf25652c3424265923eb4f17',
  'symfony/polyfill-php72' => 'v1.12.0@04ce3335667451138df4307d6a9b61565560199e',
  'symfony/polyfill-php73' => 'v1.12.0@2ceb49eaccb9352bff54d22570276bb75ba4a188',
  'symfony/routing' => 'v4.4.0@cf6d72cf0348775f5243b8389169a7096221ea40',
  'symfony/service-contracts' => 'v2.0.0@9d99e1556417bf227a62e14856d630672bf10eaf',
  'symfony/var-dumper' => 'v5.0.0@956b8b6e4c52186695f592286414601abfcec284',
  'symfony/yaml' => 'v4.4.0@76de473358fe802578a415d5bb43c296cf09d211',
  'twig/extensions' => 'v1.5.4@57873c8b0c1be51caa47df2cdb824490beb16202',
  'twig/twig' => 'v2.12.2@d761fd1f1c6b867ae09a7d8119a6d95d06dc44ed',
  'vlucas/phpdotenv' => 'v3.3.3@dbcc609971dd9b55f48b8008b553d79fd372ddde',
  'webmozart/assert' => '1.5.0@88e6d84706d09a236046d686bbea96f07b3a34f4',
  'whitehat101/apr1-md5' => 'v1.0.0@8b261c9fc0481b4e9fa9d01c6ca70867b5d5e819',
  'wpackagist-plugin/acf-better-search' => '3.4.3@trunk',
  'wpackagist-plugin/advanced-access-manager' => '6.0.1@tags/6.0.1',
  'wpackagist-plugin/akismet' => '4.1.3@tags/4.1.3',
  'wpackagist-plugin/all-in-one-wp-migration' => '7.10@tags/7.10',
  'wpackagist-plugin/better-font-awesome' => '1.7.1@tags/1.7.1',
  'wpackagist-plugin/capability-manager-enhanced' => '1.8.1@tags/1.8.1',
  'wpackagist-plugin/classic-editor' => '1.5@tags/1.5',
  'wpackagist-plugin/code-snippets' => '2.13.3@trunk',
  'wpackagist-plugin/coming-soon' => '5.1.0@tags/5.1.0',
  'wpackagist-plugin/custom-post-type-ui' => '1.7.1@tags/1.7.1',
  'wpackagist-plugin/google-analytics-dashboard-for-wp' => '5.3.9@tags/5.3.9',
  'wpackagist-plugin/granular-controls-for-elementor' => '1.0.5@tags/1.0.5',
  'wpackagist-plugin/intuitive-custom-post-order' => '3.1.2@tags/3.1.2',
  'wpackagist-plugin/json-content-importer' => '1.3.5@tags/1.3.5',
  'wpackagist-plugin/kirki' => '3.0.45@tags/3.0.45',
  'wpackagist-plugin/pantheon-advanced-page-cache' => '0.3.1@tags/0.3.1',
  'wpackagist-plugin/simple-social-icons' => '3.0.1@tags/3.0.1',
  'wpackagist-plugin/siteimprove' => '1.1.0@tags/1.1.0',
  'wpackagist-plugin/wp-cfm' => '1.6@tags/1.6',
  'wpackagist-plugin/wp-mail-smtp' => '1.7.1@tags/1.7.1',
  'wpackagist-plugin/wp-native-php-sessions' => '0.9.0@tags/0.9.0',
  'wpackagist-plugin/wp-rss-aggregator' => '4.16@tags/4.16',
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
  'myclabs/deep-copy' => '1.9.3@007c053ae6f31bba39dfa19a7726f56e9763bbea',
  'ocramius/package-versions' => '1.5.1@1d32342b8c1eb27353c8887c366147b4c2da673c',
  'ocramius/proxy-manager' => '2.0.4@a55d08229f4f614bf335759ed0cf63378feeb2e6',
  'paulgibbs/behat-wordpress-extension' => 'v0.8.0@04aaa4e2262f3678c4cf76829331b99051bf0ff9',
  'phar-io/manifest' => '1.0.1@2df402786ab5368a0169091f61a7c1e0eb6852d0',
  'phar-io/version' => '1.0.1@a70c0ced4be299a63d32fa96d9281d03e94041df',
  'phpdocumentor/reflection-common' => '2.0.0@63a995caa1ca9e5590304cd845c15ad6d482a62a',
  'phpdocumentor/reflection-docblock' => '5.0.0-alpha5@8fcadfe5f85c38705151c9ab23b4781f23e6a70e',
  'phpdocumentor/type-resolver' => '0.7.2@2e32a6d48972b2c1976ed5d8967145b6cec4a4a9',
  'phpspec/prophecy' => '1.9.0@f6811d96d97bdf400077a0cc100ae56aa32b9203',
  'phpunit/php-code-coverage' => '5.3.2@c89677919c5dd6d3b3852f230a663118762218ac',
  'phpunit/php-file-iterator' => '1.4.5@730b01bc3e867237eaac355e06a36b85dd93a8b4',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '1.0.9@3dcf38ca72b158baf0bc245e9184d3fdffa9c46f',
  'phpunit/php-token-stream' => '2.0.2@791198a2c6254db10131eecfe8c06670700904db',
  'phpunit/phpunit' => '6.5.14@bac23fe7ff13dbdb461481f706f0e9fe746334b7',
  'phpunit/phpunit-mock-objects' => '5.0.10@cd1cf05c553ecfec36b170070573e540b67d3f1f',
  'roave/security-advisories' => 'dev-master@40fb2c205dd261ab6bb42ec29545934f0db7026f',
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
  'symfony/browser-kit' => 'v4.4.0@e19e465c055137938afd40cfddd687e7511bbbf0',
  'symfony/class-loader' => 'v3.4.35@e212b06996819a2bce026a63da03b7182d05a690',
  'symfony/console' => 'v4.4.0@35d9077f495c6d184d9930f7a7ecbd1ad13c7ab8',
  'symfony/css-selector' => 'v3.4.35@f819f71ae3ba6f396b4c015bd5895de7d2f1f85f',
  'symfony/dom-crawler' => 'v4.4.0@36bbcab9369fc2f583220890efd43bf262d563fd',
  'symfony/translation' => 'v4.4.0@897fb68ee7933372517b551d6f08c6d4bb0b8c40',
  'symfony/translation-contracts' => 'v2.0.0@8feb81e6bb1a42d6a3b1429c751d291eb6d05297',
  'theseer/tokenizer' => '1.1.3@11336f6f84e16a720dae9d8e6ed5019efa85a0f9',
  'wp-coding-standards/wpcs' => 'dev-master@f90e8692ce97b693633db7ab20bfa78d930f536a',
  'zendframework/zend-code' => '3.1.0@2899c17f83a7207f2d7f53ec2f421204d3beea27',
  'zendframework/zend-eventmanager' => '3.2.1@a5e2583a211f73604691586b8406ff7296a946dd',
  'pantheon-systems/example-wordpress-composer' => 'v1.2.31@f5ff413ad76be1f025dd8cd0c5865080664f6a50',
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
