var gulp = require('gulp');
var minijs = require('gulp-minify');
var cleancss = require('gulp-clean-css');

gulp.task('default', function() {
	  console.log("Ne fait rien");
	});



gulp.task('compress', function(){
	
	gulp.src('css/manage-svg.css')
	.pipe(cleancss())
	.pipe(gulp.dest('dist'));
	gulp.src('js/manage-svg.js')
	.pipe(minijs())
	.pipe(gulp.dest('dist'));
})

