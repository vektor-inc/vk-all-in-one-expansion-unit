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

var replace = require('gulp-replace');

gulp.task('text-domain', function () {
		gulp.src(['./inc/font-awesome/**/*'])
				.pipe(replace('vk_font_awesome_version_textdomain', 'vk-all-in-one-expansion-unit' ))
				.pipe(gulp.dest('./inc/font-awesome/'));
});

gulp.task('sass', function() {
    gulp.src('./assets/_scss/*.scss',{ base: './assets/_scss' })
        .pipe(plumber())
        .pipe(sass())
				.pipe(cmq({log:true}))
        .pipe(autoprefixer())
				.pipe(cleanCss())
        .pipe(gulp.dest('./assets/css/'));
});

// ファイル結合
gulp.task('scripts', function() {
  return gulp.src(['./assets/js/jquery.flatheights.js','./assets/js/master.js','./inc/pagetop-btn/js/pagetop-btn.js'])
    .pipe(concat('all.js'))
    .pipe(gulp.dest('./assets/js/'))
    .pipe(jsmin())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('./assets/js'));
});

// js最小化
gulp.task('jsmin_scroll', function () {
	gulp.src(['./inc/smooth-scroll/js/smooth-scroll.js'])
  .pipe(plumber()) // エラーでも監視を続行
  .pipe(jsmin())
  .pipe(rename({suffix: '.min'}))
  .pipe(gulp.dest('./inc/smooth-scroll/js'));
});

// Watch
gulp.task('watch', function() {
    gulp.watch(['./assets/js/jquery.flatheights.js','./assets/js/master.js','./inc/pagetop-btn/js/pagetop-btn.js'], ['scripts']);
    gulp.watch(['./inc/smooth-scroll/js/smooth-scroll.js'], ['jsmin_scroll']);
    gulp.watch('./assets/_scss/**/*.scss', ['sass']);
    gulp.watch('./inc/pagetop-btn/assets/_scss/*.scss', ['sass']);
});

// gulp.task('default', ['scripts','watch','sprite']);
gulp.task('default', ['text-domain','watch']);

gulp.task('compile', ['scripts','text-domain','jsmin','sass']);

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
							'./**/*.less',
							'./**/*.png',
							'./images/**',
							'./inc/**',
							'./assets/**',
							'./admin/**',
							'./languages/**',
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
