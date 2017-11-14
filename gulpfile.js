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
// エラーでも監視を続行させる
var plumber = require('gulp-plumber');
// 同期的に処理してくれる
var runSequence = require('run-sequence');
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

// copy dist ////////////////////////////////////////////////

gulp.task('copy_dist', function() {
    return gulp.src(
            [
                './**/*.php',
                './**/*.txt',
                './**/*.css',
                './**/*.png',
                './images/**',
                './inc/**',
                './js/**',
                './languages/**',
                './library/**',
                "!./tests/**",
                "!./dist/**",
                "!./node_modules/**/*.*"
            ],
            { base: './' }
        )
        .pipe( gulp.dest( 'dist' ) ); // distディレクトリに出力
} );
// gulp.task('build:dist',function(){
//     /* ここで、CSS とか JS をコンパイルする */
// });

gulp.task('dist', function(cb){
    // return runSequence( 'build:dist', 'copy', cb );
    // return runSequence( 'build:dist', 'copy_dist', cb );
    return runSequence( 'copy_dist', cb );
});
