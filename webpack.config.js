const path = require('path')
const WebpackAssetsManifest = require('webpack-assets-manifest')
const CleanWebpackPlugin = require('clean-webpack-plugin')

module.exports = {
  entry: {
    common: './assets/Common.js',
    map: './assets/Map.js',
    qr: './assets/QR.js',
    navlog: './assets/Navlog.js'
  },
  output: {
    path: path.join(path.resolve(), 'www/dist'),
    filename: '[name].[contenthash:8].js',
    publicPath: '/dist/'
  },
  optimization: {
    runtimeChunk: true,
    splitChunks: {
      chunks: 'all',
      maxInitialRequests: Infinity,
      minSize: 0,
      cacheGroups: {
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name(module) {
            const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1] // název knihovny
            return `npm.${packageName.replace('@', '')}`
          }
        }
      }
    }
  },
  module: {
    rules: [
      {
        test: /\.js?$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
        options: {
          'presets': [
            [
              '@babel/preset-env', {
                'targets': {
                  'browsers': ['>0.25%', 'not ie 11', 'not op_mini all']
                }
              }
            ]
          ]
        }
      },
      {
        rules: [{
          test: /\.scss$/,
          use: [
            'style-loader',
            'css-loader',
            'sass-loader'
          ]
        }]
      },
      {
        test: /\.(jpe?g|png|gif|webp|eot|ttf|woff|woff2|svg|)$/i,
        use: [{
          loader: 'url-loader',
          options: {
            limit: 1000,
            name: 'images/[name].[hash].[ext]'
          }
        }]
      }
    ]
  },
  resolve: {
    alias: {
      'DOMLoaded': path.resolve(__dirname, 'app/assets/js/DOMLoaded.js')
    }
  },
  plugins: [
    new WebpackAssetsManifest({
      entrypoints: true
    }),
    new CleanWebpackPlugin()
  ]
}
