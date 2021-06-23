import { src, dest, watch, series, parallel } from 'gulp';
import yargs from 'yargs';
import sass from 'gulp-dart-sass';
import gulpif from 'gulp-if';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'autoprefixer';
import del from 'del';
import webpack from 'webpack-stream';
import named from 'vinyl-named';
const PRODUCTION = yargs.argv.prod;
export const clean = () => del(['dist']);
  
export const styles = () => {
return src(['src/scss/wp-everything-image.scss'])
  .pipe(gulpif(!PRODUCTION, sourcemaps.init()))
  .pipe(sass().on('error', sass.logError))
  .pipe(gulpif(PRODUCTION, postcss([ autoprefixer ])))
  .pipe(gulpif(!PRODUCTION, sourcemaps.write()))
  .pipe(dest('dist/css'));
}

export const copy = () => {
  return src(['src/**/*','!src/{images,js,scss}','!src/{images,js,scss}/**/*'])
  .pipe(dest('dist'));
}
export const scripts = () => {
  return src(['src/js/wp-everything-image.js'])
  .pipe(named())
  .pipe(webpack({
    module: {
    rules: [
      {
        test: /\.js$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: []
            }
          }
        }
      ]
    },
    mode: PRODUCTION ? 'production' : 'development',
    devtool: !PRODUCTION ? 'inline-source-map' : false,
    output: {
      filename: '[name].js'
    },
    externals: {
      jquery: 'jQuery'
    },
  }))
  .pipe(dest('dist/js'));
}
export const watchForChanges = () => {
  watch('src/scss/**/*.scss', styles);
  watch('src/js/**/*.js', series(scripts));
} 
export const dev = series(clean, parallel(styles, copy, scripts), watchForChanges);
export const build = series(clean, parallel(styles, copy, scripts));
export default dev;