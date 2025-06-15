const webpack = require('webpack');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const config = {
  entry: {
    admin: [
      './assets/source/js/admin/above-the-fold-audit-admin.js',
      './assets/source/sass/admin/above-the-fold-audit-admin.scss'
    ],
    front: [
      './assets/source/js/front/above-the-fold-audit.js',
      './assets/source/sass/front/above-the-fold-audit.scss'
    ]
  },
  output: {
    path: path.resolve(__dirname, 'assets'),
    filename: '[name].js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: 'babel-loader',
        exclude: /node_modules/
      },
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'sass-loader'
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin()
  ]
};

module.exports = config;
