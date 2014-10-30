/*jslint node: true */

'use strict';

var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: './main.js',
  output: {
    path: __dirname,
    filename: "bundle.js"
  },
  resolve: {
    root: [path.join(__dirname, '../../bower_components')]
  },
  plugins: [
    // new webpack.ResolverPlugin(
    //   new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin('bower.json', ['main'])
    // )
    new webpack.ProvidePlugin({
      $: 'jquery',
      jquery: 'jquery',
      'windows.jQuery': 'jquery'
    })
  ]
};
