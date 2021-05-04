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
  '10up/distributor' => '1.6.2@ec7c44ba66cd36ea1258731834c5ae62dcc22843',
  'a5hleyrich/wp-background-processing' => '1.0.2@2cbee1abd1b49e1133cd8f611df4d4fc5a8b9800',
  'composer/installers' => 'v1.11.0@ae03311f45dfe194412081526be2e003960df74b',
  'cul-it/advanced-custom-fields-pro' => 'v5.9.4@a4d6bfbb8c0edbd501a60d6e83443b824c811d9f',
  'cul-it/ares_wordpress' => 'v1.0.7@734b57d70b9005345a938db30a7f3b5b338669c9',
  'cul-it/culu' => 'v1.5.4@4ca5adf7c273f9e5c98115c8608e5f55f482dc62',
  'cul-it/draw-attention-pro' => 'v1.9.12.1@0492fa0f7cb38c50e4d2ed9a7f7541d5c4885779',
  'cul-it/elementor-pro' => 'v3.2.1@c948497c4869ef2f6af837c96adc9a8160f1d9c9',
  'cul-it/facetwp' => 'v3.4.0.1@d8f775a95c1b910a486634367bbd8e4ad7507da3',
  'cul-it/facetwp-conditional-logic' => 'v1.3.0.1@a46ca59e97cd2f28ee80fa4bfa09cf9ea4651f57',
  'cul-it/wp-custom-loop-widget' => 'v1.2.4@17221ba2b8bb2849546e581c3785af14940b9fd3',
  'cul-it/wp-libcal-hours' => 'v2.1.4@e0af7d8322efcdc622a95c0102681271665f1f0a',
  'cul-it/wp-rss-aggregator' => 'v4.15.2.1@eab095f7a189ba6a197f2a483dc9823ed60d3abe',
  'cul-it/wp-rss-categories' => 'v1.3.3.2@caf7fa1747a7cd36daa87c3f60829a2e07b5a93c',
  'cul-it/wp-rss-keyword-filtering' => 'v1.6.3.1@353229555e768e7debf3b9b236a3af0950e87e66',
  'cul-it/wp-rss-templates' => 'v0.2@49c84700915c256c5f137f3dc84cbd186719bf41',
  'cul-it/wp-ultimate-csv-importer-pro' => 'v6.3.3.2@4a9afbbdbc97c02711f6fa828178a7fec1fcaa51',
  'cweagans/composer-patches' => '1.7.0@ae02121445ad75f4eaff800cc532b5e6233e2ddf',
  'georgestephanis/application-passwords' => '0.1.2@0eef095b4dc984c26ee8149c01a323be83da715a',
  'gettext/gettext' => 'v4.8.4@58bc0f7f37e78efb0f9758f93d4a0f669f0f84a1',
  'gettext/languages' => '2.6.0@38ea0482f649e0802e475f0ed19fa993bcb7a618',
  'guzzlehttp/guzzle' => '6.3.3@407b0cb880ace85c9b63c5f9551db498cb2d50ba',
  'guzzlehttp/promises' => '1.4.1@8e7d04f1f6450fef59366c399cfad4b9383aa30d',
  'guzzlehttp/psr7' => '1.8.2@dc960a912984efb74d0a90222870c72c87f10c91',
  'johnpbloch/wordpress-core-installer' => '0.2.1@a04c2c383ef13aae077f36799ed2eafdebd618d2',
  'onelogin/php-saml' => '3.6.1@a7328b11887660ad248ea10952dd67a5aa73ba3b',
  'pantheon-systems/quicksilver-pushback' => '1.0.1@32c65effd6802bdf829f1c68fb75ade2bd5894a0',
  'pantheon-systems/wordpress-composer' => '5.7.1@6753b97e893366bf906a0a9818f3b5b65edcbff8',
  'pantheon-systems/wp-saml-auth' => 'v1.2.2@ff8e0efd94c044b574d52f7acae2fd7403ffb6a4',
  'pear/archive_tar' => '1.4.13@2b87b41178cc6d4ad3cba678a46a1cae49786011',
  'pear/console_getopt' => 'v1.4.3@a41f8d3e668987609178c7c4a9fe48fecac53fa0',
  'pear/pear-core-minimal' => 'v1.10.10@625a3c429d9b2c1546438679074cac1b089116a7',
  'pear/pear_exception' => 'v1.0.2@b14fbe2ddb0b9f94f5b24cf08783d599f776fff0',
  'phpfastcache/riak-client' => '3.4.3@d771f75d16196006604a30bb15adc1c6a9b0fcc9',
  'phpmailer/phpmailer' => 'v6.4.1@9256f12d8fb0cd0500f93b19e18c356906cbed3d',
  'phpoption/phpoption' => '1.7.5@994ecccd8f3283ecf5ac33254543eb0ac946d525',
  'psr/container' => '1.1.1@8622567409010282b7aeebe4bb841fe98b58dcaf',
  'psr/http-message' => '1.0.1@f6561bf28d520154e4b0ec72be95418abe6d9363',
  'psr/log' => '1.1.4@d49695b909c3b7628b6289db5479a1c204601f11',
  'ralouphie/getallheaders' => '3.0.3@120b605dfeb996808c31b6477290a714d356e822',
  'robrichards/xmlseclibs' => '3.1.1@f8f19e58f26cdb42c54b214ff8a820760292f8df',
  'roots/wp-password-bcrypt' => '1.0.0@5cecd2e98ccc3193443cc5c5db9b3bc7abed5ffa',
  'rvtraveller/qs-composer-installer' => '1.1@20d6f4397e4b77599646767ad030092a60f5f92a',
  'simplesamlphp/composer-module-installer' => 'v1.1.8@45161b5406f3e9c82459d0f9a5a1dba064953cfa',
  'simplesamlphp/saml2' => 'v4.2.1@3bc980feb96ecf57898014c1bb5b26f0859f1316',
  'simplesamlphp/simplesamlphp' => 'v1.18.6@9fd1c3c5e5f9e6fed8de3c36e14ccdcce31e3dc7',
  'simplesamlphp/simplesamlphp-module-adfs' => 'v0.9.6@425e5ebbdd097c92fe5265a6b48d32a3095c7237',
  'simplesamlphp/simplesamlphp-module-authcrypt' => 'v0.9.3@9a2c1a761e2d94394a4f2d3499fd6f0853899530',
  'simplesamlphp/simplesamlphp-module-authfacebook' => 'v0.9.3@9152731e939ad4a49e0f06da5f0009ebde0d2b5c',
  'simplesamlphp/simplesamlphp-module-authorize' => 'v0.9.3@0593bfcb84fca9d9133f415246ab8ca51b412c92',
  'simplesamlphp/simplesamlphp-module-authtwitter' => 'v0.9.1@29a15e58061222632fea9eb2c807aef5e2c0d54a',
  'simplesamlphp/simplesamlphp-module-authwindowslive' => 'v0.9.1@f40aecec6c0adaedb6693309840c98cec783876e',
  'simplesamlphp/simplesamlphp-module-authx509' => 'v0.9.8@66525b1ec4145ec8d0d0e9db4534624b6be4c1fb',
  'simplesamlphp/simplesamlphp-module-authyubikey' => 'v0.9.1@8c27bfeb4981d2e6fa40a831e945f40c5a4ad3d2',
  'simplesamlphp/simplesamlphp-module-cas' => 'v0.9.1@63b72e4600550c507cdfc32fdd208ad59a64321e',
  'simplesamlphp/simplesamlphp-module-cdc' => 'v0.9.1@16a5bfac7299e04e5feb472af328e07598708166',
  'simplesamlphp/simplesamlphp-module-consent' => 'v0.9.6@2f84d15e96afb5a32b6d1cff93370f501ca7867d',
  'simplesamlphp/simplesamlphp-module-consentadmin' => 'v0.9.1@466e8d0d751f0080162d78e63ab2e125b24d17a1',
  'simplesamlphp/simplesamlphp-module-discopower' => 'v0.9.3@c892926e8186d0a2c638f7032dfc30540c1f92fb',
  'simplesamlphp/simplesamlphp-module-exampleattributeserver' => 'v1.0.0@63e0323e81c32bc3c9eaa01ea45194bb10153708',
  'simplesamlphp/simplesamlphp-module-expirycheck' => 'v0.9.3@59c59cdf87e2679257b46c07bb4c27666a11cc20',
  'simplesamlphp/simplesamlphp-module-ldap' => 'v0.9.10@78f04cbe41bfb9dcbcdeff4b5f12e67c060e1a77',
  'simplesamlphp/simplesamlphp-module-memcachemonitor' => 'v0.9.2@900b5c6b59913d9013b8dae090841a127ae55ae5',
  'simplesamlphp/simplesamlphp-module-memcookie' => 'v1.2.2@39535304e8d464b7baa1e82cb441fa432947ff57',
  'simplesamlphp/simplesamlphp-module-metarefresh' => 'v0.9.6@e284306a7097297765b5b78a4e28f19f18d4e001',
  'simplesamlphp/simplesamlphp-module-negotiate' => 'v0.9.10@db05ff40399c66e3f14697a8162da6b2fbdab47d',
  'simplesamlphp/simplesamlphp-module-oauth' => 'v0.9.2@d14d7aca6e699ec12b3f4dd0128373faa1a2cc61',
  'simplesamlphp/simplesamlphp-module-preprodwarning' => 'v0.9.2@8e032de33a75eb44857dc06d886ad94ee3af4638',
  'simplesamlphp/simplesamlphp-module-radius' => 'v0.9.3@36bd0f39f9a13f7eb96ead97c97c3634aa1c3f2d',
  'simplesamlphp/simplesamlphp-module-riak' => 'v0.9.1@c1a9d9545cb4e05b9205b34624850bb777aca991',
  'simplesamlphp/simplesamlphp-module-sanitycheck' => 'v0.9.1@15d6664eae73a233c3c4c72fd8a5c2be72b6ed2a',
  'simplesamlphp/simplesamlphp-module-smartattributes' => 'v0.9.1@b45d3ecd916e359a9cae05f9ae9df09b5c42f4e6',
  'simplesamlphp/simplesamlphp-module-sqlauth' => 'v0.9.3@c2dc4fc8aa6d8b2408131e09b39f06d8610ff374',
  'simplesamlphp/simplesamlphp-module-statistics' => 'v0.9.6@03fb6bdbbf5ce0a0cb257208db79aacac227ac10',
  'simplesamlphp/twig-configurable-i18n' => 'v2.3.4@e2bffc7eed3112a0b3870ef5b4da0fd74c7c4b8a',
  'symfony/config' => 'v4.4.22@f6d8318c14e4be81525ae47b30e618f0bed4c7b3',
  'symfony/debug' => 'v4.4.22@45b2136377cca5f10af858968d6079a482bca473',
  'symfony/dependency-injection' => 'v4.4.22@778b140b3e8f6890f43dc2c978e58e69f188909a',
  'symfony/deprecation-contracts' => 'v2.4.0@5f38c8804a9e97d23e0c8d63341088cd8a22d627',
  'symfony/error-handler' => 'v4.4.22@76603a8df8e001436df80758eb03a8baa5324175',
  'symfony/event-dispatcher' => 'v4.4.20@c352647244bd376bf7d31efbd5401f13f50dad0c',
  'symfony/event-dispatcher-contracts' => 'v1.1.9@84e23fdcd2517bf37aecbd16967e83f0caee25a7',
  'symfony/filesystem' => 'v5.3.0-BETA1@2a7311756f4ffa7ea39a4c31422c9d08013099d0',
  'symfony/http-client-contracts' => 'v2.4.0@7e82f6084d7cae521a75ef2cb5c9457bbda785f4',
  'symfony/http-foundation' => 'v4.4.22@1a6f87ef99d05b1bf5c865b4ef7992263e1cb081',
  'symfony/http-kernel' => 'v4.4.22@cd2e325fc34a4a5bbec91eecf69dda8ee8c5ea4f',
  'symfony/mime' => 'v5.3.0-BETA2@6b300fc7cabc779d5e2448faa1ea0397bf69a64a',
  'symfony/polyfill-ctype' => 'v1.22.1@c6c942b1ac76c82448322025e084cadc56048b4e',
  'symfony/polyfill-intl-idn' => 'v1.22.1@2d63434d922daf7da8dd863e7907e67ee3031483',
  'symfony/polyfill-intl-normalizer' => 'v1.22.1@43a0283138253ed1d48d352ab6d0bdb3f809f248',
  'symfony/polyfill-mbstring' => 'v1.22.1@5232de97ee3b75b0360528dae24e73db49566ab1',
  'symfony/polyfill-php72' => 'v1.22.1@cc6e6f9b39fe8075b3dabfbaf5b5f645ae1340c9',
  'symfony/polyfill-php73' => 'v1.22.1@a678b42e92f86eca04b7fa4c0f6f19d097fb69e2',
  'symfony/polyfill-php80' => 'v1.22.1@dc3063ba22c2a1fd2f45ed856374d79114998f91',
  'symfony/routing' => 'v4.4.22@049e7c5c41f98511959668791b4adc0898a821b3',
  'symfony/service-contracts' => 'v2.4.0@f040a30e04b57fbcc9c6cbcf4dbaa96bd318b9bb',
  'symfony/var-dumper' => 'v5.3.0-BETA2@ff77601582f1ffe003ca43f3860e096d0937a96a',
  'symfony/yaml' => 'v4.4.22@1c2fd24147961525eaefb65b11987cab75adab59',
  'twig/extensions' => 'v1.5.4@57873c8b0c1be51caa47df2cdb824490beb16202',
  'twig/twig' => 'v2.14.4@0b4ba691fb99ec7952d25deb36c0a83061b93bbf',
  'vlucas/phpdotenv' => 'v3.3.3@dbcc609971dd9b55f48b8008b553d79fd372ddde',
  'webmozart/assert' => '1.5.0@88e6d84706d09a236046d686bbea96f07b3a34f4',
  'whitehat101/apr1-md5' => 'v1.0.0@8b261c9fc0481b4e9fa9d01c6ca70867b5d5e819',
  'wpackagist-plugin/acf-better-search' => '3.8.0@trunk',
  'wpackagist-plugin/acf-image-aspect-ratio-crop' => '5.1.2@tags/5.1.2',
  'wpackagist-plugin/acf-to-rest-api' => '3.3.2@tags/3.3.2',
  'wpackagist-plugin/advanced-access-manager' => '6.7.4@tags/6.7.4',
  'wpackagist-plugin/akismet' => '4.1.9@tags/4.1.9',
  'wpackagist-plugin/anywhere-elementor' => '1.2.3@trunk',
  'wpackagist-plugin/better-font-awesome' => '2.0.1@tags/2.0.1',
  'wpackagist-plugin/capability-manager-enhanced' => '2.0@tags/2.0',
  'wpackagist-plugin/code-snippets' => '2.14.1@trunk',
  'wpackagist-plugin/coming-soon' => '6.2.1@tags/6.2.1',
  'wpackagist-plugin/custom-icons-for-elementor' => '0.3.1@tags/0.3.1',
  'wpackagist-plugin/custom-post-type-ui' => '1.8.2@tags/1.8.2',
  'wpackagist-plugin/easy-notification-bar' => '1.4.1@trunk',
  'wpackagist-plugin/elementor' => '3.2.2@tags/3.2.2',
  'wpackagist-plugin/filebird' => '4.7.2@trunk',
  'wpackagist-plugin/google-analytics-dashboard-for-wp' => '6.7.0@tags/6.7.0',
  'wpackagist-plugin/granular-controls-for-elementor' => '1.0.5@tags/1.0.5',
  'wpackagist-plugin/intuitive-custom-post-order' => '3.1.2@tags/3.1.2',
  'wpackagist-plugin/json-content-importer' => '1.3.12@tags/1.3.12',
  'wpackagist-plugin/kirki' => '3.1.6@tags/3.1.6',
  'wpackagist-plugin/pantheon-advanced-page-cache' => '1.0.0@tags/1.0.0',
  'wpackagist-plugin/redirection' => '5.1.1@tags/5.1.1',
  'wpackagist-plugin/relevanssi' => '4.13.0@tags/4.13.0',
  'wpackagist-plugin/simple-social-icons' => '3.0.2@tags/3.0.2',
  'wpackagist-plugin/siteimprove' => '1.2.1@tags/1.2.1',
  'wpackagist-plugin/wp-cfm' => '1.7.2@tags/1.7.2',
  'wpackagist-plugin/wp-mail-smtp' => '2.7.0@tags/2.7.0',
  'wpackagist-plugin/wp-native-php-sessions' => '1.2.3@tags/1.2.3',
  'wpackagist-plugin/wp-rss-aggregator' => '4.18.1@tags/4.18.1',
  'wpackagist-theme/twentynineteen' => '2.0@2.0',
  'yahnis-elsts/plugin-update-checker' => 'v4.11@3155f2d3f1ca5e7ed3f25b256f020e370515af43',
  'antecedent/patchwork' => '2.0.9@cab3be4865e47f1dc447715e76c7b616e48b005d',
  'behat/behat' => 'v3.8.1@fbb065457d523d9856d4b50775b4151a7598b510',
  'behat/gherkin' => 'v4.8.0@2391482cd003dfdc36b679b27e9f5326bd656acd',
  'behat/mink' => 'v1.8.1@07c6a9fe3fa98c2de074b25d9ed26c22904e3887',
  'behat/mink-browserkit-driver' => 'v1.3.4@e3b90840022ebcd544c7b394a3c9597ae242cbee',
  'behat/mink-extension' => '2.3.1@80f7849ba53867181b7e412df9210e12fba50177',
  'behat/mink-goutte-driver' => 'v1.2.1@8b9ad6d2d95bc70b840d15323365f52fcdaea6ca',
  'behat/mink-selenium2-driver' => 'v1.3.1@473a9f3ebe0c134ee1e623ce8a9c852832020288',
  'behat/transliterator' => 'v1.3.0@3c4ec1d77c3d05caa1f0bf8fb3aae4845005c7fc',
  'brain/monkey' => '1.5.0@44b2ea87147803227154c990fa01fd5e82a6bb61',
  'doctrine/instantiator' => '1.4.0@d56bf6102915de5702778fe20f2de3b2fe570b5b',
  'fabpot/goutte' => 'v3.3.1@80a23b64f44d54dd571d114c473d9d7e9ed84ca5',
  'hamcrest/hamcrest-php' => 'v1.2.2@b37020aa976fa52d3de9aa904aa2522dc518f79c',
  'instaclick/php-webdriver' => '1.4.7@b5f330e900e9b3edfc18024a5ec8c07136075712',
  'mockery/mockery' => '0.9.11@be9bf28d8e57d67883cba9fcadfcff8caab667f8',
  'myclabs/deep-copy' => '1.10.2@776f831124e9c62e1a2c601ecc52e776d8bb7220',
  'ocramius/package-versions' => '1.5.1@1d32342b8c1eb27353c8887c366147b4c2da673c',
  'ocramius/proxy-manager' => '2.0.4@a55d08229f4f614bf335759ed0cf63378feeb2e6',
  'paulgibbs/behat-wordpress-extension' => 'v0.8.0@04aaa4e2262f3678c4cf76829331b99051bf0ff9',
  'phar-io/manifest' => '1.0.1@2df402786ab5368a0169091f61a7c1e0eb6852d0',
  'phar-io/version' => '1.0.1@a70c0ced4be299a63d32fa96d9281d03e94041df',
  'phpdocumentor/reflection-common' => '2.2.0@1d01c49d4ed62f25aa84a747ad35d5a16924662b',
  'phpdocumentor/reflection-docblock' => '5.1.0@cd72d394ca794d3466a3b2fc09d5a6c1dc86b47e',
  'phpdocumentor/type-resolver' => '1.4.0@6a467b8989322d92aa1c8bf2bebcc6e5c2ba55c0',
  'phpspec/prophecy' => 'v1.10.3@451c3cd1418cf640de218914901e51b064abb093',
  'phpunit/php-code-coverage' => '5.3.2@c89677919c5dd6d3b3852f230a663118762218ac',
  'phpunit/php-file-iterator' => '1.4.5@730b01bc3e867237eaac355e06a36b85dd93a8b4',
  'phpunit/php-text-template' => '1.2.1@31f8b717e51d9a2afca6c9f046f5d69fc27c8686',
  'phpunit/php-timer' => '1.0.9@3dcf38ca72b158baf0bc245e9184d3fdffa9c46f',
  'phpunit/php-token-stream' => '2.0.2@791198a2c6254db10131eecfe8c06670700904db',
  'phpunit/phpunit' => '6.5.14@bac23fe7ff13dbdb461481f706f0e9fe746334b7',
  'phpunit/phpunit-mock-objects' => '5.0.10@cd1cf05c553ecfec36b170070573e540b67d3f1f',
  'roave/security-advisories' => 'dev-master@db47aac368cb81bc9d3b09556e63b716be61cf43',
  'sebastian/code-unit-reverse-lookup' => '1.0.2@1de8cd5c010cb153fcd68b8d0f64606f523f7619',
  'sebastian/comparator' => '2.1.3@34369daee48eafb2651bea869b4b15d75ccc35f9',
  'sebastian/diff' => '2.0.1@347c1d8b49c5c3ee30c7040ea6fc446790e6bddd',
  'sebastian/environment' => '3.1.0@cd0871b3975fb7fc44d11314fd1ee20925fce4f5',
  'sebastian/exporter' => '3.1.3@6b853149eab67d4da22291d36f5b0631c0fd856e',
  'sebastian/global-state' => '2.0.0@e8ba02eed7bbbb9e59e43dedd3dddeff4a56b0c4',
  'sebastian/object-enumerator' => '3.0.4@e67f6d32ebd0c749cf9d1dbd9f226c727043cdf2',
  'sebastian/object-reflector' => '1.1.2@9b8772b9cbd456ab45d4a598d2dd1a1bced6363d',
  'sebastian/recursion-context' => '3.0.1@367dcba38d6e1977be014dc4b22f47a484dac7fb',
  'sebastian/resource-operations' => '1.0.0@ce990bb21759f94aeafd30209e8cfcdfa8bc3f52',
  'sebastian/version' => '2.0.1@99732be0ddb3361e16ad77b68ba41efc8e979019',
  'sensiolabs/behat-page-object-extension' => 'v2.1.0@bd2a34221ba65ea8c86d8e693992d718de03dbae',
  'squizlabs/php_codesniffer' => '3.4.2@b8a7362af1cc1aadb5bd36c3defc4dda2cf5f0a8',
  'symfony/browser-kit' => 'v4.4.22@4c8b42b4aae93517e8f67d68c5cbe69413e3e3c1',
  'symfony/console' => 'v4.4.22@36bbd079b69b94bcc9c9c9e1e37ca3b1e7971625',
  'symfony/css-selector' => 'v5.3.0-BETA1@59a684f5ac454f066ecbe6daecce6719aed283fb',
  'symfony/dom-crawler' => 'v4.4.20@be133557f1b0e6672367325b508e65da5513a311',
  'symfony/translation' => 'v4.4.21@eb8f5428cc3b40d6dffe303b195b084f1c5fbd14',
  'symfony/translation-contracts' => 'v2.4.0@95c812666f3e91db75385749fe219c5e494c7f95',
  'theseer/tokenizer' => '1.2.0@75a63c33a8577608444246075ea0af0d052e452a',
  'wp-coding-standards/wpcs' => 'dev-master@7da1894633f168fe244afc6de00d141f27517b62',
  'zendframework/zend-code' => '3.1.0@2899c17f83a7207f2d7f53ec2f421204d3beea27',
  'zendframework/zend-eventmanager' => '3.2.1@a5e2583a211f73604691586b8406ff7296a946dd',
  'pantheon-systems/example-wordpress-composer' => 'dev-24202c12ba5b28bb61efa83fde982dc92c5257c7@24202c12ba5b28bb61efa83fde982dc92c5257c7',
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
