var gulp = require('gulp');

var cssmin = require('gulp-cssmin');
// ファイルリネーム（.min作成用）
var rename = require('gulp-rename');
// ファイル結合
var concat = require('gulp-concat');
// js最小化
var jsmin = require('gulp-jsmin');
// エラーでも監視を続行させる
var plumber = require('gulp-plumber');
// sudo npm install gulp.spritesmith --save-dev
// var spritesmith = require('gulp.spritesmith');
// http://blog.e-riverstyle.com/2014/02/gulpspritesmithcss-spritegulp.html

// ファイル結合
gulp.task('scripts', function() {
  return gulp.src(['./js/jquery.flatheights.js','./js/master.js'])
    .pipe(concat('all.js'))
    .pipe(gulp.dest('./js/'));
});

// js最小化
gulp.task('jsmin', function () {
  gulp.src(['./js/all.js'])
  .pipe(plumber()) // エラーでも監視を続行
  .pipe(jsmin())
  .pipe(rename({suffix: '.min'}))
  .pipe(gulp.dest('./js'));
});

// Watch
gulp.task('watch', function() {
    gulp.watch('js/master.js', ['scripts']);
    gulp.watch('js/all.js', ['jsmin']);
    gulp.watch('_scss/style.scss', ['copy']);
});

// gulp.task('default', ['scripts','watch','sprite']);
gulp.task('default', ['scripts','watch']);
gulp.task('compile', ['scripts','jsmin']);
