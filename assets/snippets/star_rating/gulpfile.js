var gulp = require('gulp');
var minify = require('gulp-minify-css');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');

var paths = {
  dev: {
    css: 'assets/resources/css/',
    js: 'assets/resources/js/'
  },
  prod: {
    css: 'assets/css/',
    js: 'assets/js/'
  }
};

// CSS
gulp.task('css', function () {
  return gulp
    .src([paths.dev.css + '*.css'])
    .pipe(concat('styles.min.css'))
    .pipe(gulp.dest(paths.prod.css))
    .pipe(minify({ keepSpecialComments: 0 }))
    .pipe(gulp.dest(paths.prod.css));
});

// JS
gulp.task('js', function () {
  return gulp
    .src([paths.dev.js + '*.js'])
    .pipe(concat('scripts.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest(paths.prod.js));
});

gulp.task('watch', function () {
  gulp.watch(paths.dev.css + '/*.css', ['css']);
  gulp.watch(paths.dev.js + '/*.js', ['js']);
});

gulp.task('default', ['css', 'js', 'watch']);
