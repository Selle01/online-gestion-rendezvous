require('dotenv').config();
const path = require("path");
const webpack = require('webpack');


module.exports = {
    watch: true,
    mode: 'development',
    entry: ["@babel/polyfill", "./assets/js/app.js"],

    output: {
        path: path.resolve(__dirname, "public/build/"),
        filename: "./bundle.js"
    },
    devServer: {
        contentBase: path.resolve(__dirname, "public/build/"),
        open: true,   // ===   "start":"webpack-dev-server --open",
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@babel/preset-env"]
                    }
                }
            },
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.(png|jpg|gif)$/,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            limit: 8192
                        }
                    }
                ]
            },
            {
                test: /\.(png|jpe?g|gif)$/i,
                use: [
                    {
                        loader: 'file-loader',
                    },
                ],
            }
        ]
    },
    plugins: [
        new webpack.DefinePlugin(options => {
            options['process.env'].API_URL = process.env.URL;
        })
    ],
}
