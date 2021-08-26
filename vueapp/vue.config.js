module.exports = {
  pages: {
    index: {
      entry: 'src/main.js',
      title: 'Chat Manager'
    }
  },
  publicPath: ".",
  assetsDir: process.env.NODE_ENV === 'production' ? '../modules/addons/ChatManager/lib/app/Views/' : ''
} 