var gulp = require('gulp')
var cssmin = require('gulp-cssmin')
// ファイルリネーム（.min作成用）
var rename = require('gulp-rename')
// ファイル結合
var concat = require('gulp-concat')
// js最小化
var jsmin = require('gulp-jsmin')
// エラーでも監視を続行させる
var plumber = require('gulp-plumber')
// sass compiler
var sass = require('gulp-sass')
var cleanCss = require('gulp-clean-css')
var cmq = require('gulp-merge-media-queries')
// add vender prifix
var autoprefixer = require('gulp-autoprefixer')
var cleanCss = require('gulp-clean-css')
// 同期的に処理してくれる（ distで使用している ）
var runSequence = require('run-sequence')
var replace = require('gulp-replace')


gulp.task('text-domain', (done) => {
    gulp.src(['./inc/font-awesome/**/*'])
        .pipe(replace('vk_font_awesome_version_textdomain', 'vk-all-in-one-expansion-unit' ))
        .pipe(gulp.dest('./inc/font-awesome/'))
    done()
})

gulp.task('sass', (done) => {
    gulp.src('./assets/_scss/*.scss',{ base: './assets/_scss' })
        .pipe(plumber())
        .pipe(sass())
        .pipe(cmq({log:true}))
        .pipe(autoprefixer())
        .pipe(cleanCss())
        .pipe(gulp.dest('./assets/css/'))
    done()
})

// ファイル結合
gulp.task('scripts', (done) => {
    gulp.src(
        [
            './assets/js/jquery.flatheights.js',
            './assets/js/master.js',
            './inc/pagetop-btn/js/pagetop-btn.js'
        ]
    )
        .pipe(concat('all.js'))
        .pipe(gulp.dest('./assets/js/'))
        .pipe(jsmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./assets/js'))
    done()
})

// js最小化
gulp.task('jsmin_scroll', (done) => {
    gulp.src(['./inc/smooth-scroll/js/smooth-scroll.js'])
        .pipe(plumber()) // エラーでも監視を続行
        .pipe(jsmin())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./inc/smooth-scroll/js'))
    done()
})

// Watch
gulp.task('watch', (done) => {
    gulp.watch(
        [
            './assets/js/jquery.flatheights.js',
            './assets/js/master.js',
            './inc/pagetop-btn/js/pagetop-btn.js'
        ],
        gulp.series('scripts')
    )
    gulp.watch(['./inc/smooth-scroll/js/smooth-scroll.js'], gulp.series('jsmin_scroll'))
    gulp.watch(['./assets/_scss/**/*.scss', './inc/pagetop-btn/assets/_scss/**/*.scss'], gulp.series('sass'))
    done()
})

// gulp.task('default', ['scripts','watch','sprite'])
gulp.task('default', gulp.series('text-domain','watch'))
gulp.task('compile', gulp.series('scripts','text-domain','scripts', 'jsmin_scroll', 'sass'))

// copy dist ////////////////////////////////////////////////

gulp.task('copy_dist', (done) => {
    gulp.src(
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
        {
            base: './'
        }
    )
        .pipe(gulp.dest('dist'))
    done()
})

gulp.task('dist', gulp.series('copy_dist'))
