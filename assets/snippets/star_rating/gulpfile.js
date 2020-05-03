const { parallel, src, dest, series, watch } = require('gulp');
const cssMinify = require('gulp-clean-css');
const concat = require('gulp-concat');
const uglify = require('gulp-uglify');
const rename = require('gulp-rename');

function css() {
  return src('assets/resources/css/*.css')
    .pipe(concat('styles.css'))
    .pipe(cssMinify({ specialComments: false }))
    .pipe(rename({ extname: '.min.css' }))
    .pipe(dest('assets/css/'));
}

function js() {
  return src('assets/resources/js/*.js')
    .pipe(concat('scripts.js'))
    .pipe(uglify())
    .pipe(rename({ extname: '.min.js' }))
    .pipe(dest('assets/js/'));
}

function watchChanges() {
  watch('assets/resources/css/*.css', css);
  watch('assets/resources/js/*.js', js);
}

module.exports = {
  js,
  css,
  watch: series(parallel(js, css), watchChanges),
  default: parallel(js, css),
};
