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
// sass compiler
var sass = require('gulp-sass');

var cleanCss = require('gulp-clean-css');

var cmq = require('gulp-merge-media-queries');
// add vender prifix
var autoprefixer = require('gulp-autoprefixer');

// var path = require('path');
// var fs = require('fs');
// var pkg = JSON.parse(fs.readFileSync('./package.json'));
// var assetsPath = path.resolve(pkg.path.assetsDir);
var cleanCss = require('gulp-clean-css');

// sudo npm install gulp.spritesmith --save-dev
// var spritesmith = require('gulp.spritesmith');
// http://blog.e-riverstyle.com/2014/02/gulpspritesmithcss-spritegulp.html

// 同期的に処理してくれる（ distで使用している ）
var runSequence = require('run-sequence');

gulp.task('sass', function() {
    gulp.src(['_scss/*.scss'])
        .pipe(plumber())
        .pipe(sass())
				.pipe(cmq({log:true}))
        .pipe(autoprefixer())
				.pipe(cleanCss())
        .pipe(gulp.dest('./css/'));
});

// ファイル結合
gulp.task('scripts', function() {
  return gulp.src(['./js/jquery.flatheights.js','./js/master.js','./plugins/pagetop-btn/js/pagetop-btn.js'])
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
    gulp.watch('plugins/pagetop-btn/js/pagetop-btn.js', ['scripts']);
    gulp.watch('js/all.js', ['jsmin']);
    gulp.watch('_scss/**/*.scss', ['sass']);
});

// gulp.task('default', ['scripts','watch','sprite']);
gulp.task('default', ['scripts','watch']);

gulp.task('compile', ['scripts','jsmin','sass']);

// copy dist ////////////////////////////////////////////////

gulp.task('copy_dist', function() {
    return gulp.src(
            [
							'./**/*.php',
							'./**/*.txt',
							'./**/*.css',
							'./**/*.scss',
							'./**/*.bat',
							'./**/*.rb',
							'./**/*.eot',
							'./**/*.svg',
							'./**/*.ttf',
							'./**/*.woff',
							'./**/*.woff2',
							'./**/*.otf',
							'./**/*.png',
							'./images/**',
							'./inc/**',
							'./js/**',
							'./plugins/**',
							'./plugins_admin/**',
							'./languages/**',
							'./libraries/**',
							"!./compile.bat",
							"!./config.rb",
							"!./tests/**",
							"!./dist/**",
							"!./node_modules/**"
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
