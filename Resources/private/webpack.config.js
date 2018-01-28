var Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('../public/build')
  .setPublicPath('/bundles/scribercore/build')

  .enableSourceMaps(!Encore.isProduction())
  .cleanupOutputBeforeBuild()
  .setManifestKeyPrefix('scribercore')
  .enableVersioning()

  .enableSassLoader()
  .enableVueLoader()

  .addEntry('myaccount', './js/my-account/main.js');

const config = Encore.getWebpackConfig();

config.externals = {
  axios: 'axios',
  $Scriber: '$Scriber',
  vue: 'Vue'
};

module.exports = config;
