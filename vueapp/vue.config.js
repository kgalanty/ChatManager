module.exports = {
  pages: {
    index: {
      entry: 'src/main.js',
      title: 'Chat Manager'
    }
  },
  publicPath: ".",
  assetsDir: process.env.NODE_ENV === 'production' ? '../modules/addons/ChatManager/lib/app/Views/' : '',
  devServer: {
    // open: process.platform === 'darwin',
    // host: '0.0.0.0',
    // port: 8080, // CHANGE YOUR PORT HERE!
     https: true,
    // hotOnly: false,
    //public: 'https://localhost:8080/',
    public: 'https://localhost:8080/',
  proxy: {
    "addonmodules.php": {
      //target: "https://ticketing.stage.tmdhosting.com/admin/",
      target: "https://my.tmdhosting.com/admin/",
      logLevel: "debug",
     // changeOrigin: true,
      secure: false,
      withCredentials: true,
      cookieDomainRewrite: {"localhost": "my.tmdhosting.com" },
      headers: { Cookie: 'WHMCSBaCqM4Y33YVw=93595795d68e2a076bb864a970e90c55'},
    }
  }
}
} 