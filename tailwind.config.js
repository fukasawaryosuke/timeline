/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue"
  ],
  theme: {
    extend: {
      spacing: {
        '100': '25rem',
        '104': '26rem',
        '120': '30rem',
        '128': '32rem',
        '132': '33rem',
        '160': '40rem',
        '172': '43rem',
        '176': '44rem',
        '192': '48rem',
      },
      colors: {
        // 'カラー名': 'カラーコード'
        'timeline': 'rgb(230,253,252)',
        'assist-grad-from': 'rgb(11,214,155)',
        'assist-grad-to': 'rgb(11,179,214)',
      },
    },
  }
}
