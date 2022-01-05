const gulp = require('gulp');
const browserify = require('browserify');
const environments = require('gulp-environments');
const babelify = require('babelify');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const sass = require('gulp-sass');
const sassGlob = require('gulp-sass-glob');
// const eslint = require('gulp-eslint');
// const eslintify = require('eslintify');
const gulpStylelint = require('gulp-stylelint');
const cleanCSS = require('gulp-clean-css');
// const fontAwesome = require('node-font-awesome');
const autoprefixer = require('gulp-autoprefixer');
// const source = require('vinyl-source-stream');
const buffer = require('vinyl-buffer');
// const livereload = require('gulp-livereload');
// const plumber = require('gulp-plumber');
// const plumberNotifier = require('gulp-plumber-notifier');
const uglify = require('gulp-uglify');
const imagemin = require('gulp-imagemin');
const tap = require('gulp-tap');
// const exec = require('gulp-exec');
const merge = require('merge-stream');
const clean = require('gulp-clean');
const drupalBreakpoints = require('drupal-breakpoints-scss');
var log = require('fancy-log');

var development = environments.development;
var production = environments.production;

var SassPath = [
  './scss/**/*.+(scss|sass)'
];

gulp.task('scss', ['breakpoints-scss'], function () {
  log('Compiling scss on ' + environments.current().$nuage + ' env.');

  return gulp.src(SassPath)
    .pipe(development(sourcemaps.init()))
    // .pipe(gulpStylelint({
    //   fix: true,
    //   failAfterError: production(),
    //   reporters: [
    //     {formatter: 'string', console: true}
    //   ]
    // }))
    .pipe(sassGlob())
    .pipe(sass(
      {
        includePaths: [
          // 'node_modules/font-awesome/scss/',
          'node_modules/susy/sass'
        ],
        style: development ? 'expanded' : 'compressed'
      }
    ).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions'],
      cascade: false
    }))
    .pipe(development(sourcemaps.write()))
    .pipe(production(cleanCSS()))
    .pipe(gulp.dest('./assets/css'))
});

gulp.task('scripts', function () {
  return gulp.src('js/**/*.js', {read: false})
    .pipe(tap(function (file) {
      log('bundling ' + file.path);
      var b = browserify(file.path, {})
        .transform('browserify-shim')
        .transform(babelify, {
          'presets': ['env'],
          sourceMaps: development
        });
      file.contents = b.bundle()
    }))
    .pipe(buffer())
    .pipe(development(sourcemaps.init({loadMaps: true})))
    .pipe(production(uglify()))
    .pipe(development(sourcemaps.write()))
    .pipe(gulp.dest('assets/js/'))
});

gulp.task('fonts', function () {
  return merge(
      gulp.src('fonts/**/*').pipe(gulp.dest('./assets/fonts'))
    // gulp.src(fontAwesome.fonts).pipe(gulp.dest('./assets/fonts/FontAwesome')),
    // gulp.src('fonts/CenturyGothic/*.+(woff|woff2|ttf|eot|svg)').pipe(gulp.dest('assets/fonts/CenturyGothic'))
  )
});

gulp.task('breakpoints-scss', function () {
    return gulp.src('./uag.breakpoints.yml')
        .pipe(drupalBreakpoints.ymlToScss({
            vars: true,
            map: true,
            varsPrefix: 'bp_',
            mapNuage: 'breakpoints'
        }))
        .pipe(rename('_breakpoints.scss'))
        .pipe(gulp.dest('./scss'));
});

gulp.task('images', function () {
  // gulp.src('node_modules/slick-carousel/slick/ajax-loader.gif')
  //   .pipe(gulp.dest('./web/assets/images/slick'));
  gulp.src('./images/**/*')
    .pipe(imagemin())
    .pipe(gulp.dest('./assets/images'))
});

gulp.task('libs', function () {
  return merge(
      gulp.src('node_modules/jquery.animate-number/**/*').pipe(gulp.dest('./assets/libs/jquery.animate-number')),
      gulp.src('node_modules/bootstrap-select/**/*').pipe(gulp.dest('./assets/libs/bootstrap-select')),
      gulp.src('node_modules/jquery-viewport-checker/**/*').pipe(gulp.dest('./assets/libs/jquery-viewport-checker')),
      gulp.src('node_modules/huagmerjs/**/*').pipe(gulp.dest('./assets/libs/huagmerjs')),
      gulp.src('node_modules/overlayscrollbars/**/*').pipe(gulp.dest('./assets/libs/overlayscrollbars'))
  );
});

gulp.task('watch', ['libs', 'scss', 'scripts', 'fonts', 'images'], function () {
  gulp.watch(SassPath, ['scss']);
  gulp.watch('./js/**/*.js', ['scripts']);
  gulp.watch('./media/**/*', ['images']);
});

gulp.task('eslint', function () {
  return (gulp.src('js/index.js'))
});

gulp.task('clean' , function () {
  return gulp.src('./assets', {read: false})
    .pipe(clean())
});

gulp.task('set-prod', production.task);
gulp.task('build', ['set-prod', 'libs', 'scss', 'scripts', 'fonts', 'images']);

gulp.task('default', ['watch']);
