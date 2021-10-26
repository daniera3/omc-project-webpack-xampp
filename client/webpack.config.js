const path = require('path')
const webpack = require('webpack')
const MiniCssExtractPlugin = require('mini-css-extract-plugin');


module.exports = env => {
    return {
        plugins: [
            new MiniCssExtractPlugin({
                filename: '[name].bundle.css',
                chunkFilename: '[id].css'
            }),
            new webpack.HotModuleReplacementPlugin({
                template: path.resolve(__dirname, "src", "index.php")
            })
        ],
        entry: [
            path.resolve(__dirname, 'src', 'index.js'),

        ],
        output: {
            path: path.resolve(__dirname, '..', 'html'),
            filename: 'bundle.js',
        },
        module: {
            rules: [

                {
                    test: /\.(jsx|js)$/,
                    include: path.resolve(__dirname, 'src'),
                    exclude: /node_modules/,
                    use: [{
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                '@babel/preset-env',
                                '@babel/preset-react'
                            ]

                        }
                    }]
                },
                {
                    loader: 'eslint-loader',
                    options: {
                        fix: true
                    }
                }
            ]
        }
    }
}