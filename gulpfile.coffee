gulp = require('gulp')

gulp.task 'build', () ->
  gulp
    .src [
      'bower_components/jquery/dist/jquery.js',
    ]
    .pipe gulp.dest 'public/vendor/jquery/'